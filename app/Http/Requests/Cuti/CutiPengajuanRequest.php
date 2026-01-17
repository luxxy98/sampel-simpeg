<?php

namespace App\Http\Requests\Cuti;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CutiPengajuanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_sdm' => 'required|integer|exists:person_sdm,id_sdm',
            'id_jenis_cuti' => 'required|integer|exists:cuti_jenis,id_jenis_cuti',
            'tanggal_mulai' => 'required|date_format:Y-m-d',
            'tanggal_selesai' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_sdm' => 'SDM',
            'id_jenis_cuti' => 'jenis cuti',
            'tanggal_mulai' => 'tanggal mulai',
            'tanggal_selesai' => 'tanggal selesai',
            'alasan' => 'alasan',
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
