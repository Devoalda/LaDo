<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Pomo;
use App\Models\Todo;
use App\Policies\PomoPolicy;
use App\Policies\TodoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Pomo::class => PomoPolicy::class,
        Todo::class => TodoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
