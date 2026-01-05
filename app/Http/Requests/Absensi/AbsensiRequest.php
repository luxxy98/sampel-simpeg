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

        $mulaiArr   = $detail['waktu_mulai'] ?? null;
        $selesaiArr = $detail['waktu_selesai'] ?? null;
        $durasiArr  = $detail['durasi_jam'] ?? null;

        if (!is_array($mulaiArr) || !is_array($selesaiArr)) return;
        if (!is_array($durasiArr)) $durasiArr = [];

        $n = max(count($mulaiArr), count($selesaiArr), count($durasiArr));

        for ($i = 0; $i < $n; $i++) {
            $mulai  = $mulaiArr[$i] ?? null;
            $selesai = $selesaiArr[$i] ?? null;

            if (!$mulai || !$selesai) continue;

            try {
                // Normalize datetime using Carbon (handles format variations)
                $start = Carbon::parse($mulai);
                $end   = Carbon::parse($selesai);
                
                // Auto-koreksi tanggal waktu selesai untuk lembur dini hari
                // Jika jam mulai >= 12 dan jam selesai 00-01, maka user pasti maksud hari berikutnya
                $startHour = (int) $start->format('H');
                $endHour = (int) $end->format('H');
                
                if ($startHour >= 12 && $endHour <= self::OVERTIME_HOUR_END && $end->toDateString() === $start->toDateString()) {
                    // User input jam 00:00-01:00 di hari yang sama padahal maksudnya hari berikutnya
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

            $allowedEndDates = [
                $header->toDateString(),
                $header->copy()->addDay()->toDateString(), // shift malam boleh nyebrang H+1
            ];

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

    protected function failedValidation(ContractValidator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()->messages(),
        ], 422));
    }
}
