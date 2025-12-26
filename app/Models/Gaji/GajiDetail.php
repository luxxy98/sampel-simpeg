<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;

final class GajiDetail extends Model
{
    protected $table = 'gaji_detail';
    protected $primaryKey = 'id_gaji_detail';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'id_gaji',
        'id_gaji_komponen',
        'nominal',
        'keterangan',
    ];

    protected $casts = [
        'id_gaji_detail' => 'integer',
        'id_gaji' => 'integer',
        'id_gaji_komponen' => 'integer',
        'nominal' => 'decimal:2',
    ];
}
