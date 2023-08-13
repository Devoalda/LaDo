<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TodoPolicy
{
    use HandlesAuthorization;

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
    public function view(User $user, Project $project, Todo $todo): bool
    {
        // Check if user is owner of project and todo
        return $project->user->contains('id', $user->id) && $todo->projects->contains('id', $project->id);
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
    public function update(User $user, Todo $todo): bool
    {
        $project = $todo->projects->first();

        return $project->user->contains('id', $user->id) && $todo->projects->contains('id', $project->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Todo $todo): bool
    {
        $project = $todo->projects->first();

        return $project->user->contains('id', $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Todo $todo): bool
    {
        $project = $todo->projects->first();

        return $project->user->contains('id', $user->id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Todo $todo): bool
    {
        $project = $todo->projects->first();

        return $project->user->contains('id', $user->id);
    }
}
