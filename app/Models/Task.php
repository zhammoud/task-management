<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([TaskObserver::class])]
class Task extends Model
{
    use SoftDeletes;

    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'status',
        'user_id',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'attachments' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function lastestComments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest()->limit(10);
    }
}
