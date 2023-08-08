<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use App\Models\{
    User,
    Project,
    Todo
};

class PomoTime extends Component
{
    public int $ave_pomo_time = 0;

    public function mount()
    {
        $user = User::find(auth()->user()->id);

        // Get all pomos and calculate the average time spent per todo (due_end - due_start)/count/total pomos
        $pomos = $user->pomos();
        $pomos = $pomos->map(function ($pomo) {
            $pomo->todo = Todo::find($pomo->todo_id);
            $pomo->project = Project::find($pomo->todo->project_id);
            return $pomo;
        });

        $total_pomos = $pomos->count();

        $total_time = 0;

        foreach ($pomos as $pomo) {
            $total_time += $pomo->pomo_end - $pomo->pomo_start;
        }

        $this->ave_pomo_time = $total_time / $total_pomos;

    }


    public function render()
    {
        return view('livewire.dashboard.pomo-time', [
            'ave_pomo_time' => $this->ave_pomo_time,
        ]);
    }
}
