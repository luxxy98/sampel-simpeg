<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;

class TarifLembur extends Model
{
    protected $connection = 'mysql';
    protected $table = 'tarif_lembur';
    protected $primaryKey = 'id_tarif';
    public $timestamps = true;

    protected $fillable = [
        'nama_tarif',
        'tarif_per_jam',
        'keterangan',
    ];

    protected $casts = [
        'tarif_per_jam' => 'decimal:2',
    ];
}
