<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Pomo;
use App\Models\User;
use Livewire\Component;

class PomoCount extends Component
{
    public int $ave_pomo_count = 0;

    public function mount(){
        $user = User::find(auth()->user()->id);
        $todos = $user->projects->map(function ($project) {
            return $project->todos;
        })->flatten();

//        $todos = $user->todos()->map(function ($todo) {
//            $todo = \App\Models\Todo::find($todo->id);
//            $todo->pomos = Pomo::where('todo_id', $todo->id);
//            return $todo;
//        });

        // Get the average pomo count per todo
        $ave_pomo_count = 0;
        foreach ($todos as $todo) {
            $ave_pomo_count += $todo->pomo->count();
        }
        $ave_pomo_count = $ave_pomo_count / $todos->count();

        $this->ave_pomo_count = $ave_pomo_count;
    }

    public function render()
    {
        return view('livewire.dashboard.pomo-count', [
            'ave_pomo_count' => $this->ave_pomo_count,
        ]);
    }
}
