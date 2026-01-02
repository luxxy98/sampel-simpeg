<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

class JadwalKaryawan extends Model
{
    protected $connection = 'mysql';
    protected $table = 'sdm_jadwal_karyawan'; 
    protected $primaryKey = 'id_jadwal_karyawan';
    public $timestamps = false;

   
    protected $fillable = ['nama_jadwal', 'keterangan', 'jam_masuk', 'jam_pulang'];
}
