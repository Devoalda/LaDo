<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Project extends Model
{
    use HasFactory, UuidTrait;

    protected $dateFormat = 'U';
    protected $table = 'projects';

    protected $fillable = [
        'name',
        'description',
        'completed_at',
    ];

    /**
     * Relationship with Todo model (one to many)
     * @return BelongsToMany
     */
    public function todos(): BelongsToMany
    {
        return $this->belongsToMany(Todo::class, 'project_todo', 'project_id', 'todo_id');
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            projectUser::class,
            'project_id',
            'id',
            'id',
            'user_id'
        );
    }
}
