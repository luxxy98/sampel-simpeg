<?php

namespace App\Http\Requests\Absensi;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator as ContractValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

final class AbsensiRequest extends FormRequest
{
    /** toleransi selisih durasi vs selisih waktu (dalam jam). 0.05 jam ≈ 3 menit */
    private const DURATION_TOLERANCE_HOURS = 0.05;

    /** batas aman durasi 1 baris detail (dalam jam) - jam operasional + lembur = 18 jam */
    private const MAX_DETAIL_HOURS = 18;

    /** jam operasional perusahaan */
    private const WORKING_HOUR_START = 7;  // 07:00
    private const WORKING_HOUR_END = 23;   // 23:00
    
    /** batas maksimal lembur (01:00 = jam 1 di hari berikutnya) */
    private const OVERTIME_HOUR_END = 1;   // 01:00 (next day)

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Auto-isi durasi_jam jika user tidak mengisi, tapi waktu_mulai & waktu_selesai ada.
     * Ini membuat UX lebih enak, sekaligus meminimalkan data salah.
     */
    protected function prepareForValidation(): void
    {
        $detail = $this->input('detail');
        if (!is_array($detail)) return;

        $tanggalHeader = $this->input('tanggal');
        if (!$tanggalHeader) return;

        try {
            $headerDate = Carbon::parse($tanggalHeader)->startOfDay();
        } catch (\Throwable) {
            return;
        }

        $mulaiArr   = $detail['waktu_mulai'] ?? null;
        $selesaiArr = $detail['waktu_selesai'] ?? null;
        $durasiArr  = $detail['durasi_jam'] ?? null;

        if (!is_array($mulaiArr) || !is_array($selesaiArr)) return;
        if (!is_array($durasiArr)) $durasiArr = [];

        $n = max(count($mulaiArr), count($selesaiArr), count($durasiArr));

        for ($i = 0; $i < $n; $i++) {
            $mulai  = trim($mulaiArr[$i] ?? '');
            $selesai = trim($selesaiArr[$i] ?? '');

            if (!$mulai || !$selesai) continue;

            try {
                // Cek apakah input hanya waktu (HH:mm atau HH:mm:ss) atau sudah datetime lengkap
                $isTimeOnlyMulai = preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $mulai);
                $isTimeOnlySelesai = preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $selesai);

                if ($isTimeOnlyMulai) {
                    // Gabungkan tanggal header dengan waktu input
                    $start = Carbon::parse($headerDate->toDateString() . ' ' . $mulai);
                } else {
                    $start = Carbon::parse($mulai);
                }

                if ($isTimeOnlySelesai) {
                    // Gabungkan tanggal header dengan waktu input
                    $end = Carbon::parse($headerDate->toDateString() . ' ' . $selesai);
                } else {
                    $end = Carbon::parse($selesai);
                }
                
                // Auto-koreksi tanggal waktu selesai untuk lembur dini hari / shift malam
                // Jika waktu selesai <= waktu mulai, maka user pasti maksud hari berikutnya
                $startHour = (int) $start->format('H');
                $endHour = (int) $end->format('H');
                
                // Jika end <= start (misalnya mulai 22:00, selesai 06:00), tambahkan 1 hari ke end
                if ($end->lessThanOrEqualTo($start)) {
                    $end = $end->addDay();
                }
                // Atau jika jam mulai siang/malam dan selesai dini hari
                elseif ($startHour >= 12 && $endHour <= self::OVERTIME_HOUR_END && $end->toDateString() === $start->toDateString()) {
                    $end = $end->addDay();
                }
                
                // Update arrays with normalized format
                $mulaiArr[$i] = $start->format('Y-m-d H:i:s');
                $selesaiArr[$i] = $end->format('Y-m-d H:i:s');
            } catch (\Throwable) {
                continue;
            }

            // kalau end < start, biarkan validasi after yang menolak
            $diffSeconds = $start->diffInSeconds($end, false);
            if ($diffSeconds < 0) continue;

            // Always calculate duration
            $diffHours = $diffSeconds / 3600;
            $durasiArr[$i] = number_format($diffHours, 2, '.', '');
        }

        $detail['waktu_mulai'] = $mulaiArr;
        $detail['waktu_selesai'] = $selesaiArr;
        $detail['durasi_jam'] = $durasiArr;
        $this->merge(['detail' => $detail]);
    }


    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'id_jadwal_karyawan' => ['required', 'integer', 'exists:sdm_jadwal_karyawan,id_jadwal_karyawan'],
            'id_sdm' => ['required', 'integer', 'exists:person_sdm,id_sdm'],

            'total_jam_kerja' => ['nullable', 'numeric', 'min:0'],
            'total_terlambat' => ['nullable', 'numeric', 'min:0'],
            'total_pulang_awal' => ['nullable', 'numeric', 'min:0'],
            'total_lembur' => ['nullable', 'numeric', 'min:0'],

            // detail masih boleh nullable agar request lama tidak langsung pecah,
            // tapi akan kita paksa minimal 1 baris via withValidator().
            'detail' => ['nullable', 'array'],

            'detail.id_jenis_absen' => ['nullable', 'array'],
            'detail.id_jenis_absen.*' => ['nullable', 'integer', 'exists:absen_jenis,id_jenis_absen'],

            'detail.waktu_mulai' => ['nullable', 'array'],
            'detail.waktu_mulai.*' => ['nullable', 'date'],

            'detail.waktu_selesai' => ['nullable', 'array'],
            'detail.waktu_selesai.*' => ['nullable', 'date'],

            'detail.durasi_jam' => ['nullable', 'array'],
            'detail.durasi_jam.*' => ['nullable', 'numeric', 'min:0'],

            'detail.lokasi_pulang' => ['nullable', 'array'],
            'detail.lokasi_pulang.*' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Validasi tambahan agar aman saat input:
     * - minimal 1 detail
     * - waktu mulai <= waktu selesai
     * - tanggal detail sesuai tanggal header (end boleh +1 hari untuk shift malam)
     * - durasi harus sesuai selisih waktu (dengan toleransi)
     * - tidak boleh overlap antar baris detail
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $tanggalHeader = $this->input('tanggal');
            $idSdm = $this->input('id_sdm');

            if (!$tanggalHeader) return;

            try {
                $header = Carbon::parse($tanggalHeader)->startOfDay();
            } catch (\Throwable) {
                return;
            }

            // Cek duplikat: tidak boleh ada absensi untuk SDM yang sama di tanggal yang sama
            if ($idSdm) {
                $existingId = DB::connection('mysql')->table('absensi')
                    ->where('id_sdm', $idSdm)
                    ->whereDate('tanggal', $header->toDateString())
                    ->when($this->route('id'), fn($q, $id) => $q->where('id_absensi', '!=', (int) $id))
                    ->value('id_absensi');

                if ($existingId) {
                    $validator->errors()->add('tanggal', 'Sudah ada data absensi untuk SDM ini pada tanggal tersebut.');
                    return;
                }
            }

            // Cek apakah SDM memiliki cuti disetujui pada tanggal ini
            // Jika ada cuti disetujui, tidak boleh input absensi HADIR
            $cutiDisetujui = null;
            if ($idSdm) {
                $cutiDisetujui = DB::connection('mysql')->table('cuti_pengajuan')
                    ->where('id_sdm', $idSdm)
                    ->where('status', 'disetujui')
                    ->whereDate('tanggal_mulai', '<=', $header->toDateString())
                    ->whereDate('tanggal_selesai', '>=', $header->toDateString())
                    ->first(['id_cuti', 'tanggal_mulai', 'tanggal_selesai', 'jumlah_hari']);
            }

            $allowedEndDates = [
                $header->toDateString(),
                $header->copy()->addDay()->toDateString(), // shift malam boleh nyebrang H+1
            ];

            // Jika tanggal absensi adalah hari libur (Minggu / libur nasional / libur PT),
            // maka input jenis ALPHA/CUTI tidak masuk akal (karena tidak ada potongan di hari libur).
            // Ini mencegah data yang membingungkan dan menjaga alur payroll.
            $isHoliday = $this->isHolidayDate($header->toDateString());
            $jenisNameMap = [];
            if ($isHoliday) {
                $ids = array_values(array_unique(array_filter((array) data_get($this->input('detail', []), 'id_jenis_absen', []))));
                if (!empty($ids)) {
                    $jenisNameMap = DB::connection('mysql')->table('absen_jenis')
                        ->whereIn('id_jenis_absen', $ids)
                        ->pluck('nama_absen', 'id_jenis_absen')
                        ->map(fn($v) => strtoupper(trim((string) $v)))
                        ->toArray();
                }
            }

            $detail = $this->input('detail', []);
            if (!is_array($detail)) {
                $validator->errors()->add('detail', 'Detail absensi harus berupa array.');
                return;
            }

            $keys = ['id_jenis_absen', 'waktu_mulai', 'waktu_selesai', 'durasi_jam', 'lokasi_pulang'];
            $n = 0;
            foreach ($keys as $k) {
                $v = $detail[$k] ?? null;
                if (is_array($v)) $n = max($n, count($v));
            }

            $intervals = [];
            $validRowCount = 0;

            for ($i = 0; $i < $n; $i++) {
                $idJenis = data_get($detail, "id_jenis_absen.$i");
                $mulai   = data_get($detail, "waktu_mulai.$i");
                $selesai = data_get($detail, "waktu_selesai.$i");
                $durasi  = data_get($detail, "durasi_jam.$i");
                $lokasi  = data_get($detail, "lokasi_pulang.$i");

                // jika satu baris benar-benar kosong -> skip
                $allEmpty = empty($idJenis) && empty($mulai) && empty($selesai)
                    && ($durasi === null || $durasi === '' || (float)$durasi === 0.0)
                    && empty($lokasi);

                if ($allEmpty) continue;

                // jika user mengisi sebagian -> wajib lengkap
                if (empty($idJenis)) {
                    $validator->errors()->add("detail.id_jenis_absen.$i", 'Jenis absen wajib dipilih.');
                    continue;
                }

                // Validasi: hari libur tidak boleh diisi ALPHA/CUTI
                if ($isHoliday) {
                    $nama = $jenisNameMap[(int) $idJenis] ?? null;
                    if (in_array($nama, ['ALPHA', 'CUTI'], true)) {
                        $validator->errors()->add("detail.id_jenis_absen.$i", 'Tanggal ini adalah hari libur, jadi tidak boleh memilih jenis ALPHA/CUTI.');
                        continue;
                    }
                }

                // Validasi: jika ada cuti disetujui, tidak boleh input HADIR
                if ($cutiDisetujui) {
                    // Get nama jenis absen untuk cek apakah HADIR
                    $namaJenisAbsen = DB::connection('mysql')->table('absen_jenis')
                        ->where('id_jenis_absen', $idJenis)
                        ->value('nama_absen');
                    
                    if (strtoupper(trim($namaJenisAbsen ?? '')) === 'HADIR') {
                        $tglMulai = Carbon::parse($cutiDisetujui->tanggal_mulai)->format('d/m/Y');
                        $tglSelesai = Carbon::parse($cutiDisetujui->tanggal_selesai)->format('d/m/Y');
                        $validator->errors()->add(
                            "detail.id_jenis_absen.$i", 
                            "Tidak bisa input HADIR karena SDM ini memiliki cuti disetujui pada tanggal {$tglMulai} s/d {$tglSelesai}."
                        );
                        continue;
                    }
                }
                if (empty($mulai)) {
                    $validator->errors()->add("detail.waktu_mulai.$i", 'Waktu mulai wajib diisi.');
                    continue;
                }
                if (empty($selesai)) {
                    $validator->errors()->add("detail.waktu_selesai.$i", 'Waktu selesai wajib diisi.');
                    continue;
                }

                try {
                    $start = Carbon::parse($mulai);
                    $end   = Carbon::parse($selesai);
                } catch (\Throwable) {
                    $validator->errors()->add("detail.waktu_mulai.$i", 'Format waktu mulai/selesai tidak valid.');
                    continue;
                }

                // validasi jam operasional perusahaan (07:00 - 23:00) + lembur sampai 01:00
                $startHour = (int) $start->format('H');
                $endHour = (int) $end->format('H');
                $isSameDay = $end->toDateString() === $start->toDateString();
                $isNextDay = $end->toDateString() === $start->copy()->addDay()->toDateString();
                
                // Waktu mulai harus dalam jam operasional (07:00 - 23:00)
                if ($startHour < self::WORKING_HOUR_START || $startHour >= self::WORKING_HOUR_END) {
                    $validator->errors()->add("detail.waktu_mulai.$i", 'Waktu mulai harus dalam jam operasional perusahaan (07:00 - 23:00).');
                    continue;
                }
                
                // Validasi waktu selesai
                if ($isSameDay) {
                    // Jika selesai di hari yang sama, harus dalam jam operasional (07:00 - 23:59)
                    if ($endHour < self::WORKING_HOUR_START) {
                        $validator->errors()->add("detail.waktu_selesai.$i", 'Waktu selesai harus dalam jam operasional perusahaan (07:00 - 01:00).');
                        continue;
                    }
                } elseif ($isNextDay) {
                    // Jika nyebrang hari (lembur), hanya boleh sampai jam 01:00
                    if ($endHour > self::OVERTIME_HOUR_END) {
                        $validator->errors()->add("detail.waktu_selesai.$i", 'Lembur maksimal sampai jam 01:00. Waktu selesai melampaui batas lembur.');
                        continue;
                    }
                } else {
                    // Lebih dari 1 hari tidak diperbolehkan
                    $validator->errors()->add("detail.waktu_selesai.$i", 'Waktu selesai tidak valid. Maksimal lembur sampai jam 01:00 hari berikutnya.');
                    continue;
                }

                // aturan tanggal: mulai harus di tanggal header; selesai boleh header atau header+1
                if ($start->toDateString() !== $header->toDateString()) {
                    $validator->errors()->add("detail.waktu_mulai.$i", 'Tanggal waktu mulai harus sama dengan tanggal absensi.');
                    continue;
                }
                if (!in_array($end->toDateString(), $allowedEndDates, true)) {
                    $validator->errors()->add("detail.waktu_selesai.$i", 'Tanggal waktu selesai harus di tanggal absensi atau maksimal H+1 (shift malam).');
                    continue;
                }

                $diffSeconds = $start->diffInSeconds($end, false);
                if ($diffSeconds < 0) {
                    $validator->errors()->add("detail.waktu_selesai.$i", 'Waktu selesai harus lebih besar/sama dengan waktu mulai.');
                    continue;
                }

                $diffHours = $diffSeconds / 3600;
                if ($diffHours > self::MAX_DETAIL_HOURS) {
                    $validator->errors()->add("detail.waktu_selesai.$i", 'Durasi 1 baris detail terlalu lama (maks ' . self::MAX_DETAIL_HOURS . ' jam).');
                    continue;
                }

                $durasiF = (float) ($durasi ?? 0);
                if ($durasiF < 0) {
                    $validator->errors()->add("detail.durasi_jam.$i", 'Durasi tidak boleh negatif.');
                    continue;
                }

                // durasi harus sesuai selisih waktu (toleransi kecil)
                if (abs($diffHours - $durasiF) > self::DURATION_TOLERANCE_HOURS) {
                    $validator->errors()->add(
                        "detail.durasi_jam.$i",
                        'Durasi tidak sesuai dengan selisih waktu mulai & selesai. (Seharusnya: ' . number_format($diffHours, 2, '.', '') . ' jam)'
                    );
                    continue;
                }

                $validRowCount++;
                $intervals[] = [
                    'i' => $i,
                    'start' => $start->getTimestamp(),
                    'end'   => $end->getTimestamp(),
                ];
            }

            if ($validRowCount === 0) {
                $validator->errors()->add('detail', 'Minimal isi 1 baris detail absensi.');
                return;
            }

            // cek overlap antar baris detail
            usort($intervals, fn ($a, $b) => $a['start'] <=> $b['start']);
            for ($k = 1; $k < count($intervals); $k++) {
                $prev = $intervals[$k - 1];
                $cur  = $intervals[$k];
                if ($cur['start'] < $prev['end']) {
                    $validator->errors()->add("detail.waktu_mulai.{$cur['i']}", 'Waktu detail bertabrakan dengan baris detail sebelumnya.');
                    break;
                }
            }
        });
    }

    /**
     * Hari libur = Minggu atau tanggal yang terdaftar di ref_libur_nasional / ref_libur_pt.
     */
    private function isHolidayDate(string $date): bool
    {
        // Minggu
        if ((int) date('w', strtotime($date)) === 0) {
            return true;
        }

        $existsNasional = DB::connection('mysql')
            ->table('ref_libur_nasional')
            ->whereDate('tanggal', $date)
            ->exists();

        if ($existsNasional) {
            return true;
        }

        return DB::connection('mysql')
            ->table('ref_libur_pt')
            ->whereDate('tanggal', $date)
            ->exists();
    }

    protected function failedValidation(ContractValidator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()->messages(),
        ], 422));
    }
}
