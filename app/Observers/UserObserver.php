<?php

namespace App\Observers;

use App\Models\User;
use App\Services\FingerprintService;

class UserObserver
{
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
