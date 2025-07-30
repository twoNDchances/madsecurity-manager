<?php

namespace App\Observers;

use App\Models\Group;
use App\Services\FingerprintService;

class GroupObserver
{
    /**
     * Handle the Group "created" event.
     */
    public function created(Group $group): void
    {
        FingerprintService::controlObserver($group, 'Create');
    }

    /**
     * Handle the Group "updated" event.
     */
    public function updated(Group $group): void
    {
        FingerprintService::controlObserver($group, 'Update');
    }

    /**
     * Handle the Group "deleted" event.
     */
    public function deleted(Group $group): void
    {
        FingerprintService::controlObserver($group, 'Delete');
    }
}
