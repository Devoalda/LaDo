<?php

namespace App\Http\Livewire\Pomo;

use Livewire\Component;
use App\Models\{
    Pomo,
    Project,
    User,
    Todo
};

class Pomos extends Component
{
    public function render()
    {
        $user = User::find(auth()->id());
        $pomos = $user->pomos();

        // Convert Pomos from Collection to class
        $pomos = $pomos->map(function ($pomo) {
            $pomo->todo = Todo::find($pomo->todo_id);
            $pomo->project = Project::find($pomo->todo->project_id);
            return $pomo;
        });


        return view('livewire.pomo.pomos', [
            'pomos' => $pomos,
        ]);
    }
}
