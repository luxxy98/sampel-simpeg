<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Model;

final class DbQueryLog extends Model
{
    public $timestamps = false;

    protected $connection = 'log';

    protected $table = 'db_query_logs';

    protected $fillable = [
        'connection_name',
        'sql_text',
        'bindings',
        'time_ms',
        'url',
        'route_name',
        'method',
        'user_id',
        'user_name',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'bindings' => 'array',
        'time_ms' => 'float',
        'created_at' => 'datetime',
    ];
}
