<?php

namespace App\Policies;

use App\Models\User;
use App\Services\IdentificationService;

class UserPolicy
{
    private function getResource(User $user, string $action)
    {
        return IdentificationService::can($user, 'user', $action);
    }

    /**
     * Determine whether the user can use all models.
     */
    public function all(User $user): bool
    {
        return $this->getResource($user, 'all');
    }

    private function operate(User $user, User $model, string $action): bool
    {
        if ($model->important && !$user->important)
        {
            return false;
        }
        return $this->getResource($user, $action);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->getResource($user, 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $this->operate($user, $model,'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->getResource($user, 'create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $this->operate($user, $model,'update');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $this->getResource($user, 'deleteAny');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $this->operate($user, $model,'delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
