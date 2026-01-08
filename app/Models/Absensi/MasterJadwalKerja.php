<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

/**
 * Master shift/jadwal kerja.
 * Ini tabel referensi jam masuk & jam pulang.
 *
 * Disarankan pakai tabel: master_jadwal_kerja
 * Kolom minimal: id_jadwal, nama_jadwal, jam_masuk, jam_pulang, (opsional) keterangan, timestamps
 */
final class MasterJadwalKerja extends Model
{
    protected $connection = 'mysql';
    protected $table = 'master_jadwal_kerja';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = true;

    protected $fillable = [
        'nama_jadwal',
        'keterangan',
        'jam_masuk',
        'jam_pulang',
    ];

    protected $casts = [
        'id_jadwal' => 'integer',
    ];
}
