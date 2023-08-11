<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Todo,
    Project,
    User
};


class AveTodoPerProject extends Component
{
    public $ave_todo_count;

    public function mount()
    {
        $user = auth()->user();
        $projects = $user->projects;
        $project_count = $projects->count();

        if ($project_count === 0) {
            $this->ave_todo_count = 0;
            return;
        }
        // Average number of todos per project
        $ave_todo_count = function ($projects) {
            $todo_count = 0;
            foreach ($projects as $project) {
                $todo_count += $project->todos->count();
            }

            return $todo_count / $projects->count();
        };

        $this->ave_todo_count = $ave_todo_count($projects);
    }

    public function render()
    {
        return view('livewire.dashboard.ave-todo-per-project');
    }
}
