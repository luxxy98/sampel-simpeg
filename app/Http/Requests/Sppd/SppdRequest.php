<?php

namespace App\Http\Requests\Sppd;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SppdRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        // Konversi field biaya ke integer (hapus karakter non-digit)
        $biayaFields = ['biaya_transport', 'biaya_penginapan', 'uang_harian', 'biaya_lainnya'];
        $cleaned = [];
        
        foreach ($biayaFields as $field) {
            if ($this->has($field) && $this->input($field) !== null && $this->input($field) !== '') {
                // Hapus semua karakter kecuali digit
                $cleaned[$field] = (int) preg_replace('/[^0-9]/', '', (string) $this->input($field));
            }
        }
        
        if (!empty($cleaned)) {
            $this->merge($cleaned);
        }
    }

    public function rules(): array
    {
        return [
            'id_sdm' => 'required|integer|exists:person_sdm,id_sdm',
            'nomor_surat' => 'nullable|string|max:50',
            'tanggal_surat' => 'required|date_format:Y-m-d',
            'tanggal_berangkat' => 'required|date_format:Y-m-d',
            'tanggal_pulang' => 'required|date_format:Y-m-d|after_or_equal:tanggal_berangkat',
            'tujuan' => 'required|string|max:120',
            'instansi_tujuan' => 'nullable|string|max:120',
            'maksud_tugas' => 'required|string|max:255',
            'transportasi' => 'nullable|string|max:80',
            'biaya_transport' => 'nullable|integer|min:0',
            'biaya_penginapan' => 'nullable|integer|min:0',
            'uang_harian' => 'nullable|integer|min:0',
            'biaya_lainnya' => 'nullable|integer|min:0',
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
