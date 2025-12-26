<?php

namespace App\Http\Requests\Absensi;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class AbsensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'id_jadwal_karyawan' => ['required', 'integer'],
            'id_sdm' => ['required', 'integer'],

            'total_jam_kerja' => ['nullable', 'numeric', 'min:0'],
            'total_terlambat' => ['nullable', 'numeric', 'min:0'],
            'total_pulang_awal' => ['nullable', 'numeric', 'min:0'],

            'detail' => ['nullable', 'array'],

            // tambahan dari modal edit kamu
            'detail.id_detail' => ['nullable', 'array'],
            'detail.id_detail.*' => ['nullable', 'integer'],

            'detail.id_jenis_absen' => ['nullable', 'array'],
            'detail.id_jenis_absen.*' => ['nullable', 'integer'],

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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()->messages(),
        ], 422));
    }
}
