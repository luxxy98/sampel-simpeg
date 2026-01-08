<?php

namespace App\Http\Requests\Absensi;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class MasterJadwalKerjaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_jadwal' => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'jam_masuk' => ['required', 'date_format:H:i:s'],
            'jam_pulang' => ['required', 'date_format:H:i:s'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function (Validator $v) {
            $masuk = (string) $this->input('jam_masuk');
            $pulang = (string) $this->input('jam_pulang');

            if ($masuk && $pulang && $masuk === $pulang) {
                $v->errors()->add('jam_pulang', 'Jam pulang tidak boleh sama dengan jam masuk.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()->messages(),
        ], 422));
    }
}
