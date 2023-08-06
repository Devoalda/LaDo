<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Todo extends Model
{
    use HasFactory, UuidTrait;

    protected $dateFormat = 'U';

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): HasOneThrough
    {
        return $this->hasOneThrough(Project::class, ProjectTodo::class, 'todo_id', 'id', 'id', 'project_id');
    }

}
