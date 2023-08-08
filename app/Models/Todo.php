<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function user(): Collection
    {
        // Select User given Todo
        return DB::table('users')
            ->join('project_user', 'users.id', '=', 'project_user.user_id')
            ->join('projects', 'project_user.project_id', '=', 'projects.id')
            ->join('project_todo', 'projects.id', '=', 'project_todo.project_id')
            ->join('todos', 'project_todo.todo_id', '=', 'todos.id')
            ->where('todos.id', '=', $this->id)
            ->select('users.*')
            ->get();
    }

    public function project(): HasOneThrough
    {
        return $this->hasOneThrough(Project::class, projectTodo::class, 'todo_id', 'id', 'id', 'project_id');
    }


    public function pomo(): HasMany
    {
        return $this->hasMany(Pomo::class);
    }
}
