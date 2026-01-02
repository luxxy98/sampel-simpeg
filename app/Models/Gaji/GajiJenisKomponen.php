<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;

final class GajiJenisKomponen extends Model
{
    protected $connection = 'absensigaji';
    public $timestamps = true;

    protected $table = 'gaji_jenis_komponen';
    protected $primaryKey = 'id_jenis_komponen';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_komponen',
        'jenis', // PENGHASILAN / POTONGAN
    ];
}
