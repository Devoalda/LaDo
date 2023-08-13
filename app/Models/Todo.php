<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Todo extends Model
{
    use HasFactory, UuidTrait;

    protected $dateFormat = 'U';
    protected $table = 'todos';

    protected $fillable = [
        'title',
        'description',
        'due_start',
        'due_end',
        'completed_at',
    ];

    protected $casts = [
        // Unix timestamp for due_start, due_end, completed_at, created_at, updated_at
        "due_start" => "integer",
        "due_end" => "integer",
        "completed_at" => "integer",
        "created_at" => "integer",
        "updated_at" => "integer",
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_todo',
            'todo_id',
            'project_id');
    }


    public function pomo(): HasMany
    {
        return $this->hasMany(Pomo::class);
    }
}
