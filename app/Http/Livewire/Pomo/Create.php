<?php

namespace App\Http\Livewire\Pomo;

use App\Models\Pomo;
use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Collection;

class Create extends Component
{
    public $user;
    public $projects;
    public $incomplete_todos;
    public Pomo $pomo;
    public $editing = false;

    public function mount(Pomo $pomo = null, $editing = false)
    {
        $this->user = User::find(auth()->id());
        $this->projects = $this->user->projects;
        $this->load_incomplete_todos(null);
        $this->pomo = $pomo;
        $this->editing = $editing;
    }

    public function load_incomplete_todos($project_id = null, $editing = false)
    {
        $incomplete_todos = new Collection();

        foreach ($this->projects as $project) {
            $todos = $project->todos()->where('completed_at', null);
            if ($project_id) {
                $todos = $todos->where('project_id', $project_id);
            }
            $incomplete_todos = $incomplete_todos->merge($todos->get());
        }
        $this->incomplete_todos = $incomplete_todos;
    }

    public function render()
    {
        return view('livewire.pomo.create', [
            'projects' => $this->projects,
            'incomplete_todos' => $this->incomplete_todos,
            'pomo' => $this->pomo,
            'editing' => $this->editing,
        ]);
    }
}
