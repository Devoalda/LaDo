<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;

class ProjectCount extends Component
{
    public $project_count;

    public function mount()
    {
        $this->project_count = auth()->user()->projects()->count();
    }

    public function render()
    {
        return view('livewire.dashboard.project-count');
    }
}
