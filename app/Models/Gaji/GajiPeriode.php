<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;


final class GajiPeriode extends Model
{
    protected $connection = 'absensigaji';
    protected $table = 'gaji_periode';
    protected $primaryKey = 'id_periode';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'tahun',
        'bulan',
        'tanggal_mulai',
        'tanggal_selesai',

        'status',
        'status_peninjauan',
        'tanggal_penggajian',
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
