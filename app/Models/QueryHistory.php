<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QueryHistory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'symbol',
        'open',
        'high',
        'low',
        'close',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
