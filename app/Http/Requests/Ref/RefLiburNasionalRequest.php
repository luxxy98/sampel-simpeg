<?php

namespace App\Http\Requests\Ref;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RefLiburNasionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'tanggal' => [
                'required',
                'date',
                Rule::unique('ref_libur_nasional', 'tanggal')->ignore($id, 'id_kalnas'),
            ],
            'keterangan' => ['required', 'string', 'max:255'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()->messages(),
            ], 422)
        );
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Field :attribute wajib diisi.',
            'tanggal.date' => 'Field :attribute harus berupa tanggal yang valid.',
            'tanggal.unique' => 'Tanggal tersebut sudah ada.',
            'keterangan.required' => 'Field :attribute wajib diisi.',
            'keterangan.string' => 'Field :attribute harus berupa teks.',
            'keterangan.max' => 'Field :attribute maksimal :max karakter.',
        ];
    }
}
