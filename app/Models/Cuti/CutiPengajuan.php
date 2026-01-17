<?php

namespace App\Models\Cuti;

use App\Traits\SkipsEmptyAudit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

final class CutiPengajuan extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use SkipsEmptyAudit { SkipsEmptyAudit::transformAudit insteadof AuditableTrait; }

    public $timestamps = false;

    protected $table = 'cuti_pengajuan';
    protected $primaryKey = 'id_cuti';

    protected $fillable = [
        'id_sdm',
        'id_jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'alasan',
        'status',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
        'approved_by',
        'catatan',
    ];

    protected $guarded = [
        'id_cuti',
    ];

    protected $casts = [
        'id_cuti' => 'integer',
        'id_sdm' => 'integer',
        'id_jenis_cuti' => 'integer',
        'jumlah_hari' => 'integer',
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
        'tanggal_pengajuan' => 'date:Y-m-d',
        'tanggal_persetujuan' => 'date:Y-m-d',
        'status' => 'string',
    ];

    public function setAlasanAttribute($v): void
    {
        $this->attributes['alasan'] = trim(strip_tags((string) $v));
    }

    public function setCatatanAttribute($v): void
    {
        $this->attributes['catatan'] = $v ? trim(strip_tags((string) $v)) : null;
    }

    public function getTanggalMulaiAttribute($v): ?string
    {
        return $v ? Carbon::parse($v)->format('Y-m-d') : null;
    }

    public function getTanggalSelesaiAttribute($v): ?string
    {
        return $v ? Carbon::parse($v)->format('Y-m-d') : null;
    }

    public function getTanggalPengajuanAttribute($v): ?string
    {
        return $v ? Carbon::parse($v)->format('Y-m-d') : null;
    }

    public function getTanggalPersetujuanAttribute($v): ?string
    {
        return $v ? Carbon::parse($v)->format('Y-m-d') : null;
    }
}
