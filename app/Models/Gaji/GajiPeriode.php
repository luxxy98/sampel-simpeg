<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;

final class GajiPeriode extends Model
{
    protected $table = 'gaji_periode';
    protected $primaryKey = 'id_periode';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tahun',
        'bulan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_penggajian',
        'status',
        'status_peninjauan',
    ];

    protected $casts = [
        'id_periode' => 'integer',
        'tahun' => 'integer',
        'bulan' => 'integer',
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
        'tanggal_penggajian' => 'date:Y-m-d',
    ];
}
