<?php

namespace App\Http\Requests\Gaji;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

final class GajiPeriodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun' => ['required', 'integer', 'min:1900', 'max:2100'],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tanggal_penggajian' => ['nullable', 'date'],
            'status' => ['required', 'in:DRAFT,PROSES,SELESAI,DIBATALKAN,CLOSE'],
            'status_peninjauan' => ['required', 'in:DRAFT,DISETUJUI,GAGAL'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422));
    }
}
