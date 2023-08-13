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
        $user = Auth::user();

        $this->incomplete_count = $user->projects->map(function ($project) {
            return $project->todos->where('completed_at', null)->count();
        })->sum();

    }
    public function render()
    {
        return view('livewire.dashboard.incomplete-todo');
    }
}
