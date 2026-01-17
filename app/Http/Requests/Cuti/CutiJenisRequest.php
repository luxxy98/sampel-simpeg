<?php

namespace App\Http\Requests\Cuti;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CutiJenisRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_jenis' => 'required|string|max:50',
            'maks_hari_per_tahun' => 'nullable|integer|min:0|max:366',
            'status' => 'nullable|in:active,block',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_jenis' => 'nama jenis cuti',
            'maks_hari_per_tahun' => 'maks hari per tahun',
            'status' => 'status',
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

    public function messages(): array
    {
        return [
            'nama_jenis.required' => 'Field :attribute wajib diisi.',
            'nama_jenis.max' => 'Field :attribute maksimal :max karakter.',
            'maks_hari_per_tahun.integer' => 'Field :attribute harus angka.',
            'status.in' => 'Field :attribute hanya boleh active / block.',
        ];
    }
}
