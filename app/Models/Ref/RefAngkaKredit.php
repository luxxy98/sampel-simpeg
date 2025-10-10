<?php

namespace App\Models\Ref;

use App\Traits\SkipsEmptyAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

final class RefAngkaKredit extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use SkipsEmptyAudit {
        SkipsEmptyAudit::transformAudit insteadof AuditableTrait;
    }

    public $timestamps = false;

    protected $table = 'ref_angka_kredit';

    protected $primaryKey = 'id_score';

    protected $fillable = [
        'id_kegiatan',
        'id_subkegiatan',
        'id_jakad',
        'ak_bkd',
        'ak_dupak',
        'ak_skp',
    ];

    protected $guarded = [
        'id_score',
    ];

    protected $casts = [
        'id_score' => 'integer',
        'id_kegiatan' => 'integer',
        'id_subkegiatan' => 'integer',
        'id_jakad' => 'integer',
        'ak_bkd' => 'decimal:3',
        'ak_dupak' => 'decimal:3',
        'ak_skp' => 'decimal:3',
    ];

    public function setAkBkdAttribute($value): void
    {
        $this->attributes['ak_bkd'] = max(0, (float)$value);
    }

    public function setAkDupakAttribute($value): void
    {
        $this->attributes['ak_dupak'] = max(0, (float)$value);
    }

    public function setAkSkpAttribute($value): void
    {
        $this->attributes['ak_skp'] = max(0, (float)$value);
    }
}
