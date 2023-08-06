<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Project extends Model
{
    use HasFactory, UuidTrait;

    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relationship with Todo model (one to many)
     * @return BelongsToMany
     */
    public function todos(): HasManyThrough
    {
        return $this->hasManyThrough(Todo::class, projectTodo::class, 'project_id', 'id', 'id', 'todo_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(projectUser::class, 'project_user', 'project_id', 'user_id');
    }
}
