<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

final class AbsenJenis extends Model
{
    protected $connection = 'absensi';
    public $timestamps = false;
    protected $table = 'absen_jenis';
    protected $primaryKey = 'id_jenis_absen';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_absen',
        'kategori',
        'potong_gaji',
    ];

    protected $casts = [
        'id_jenis_absen' => 'integer',
        'potong_gaji' => 'integer',
    ];
}
