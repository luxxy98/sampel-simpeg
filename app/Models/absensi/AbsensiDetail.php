<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

class AbsensiDetail extends Model
{
    protected $table = 'absensi_detail';
    protected $connection = 'absensigaji';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_absensi',
        'id_jenis_absen',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_jam',
        'lokasi_pulang',
    ];
}
