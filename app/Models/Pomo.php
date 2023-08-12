<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class Pomo extends Model
{
    use HasFactory, UuidTrait;

    protected $dateFormat = 'U';
    protected $table = 'pomos';

    protected $fillable = [
        'todo_id',
        'notes',
        'pomo_start',
        'pomo_end',
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }


    public function user(): Collection
    {
        return DB::table('users')
            ->join('project_user', 'users.id', '=', 'project_user.user_id')
            ->join('projects', 'project_user.project_id', '=', 'projects.id')
            ->join('project_todo', 'projects.id', '=', 'project_todo.project_id')
            ->join('todos', 'project_todo.todo_id', '=', 'todos.id')
            ->join('pomos', 'todos.id', '=', 'pomos.todo_id')
            ->where('pomos.id', '=', $this->id)
            ->select('users.*')
            ->get();
    }


}
