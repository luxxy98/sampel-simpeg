<?php

namespace App\Models\Gaji;

use Illuminate\Database\Eloquent\Model;

class PeriodeGaji extends Model
{
    protected $connection = 'absensigaji';
    protected $table = 'gaji_periode';  
    protected $primaryKey = 'id_periode';
    public $timestamps = false;

    protected $fillable = [
        'tahun',
        'bulan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'status_peninjauan',
        'tanggal_penggajian',
    ];
}
