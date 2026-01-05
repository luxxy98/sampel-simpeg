<?php

namespace App\Models\Absensi;

use Illuminate\Database\Eloquent\Model;

final class Absensi extends Model
{
    protected $connection = 'mysql';
    public $timestamps = true;
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tanggal',
        'id_jadwal_karyawan',
        'id_sdm',
        'total_jam_kerja',
        'total_terlambat',
        'total_pulang_awal',
        'total_lembur',
        'is_hari_libur',
        'id_tarif_lembur',
        'nominal_lembur',
    ];

    protected $casts = [
        'id_absensi' => 'integer',
        'id_jadwal_karyawan' => 'integer',
        'id_sdm' => 'integer',
        'tanggal' => 'date:Y-m-d',
        'total_jam_kerja' => 'decimal:2',
        'total_terlambat' => 'decimal:2',
        'total_pulang_awal' => 'decimal:2',
        'total_lembur' => 'decimal:2',
        'is_hari_libur' => 'boolean',
        'id_tarif_lembur' => 'integer',
        'nominal_lembur' => 'decimal:2',
    ];
}
