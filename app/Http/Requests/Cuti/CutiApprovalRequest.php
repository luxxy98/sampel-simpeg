<?php

namespace App\Http\Requests\Cuti;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CutiApprovalRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'status persetujuan',
            'catatan' => 'catatan',
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
