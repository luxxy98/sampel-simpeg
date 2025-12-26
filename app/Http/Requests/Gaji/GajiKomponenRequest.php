<?php

namespace App\Http\Requests\Gaji;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class GajiKomponenRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_jabatan' => 'required|integer',
            'id_jenis_komponen' => 'required|integer',
            'nominal' => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_jabatan' => 'jabatan',
            'id_jenis_komponen' => 'jenis komponen',
            'nominal' => 'nominal',
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
