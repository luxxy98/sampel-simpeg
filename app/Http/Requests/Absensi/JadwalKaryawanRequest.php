<?php

namespace App\Http\Requests\Absensi;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

/**
 * Request untuk tabel assignment: sdm_jadwal_karyawan (pesonine.sql)
 * Fokus utama: mencegah bentrok rentang tanggal untuk SDM yang sama.
 */
final class JadwalKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_sdm' => ['required', 'integer', 'exists:mysql.person_sdm,id_sdm'],
            'id_jadwal' => ['required', 'integer', 'exists:mysql.master_jadwal_kerja,id_jadwal'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date'],
            'dibuat_oleh' => ['nullable', 'integer'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $v) {
            $idSdm = (int) $this->input('id_sdm');
            $mulai = (string) $this->input('tanggal_mulai');
            $selesai = (string) $this->input('tanggal_selesai');

            if (!$idSdm || !$mulai || !$selesai) return;

            // validasi rentang
            if (strtotime($mulai) > strtotime($selesai)) {
                $v->errors()->add('tanggal_selesai', 'Tanggal selesai harus >= tanggal mulai.');
                return;
            }

            // Cek overlap / bentrok: [mulai, selesai] overlap dengan data lain milik SDM yang sama
            // overlap jika: existing.mulai <= selesai && existing.selesai >= mulai
            $idCurrent = $this->route('id') ? (int) $this->route('id') : null;

            try {
                $q = DB::connection('mysql')->table('sdm_jadwal_karyawan')
                    ->where('id_sdm', $idSdm)
                    ->whereDate('tanggal_mulai', '<=', $selesai)
                    ->whereDate('tanggal_selesai', '>=', $mulai);

                if ($idCurrent) $q->where('id_jadwal_karyawan', '!=', $idCurrent);

                $exists = $q->exists();

                if ($exists) {
                    $v->errors()->add('tanggal_mulai', 'Bentrok: SDM sudah punya jadwal pada rentang tanggal tersebut.');
                }
            } catch (\Throwable) {
                // Jika tabel belum ada, jangan bikin request crash.
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()->messages(),
        ], 422));
    }
}
