<?php

namespace App\Models\Ref;

use App\Traits\SkipsEmptyAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

final class RefLiburNasional extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use SkipsEmptyAudit {
        SkipsEmptyAudit::transformAudit insteadof AuditableTrait;
    }

    protected $table = 'ref_libur_nasional';
    protected $primaryKey = 'id_kalnas';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $guarded = [
        'id_kalnas',
    ];

    protected $casts = [
        'id_kalnas' => 'integer',
        'tanggal' => 'date:Y-m-d',
    ];

    public function setKeteranganAttribute($value): void
    {
        $this->attributes['keterangan'] = trim(strip_tags((string) $value));
    }
}
