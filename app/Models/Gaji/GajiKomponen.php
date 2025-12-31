<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;

final class GajiKomponen extends Model
{
    protected $connection = 'absensigaji';
    public $timestamps = true;
    protected $table = 'gaji_komponen';
    protected $primaryKey = 'id_gaji_komponen';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_jabatan',
        'id_jenis_komponen',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];
}
