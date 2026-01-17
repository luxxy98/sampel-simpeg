<?php

namespace App\Models\Cuti;

use App\Traits\SkipsEmptyAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

final class CutiJenis extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use SkipsEmptyAudit { SkipsEmptyAudit::transformAudit insteadof AuditableTrait; }

    public $timestamps = false;

    protected $table = 'cuti_jenis';
    protected $primaryKey = 'id_jenis_cuti';

    protected $fillable = [
        'nama_jenis',
        'maks_hari_per_tahun',
        'status',
    ];

    protected $guarded = [
        'id_jenis_cuti',
    ];

    protected $casts = [
        'id_jenis_cuti' => 'integer',
        'maks_hari_per_tahun' => 'integer',
        'status' => 'string',
    ];

    public function setNamaJenisAttribute($v): void
    {
        $this->attributes['nama_jenis'] = trim(strip_tags((string) $v));
    }
}
