<?php

namespace App\Http\Requests\Cuti;

use App\Models\Cuti\CutiJenis;
use Carbon\Carbon;
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
            'tanggal_selesai' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:tanggal_mulai',
                function ($attribute, $value, $fail) {
                    $this->validateMaxDays($fail);
                },
            ],
            'alasan' => 'required|string|max:255',
        ];
    }

    /**
     * Validate that the requested days don't exceed the max allowed by leave type
     */
    protected function validateMaxDays($fail): void
    {
        $idJenisCuti = $this->input('id_jenis_cuti');
        $tanggalMulai = $this->input('tanggal_mulai');
        $tanggalSelesai = $this->input('tanggal_selesai');

        if (!$idJenisCuti || !$tanggalMulai || !$tanggalSelesai) {
            return; // Let other validators handle missing fields
        }

        $jenisCuti = CutiJenis::find($idJenisCuti);
        if (!$jenisCuti) {
            return; // Let exists validator handle this
        }

        $maksHari = $jenisCuti->maks_hari_per_tahun;
        if (!$maksHari || $maksHari <= 0) {
            return; // No limit set
        }

        try {
            $mulai = Carbon::parse($tanggalMulai);
            $selesai = Carbon::parse($tanggalSelesai);
            $jumlahHari = $mulai->diffInDays($selesai) + 1;

            if ($jumlahHari > $maksHari) {
                $fail("Jumlah hari cuti ({$jumlahHari} hari) melebihi batas maksimal untuk jenis cuti \"{$jenisCuti->nama_jenis}\" ({$maksHari} hari per tahun).");
            }
        } catch (\Exception $e) {
            // Date parsing error, let date_format validator handle it
        }
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

    public function messages(): array
    {
        return [
            'id_sdm.required' => 'Pegawai (SDM) wajib dipilih.',
            'id_sdm.exists' => 'Pegawai (SDM) tidak valid.',
            'id_jenis_cuti.required' => 'Jenis cuti wajib dipilih.',
            'id_jenis_cuti.exists' => 'Jenis cuti tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date_format' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date_format' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai. Tanggal tidak valid!',
            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.max' => 'Alasan maksimal 255 karakter.',
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
