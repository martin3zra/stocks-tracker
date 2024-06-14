<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\UserNotifier;
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
        return $this->hasMany(QueryHistory::class)
            ->orderBy('created_at', 'desc');
    }

    public function logQuerySearch(array $attributes): void
    {
        $this->queryHistories()->create($attributes);
    }

    public function notify(UserNotifier $notifier, string $filename, $stockCode, array $data)
    {
        $notifier->notify($this, $filename, $stockCode, $data);
    }
}
