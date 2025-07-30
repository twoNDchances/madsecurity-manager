<?php

namespace App\Observers;

use App\Models\Permission;
use App\Services\FingerprintService;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(Permission $permission): void
    {
        FingerprintService::controlObserver($permission, 'Create');
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updated(Permission $permission): void
    {
        FingerprintService::controlObserver($permission, 'Update');
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleted(Permission $permission): void
    {
        FingerprintService::controlObserver($permission, 'Delete');
    }
}
