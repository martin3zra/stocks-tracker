<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
    ];

    public function queryHistories(): HasMany
    {
        return $this->hasMany(QueryHistory::class);
    }
}
