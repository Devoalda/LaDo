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
        $user = auth()->user();
        $this->todo_completed_count = $user->projects->map(function ($project) {
            return $project->todos->whereNotNull('completed_at')->count();
        })->sum();

    }

    public function render()
    {
        return view('livewire.dashboard.completed-todo');
    }
}
