<?php

namespace App\Observers;

use App\Models\User;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class UserObserver
{
    /**
     * Handle the Decision "creating" event.
     */
    public function creating(User $user): void
    {
        if ($user::$skipObserver)
        {
            return;
        }
        $user->user_id = IdentificationService::get()->id;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        FingerprintService::controlObserver($user, 'Create');
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        FingerprintService::controlObserver($user, 'Update');
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        FingerprintService::controlObserver($user, 'Delete');
    }
}
