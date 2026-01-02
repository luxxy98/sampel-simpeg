<?php

namespace App\Http\Requests\Gaji;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class GajiJenisKomponenRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_komponen' => 'required|string|max:100',
            'jenis' => 'required|in:PENGHASILAN,POTONGAN',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_komponen' => 'nama komponen',
            'jenis' => 'jenis',
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
