<?php

namespace App\Http\Livewire\Pomo;

use Illuminate\View\View;
use Livewire\Component;

class Timer extends Component
{
    public int $time = 25 * 60;
    public bool $countdown = false;
    public bool $break = false;

    protected $listeners = [
        'tick' => 'tick',
        'endBreak' => 'endBreak',
    ];

    public function resetPomo()
    {
        $this->time = 25 * 60;
        $this->countdown = false;
        $this->break = false;
    }

    public function endBreak($time = null)
    {
        $this->time = $time ?? $this->time;
        $this->countdown = false;
        $this->break = false;
    }

    public function startTimer(): void
    {
        $this->countdown = true;
        $this->emit('timerStarted');
    }

    public function stopTimer(): void
    {
        $this->countdown = false;
    }

    public function tick(): void
    {
        if ($this->time > 0) {
            $this->time--;
        } else {
            $this->countdown = false;
            $this->break = true;
        }
    }

    public function mount($time = null): void
    {
        $this->time = session('time') ?? $time ?? $this->time;
        $this->countdown = false;
    }


    public function render(): View
    {
        // Cache time in session
        session(['time' => $this->time]);
        $this->time = session('time') ?? $this->time;


        return view('livewire.pomo.timer', [
            'time' => $this->time,
            'countdown' => $this->countdown,
            'break' => $this->break,
        ]);
    }
}
