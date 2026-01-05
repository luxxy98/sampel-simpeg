<?php

namespace App\Models\Referensi;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $connection = 'mysql';
    protected $table = 'hari_libur';
    protected $primaryKey = 'id_hari_libur';
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'nama',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
