<?php

namespace App\Models\Sppd;

use App\Traits\SkipsEmptyAudit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

final class Sppd extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use SkipsEmptyAudit { SkipsEmptyAudit::transformAudit insteadof AuditableTrait; }

    public $timestamps = false;

    protected $table = 'sppd';
    protected $primaryKey = 'id_sppd';

    protected $fillable = [
        'id_sdm',
        'nomor_surat',
        'tanggal_surat',
        'tanggal_berangkat',
        'tanggal_pulang',
        'tujuan',
        'instansi_tujuan',
        'maksud_tugas',
        'transportasi',
        'biaya_transport',
        'biaya_penginapan',
        'uang_harian',
        'biaya_lainnya',
        'total_biaya',
        'status',
        'catatan',
        'approved_by',
        'tanggal_persetujuan',
        'created_by',
    ];

    protected $casts = [
        'id_sppd' => 'integer',
        'id_sdm' => 'integer',
        'biaya_transport' => 'integer',
        'biaya_penginapan' => 'integer',
        'uang_harian' => 'integer',
        'biaya_lainnya' => 'integer',
        'total_biaya' => 'integer',
        'tanggal_surat' => 'date:Y-m-d',
        'tanggal_berangkat' => 'date:Y-m-d',
        'tanggal_pulang' => 'date:Y-m-d',
        'tanggal_persetujuan' => 'date:Y-m-d',
        'status' => 'string',
    ];

    public function setTujuanAttribute($v): void
    {
        $this->attributes['tujuan'] = trim(strip_tags((string) $v));
    }

    public function setMaksudTugasAttribute($v): void
    {
        $this->attributes['maksud_tugas'] = trim(strip_tags((string) $v));
    }

    public function setInstansiTujuanAttribute($v): void
    {
        $this->attributes['instansi_tujuan'] = $v ? trim(strip_tags((string) $v)) : null;
    }

    public function setTransportasiAttribute($v): void
    {
        $this->attributes['transportasi'] = $v ? trim(strip_tags((string) $v)) : null;
    }

    public function setNomorSuratAttribute($v): void
    {
        $this->attributes['nomor_surat'] = $v ? trim(strip_tags((string) $v)) : null;
    }

    public function setCatatanAttribute($v): void
    {
        $this->attributes['catatan'] = $v ? trim(strip_tags((string) $v)) : null;
    }

    public function getTanggalBerangkatAttribute($v): ?string
    {
        return $v ? Carbon::parse($v)->format('Y-m-d') : null;
    }

    public function getTanggalPulangAttribute($v): ?string
    {
        return $v ? Carbon::parse($v)->format('Y-m-d') : null;
    }
}
