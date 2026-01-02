<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

final class AbsensiDetail extends Model
{
    protected $connection = 'mysql';
    public $timestamps = true;
    protected $table = 'absensi_detail';
    protected $primaryKey = 'id_detail';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_absensi',
        'id_jenis_absen',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_jam',
        'lokasi_pulang',
    ];

    protected $casts = [
        'id_detail' => 'integer',
        'id_absensi' => 'integer',
        'id_jenis_absen' => 'integer',
        'waktu_mulai' => 'datetime:Y-m-d H:i:s',
        'waktu_selesai' => 'datetime:Y-m-d H:i:s',
        'durasi_jam' => 'decimal:2',
    ];
}
