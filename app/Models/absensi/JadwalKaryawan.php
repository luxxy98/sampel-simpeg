<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

class JadwalKaryawan extends Model
{
    protected $connection = 'absensigaji';
    protected $table = 'sdm_jadwal_karyawan'; 
    protected $primaryKey = 'id_jadwal_karyawan';
    public $timestamps = false;

   
    protected $fillable = ['nama_jadwal', 'keterangan'];
}
