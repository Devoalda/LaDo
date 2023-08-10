<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IncompleteTodo extends Component
{
    public $incomplete_count;

    public function mount()
    {
        $this->incomplete_count = DB::table('todos')
            ->join('project_todo', 'todos.id', '=', 'project_todo.todo_id')
            ->join('project_user', 'project_todo.project_id', '=', 'project_user.project_id')
            ->where('project_user.user_id', '=', Auth::user()->id)
            ->whereDate('due_end', '<=', strtotime('today midnight'))
            ->whereNull('completed_at')
            ->count();

    }
    public function render()
    {
        return view('livewire.dashboard.incomplete-todo');
    }
}
