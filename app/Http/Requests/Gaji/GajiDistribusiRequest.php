<?php

namespace App\Http\Requests\Gaji;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class GajiDistribusiRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_periode' => 'required|integer',
            'id_gaji' => 'required|integer',
            'id_sdm' => 'required|integer',
            'id_rekening' => 'nullable|integer',
            'jumlah_transfer' => 'required|numeric|min:0',
            'status_transfer' => 'required|in:PENDING,SUCCESS,FAILED',
            'tanggal_transfer' => 'nullable|date', // frontend pakai flatpickr "Y-m-d H:i:S"
            'catatan' => 'nullable|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()->messages(),
            ], 422)
        );
    }
}
