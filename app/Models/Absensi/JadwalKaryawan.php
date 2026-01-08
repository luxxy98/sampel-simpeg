<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

class JadwalKaryawan extends Model
{
    protected $connection = 'mysql';
    /**
     * Jadwal karyawan (assignment) per SDM, sesuai pesonine.sql
     * - berisi rentang tanggal: tanggal_mulai - tanggal_selesai
     * - referensi shift/jadwal ada di master_jadwal_kerja (id_jadwal)
     */
    protected $table = 'sdm_jadwal_karyawan';
    protected $primaryKey = 'id_jadwal_karyawan';
    public $timestamps = true;

    protected $fillable = [
        'id_jadwal',
        'id_sdm',
        'tanggal_mulai',
        'tanggal_selesai',
        'dibuat_oleh',
    ];

    protected $casts = [
        'id_jadwal_karyawan' => 'integer',
        'id_jadwal' => 'integer',
        'id_sdm' => 'integer',
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
    ];
}
