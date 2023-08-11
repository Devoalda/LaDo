<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UuidTrait;

    protected $table = 'users';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, projectUser::class, 'user_id', 'project_id');
    }

    public function todos(): Collection
    {
        return DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('projects', 'project_todo.project_id', '=', 'projects.id')
            ->join('project_user', 'projects.id', '=', 'project_user.project_id')
            ->join('users', 'project_user.user_id', '=', 'users.id')
            ->where('users.id', '=', $this->id)
            ->select('todos.*')
            ->get();
    }

    public function pomos(): Collection
    {
        return DB::table('pomos')
            ->join('todos', 'pomos.todo_id', '=', 'todos.id')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('projects', 'project_todo.project_id', '=', 'projects.id')
            ->join('project_user', 'projects.id', '=', 'project_user.project_id')
            ->join('users', 'project_user.user_id', '=', 'users.id')
            ->where('users.id', '=', $this->id)
            ->select('pomos.*')
            ->get();
    }
}
