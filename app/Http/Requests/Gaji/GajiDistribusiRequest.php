<?php

namespace App\Http\Requests\Gaji;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

final class GajiDistribusiRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Determine if this is create or update operation
        $idDistribusi = $this->route('id'); // dari route parameter {id}
        
        return [
            'id_periode' => 'required|integer',
            'id_gaji' => [
                'required',
                'integer',
                // Unique validation: untuk create, cek duplikasi. Untuk update, exclude current record
                $idDistribusi 
                    ? "unique:absensigaji.gaji_distribusi,id_gaji,{$idDistribusi},id_distribusi"
                    : 'unique:absensigaji.gaji_distribusi,id_gaji'
            ],
            'id_sdm' => 'required|integer',
            'id_rekening' => 'nullable|integer',
            'jumlah_transfer' => 'required|numeric|min:0',
            'status_transfer' => 'required|in:PENDING,SUCCESS,FAILED',
            'tanggal_transfer' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    // Skip validation jika tanggal_transfer kosong
                    if (empty($value)) return;
                    
                    // Ambil data periode
                    $idPeriode = $this->input('id_periode');
                    if (!$idPeriode) return;
                    
                    $periode = DB::connection('absensigaji')
                        ->table('gaji_periode')
                        ->where('id_periode', $idPeriode)
                        ->first(['tahun', 'bulan']);
                    
                    if (!$periode) return;
                    
                    // Hitung tanggal awal periode (tanggal 1 bulan periode)
                    $periodeStart = sprintf('%04d-%02d-01', $periode->tahun, $periode->bulan);
                    
                    // Bandingkan tanggal transfer dengan periode
                    if ($value < $periodeStart) {
                        $bulanNama = [
                            '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        $periodeName = $bulanNama[$periode->bulan] . ' ' . $periode->tahun;
                        $fail("Tanggal transfer tidak boleh sebelum periode gaji {$periodeName}");
                    }
                }
            ],
            'catatan' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id_gaji.unique' => 'Data distribusi untuk transaksi gaji ini sudah ada',
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
