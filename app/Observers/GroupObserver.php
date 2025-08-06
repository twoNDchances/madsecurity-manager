<?php

namespace App\Observers;

use App\Models\Group;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class GroupObserver
{
    /**Group "creating" event.
     */
    public function creating(Group $group): void
    {
        if ($group::$skipObserver)
        {
            return;
        }
        $group->user_id = IdentificationService::get()->id;
    }

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
