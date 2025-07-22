<?php

namespace App\Policies;

use App\Models\Defender;
use App\Models\User;
use App\Services\AuthenticationService;

class DefenderPolicy
{
    private function getResource(User $user, string $action)
    {
        return AuthenticationService::can($user, 'defender', $action);
    }

    /**
     * Determine whether the user can use all models.
     */
    public function all(User $user): bool
    {
        return $this->getResource($user, 'all');
    }

    private function operate(User $user, Defender $defender, string $action): bool
    {
        if ($defender->important && !$user->important)
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
    public function view(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'view');
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
    public function update(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'update');
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
    public function delete(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'delete');
    }

    /**
     * Determine whether the user can health check the Defender.
     */
    public function health(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'health');
    }

    /**
     * Determine whether the user can sync data from the Defender.
     */
    public function sync(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'sync');
    }

    /**
     * Determine whether the user can apply data from the Defender.
     */
    public function apply(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'apply');
    }

    /**
     * Determine whether the user can revoke data from the Defender.
     */
    public function revoke(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'revoke');
    }

    /**
     * Determine whether the user can implement data from the Defender.
     */
    public function implement(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'implement');
    }

    /**
     * Determine whether the user can suspend data from the Defender.
     */
    public function suspend(User $user, Defender $defender): bool
    {
        return $this->operate($user, $defender,'suspend');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Defender $defender): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Defender $defender): bool
    {
        return false;
    }
}
