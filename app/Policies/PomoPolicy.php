<?php

namespace App\Policies;

use App\Models\Pomo;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;

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
        return true;
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
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pomo $pomo): bool
    {
        // TODO: Fix this policy
        // Search for pomo user through pomos->todo->project_todo->project_user->user
//        $PomoUser = $pomo->todo->project;

        // Check if the user is the owner of the pomo
//        return $user->id === $PomoUser[0]->id;
        return true;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pomo $pomo): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pomo $pomo): bool
    {
        return true;
    }
}
