<?php

namespace App\Policies;

use App\Models\Pomo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PomoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pomo $pomo): bool
    {
        if (!$user)
        {
            return false;
        }
        return $user->id === $pomo->todo->project->user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pomo $pomo): bool
    {
        return $user->id === $pomo->todo->project->user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pomo $pomo): bool
    {
        // Check if the user is the owner of the pomo
        return $user->id === $pomo->todo->project->user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pomo $pomo): bool
    {
        return $user->id === $pomo->todo->project->user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pomo $pomo): bool
    {
        return $user->id === $pomo->todo->project->user->id;
    }
}
