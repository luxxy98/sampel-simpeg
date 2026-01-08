<?php

namespace App\Models\Ref;

use App\Traits\SkipsEmptyAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

final class RefLiburPt extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use SkipsEmptyAudit {
        SkipsEmptyAudit::transformAudit insteadof AuditableTrait;
    }

    protected $table = 'ref_libur_pt';
    protected $primaryKey = 'id_libur_pt';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $guarded = [
        'id_libur_pt',
    ];

    protected $casts = [
        'id_libur_pt' => 'integer',
        'tanggal' => 'date:Y-m-d',
    ];

    public function setKeteranganAttribute($value): void
    {
        $this->attributes['keterangan'] = trim(strip_tags((string) $value));
    }
}
