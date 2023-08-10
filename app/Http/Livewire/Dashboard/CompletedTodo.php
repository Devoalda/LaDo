<?php

namespace App\Http\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;


class CompletedTodo extends Component
{
    public $todo_completed_count;

    public function mount()
    {
        $this->todo_completed_count = DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('project_user', 'project_todo.project_id', '=', 'project_user.project_id')
            ->where('project_user.user_id', '=', Auth::user()->id)
            ->whereDate('due_end', '<=', strtotime('today midnight'))
            ->whereNotNull('completed_at')
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.completed-todo');
    }
}
