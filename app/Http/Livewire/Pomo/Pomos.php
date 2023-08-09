<?php

namespace App\Http\Livewire\Pomo;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\{
    Pomo,
    Project,
    User,
    Todo
};

class Pomos extends Component
{
    public $perPage = 9;

    public $listeners = [
        'load-more' => 'loadMore',
    ];

    public function loadMore()
    {
        $this->perPage += 9;
    }


    public function render()
    {
        $user = User::find(auth()->id());

        $pomos = Pomo::whereHas('todo', function ($query) use ($user) {
            $query->whereHas('project', function ($query) use ($user) {
                $query->whereHas('user', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            });
        })->orderBy('pomo_start', 'desc')->paginate($this->perPage);

        return view('livewire.pomo.pomos', [
            'pomos' => $pomos,
        ]);
    }
}
