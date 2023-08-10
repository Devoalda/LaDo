<?php

namespace App\Http\Livewire\Todo;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Todo;

class TodaysTodo extends Component
{
    public $perPage = 5;

    public $listeners = [
        'load-more' => 'loadMore',
    ];

    public function loadMore()
    {
        $this->perPage += 5;
    }

    public function render()
    {
        $todos = DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('project_user', 'project_todo.project_id', '=', 'project_user.project_id')
            ->where('project_user.user_id', '=', auth()->user()->id)
            ->whereDate('due_end', '<=', strtotime('today midnight'))
            ->whereNull('completed_at')
            ->orderBy('due_end', 'asc')
            ->paginate($this->perPage);


        $todos->transform(function ($todo) {
            return Todo::find($todo->id);
        });


        $incomplete_count = DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('project_user', 'project_todo.project_id', '=', 'project_user.project_id')
            ->where('project_user.user_id', '=', auth()->user()->id)
            ->whereDate('due_end', '<=', strtotime('today midnight'))
            ->whereNull('completed_at')
            ->count();

        return view('livewire.todo.todays-todo', [
            'todos' => $todos,
            'incomplete_count' => $incomplete_count,
        ]);
    }
}
