<?php

namespace App\Http\Requests\Absensi;

use Illuminate\Foundation\Http\FormRequest;

class AbsensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sudah ada middleware auth:admin di routes
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'id_sdm' => ['required', 'integer'],
            'id_jadwal_karyawan' => ['required', 'integer'],
            'total_jam_kerja' => ['nullable', 'numeric'],
            'total_terlambat' => ['nullable', 'numeric'],
            'total_pulang_awal' => ['nullable', 'numeric'],
        ];
    }
}
