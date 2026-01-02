<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;

final class GajiDistribusi extends Model
{
    protected $connection = 'absensigaji';

    protected $table = 'gaji_distribusi';
    protected $primaryKey = 'id_distribusi';
    public $incrementing = true;
    protected $keyType = 'int';

    // tabel ini tidak punya created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'id_periode',
        'id_gaji',
        'id_sdm',
        'id_rekening',
        'jumlah_transfer',
        'status_transfer',
        'tanggal_transfer',
        'catatan',
    ];

    protected $casts = [
        'id_distribusi' => 'integer',
        'id_periode' => 'integer',
        'id_gaji' => 'integer',
        'id_sdm' => 'integer',
        'id_rekening' => 'integer',
        'jumlah_transfer' => 'decimal:2',
        'tanggal_transfer' => 'datetime:Y-m-d H:i:s',
    ];
}
