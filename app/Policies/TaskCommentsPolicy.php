<?php

namespace App\Policies;

use App\Models\TaskComments;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskCommentsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
         return $user->hasRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskComments $taskComments)
    {
         return $user->hasRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
         return $user->hasRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskComments $taskComments)
    {
         return $user->hasRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskComments $taskComments)
    {
         return $user->hasRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskComments $taskComments)
    {
         return $user->hasRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskComments $taskComments)
    {
         return $user->hasRole(['Admin', 'Manager', 'Staff']);
    }
}
