<?php

namespace App\Http\Requests\Gaji;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PeriodeGajiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id'); // dari /admin/gaji/periode/{id}

        return [
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'string'],
            'status_peninjauan' => ['required', 'string'],
            'tanggal_penggajian' => ['nullable', 'date'],

            // optional tapi bagus: unik kombinasi tahun+bulan
            // Kalau table-mu pakai unique (tahun, bulan), ini mencegah error SQL.
            // Rule ini untuk MySQL biasanya perlu custom; yang aman: validasi via DB unique index saja.
        ];
    }
}
