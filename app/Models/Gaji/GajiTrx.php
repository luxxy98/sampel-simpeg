<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;

final class GajiTrx extends Model
{
    protected $table = 'gaji_trx';
    protected $primaryKey = 'id_gaji';
    public $incrementing = true;
    protected $keyType = 'int';

    // untuk aman (karena kita read-only di modul ini)
    public $timestamps = false;

    protected $fillable = [
        'id_periode',
        'id_sdm',
        'total_penghasilan',
        'total_potongan',
        'total_take_home_pay',
        'status',
    ];

    protected $casts = [
        'id_gaji' => 'integer',
        'id_periode' => 'integer',
        'id_sdm' => 'integer',
        'total_penghasilan' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'total_take_home_pay' => 'decimal:2',
    ];
}
