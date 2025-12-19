<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    protected $connection = 'absensigaji';

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'id_sdm',
        'id_jadwal_karyawan',
        'total_jam_kerja',
        'total_terlambat',
        'total_pulang_awal',
    ];
}
