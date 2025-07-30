<?php

namespace App\Observers;

use App\Models\Policy;
use App\Services\FingerprintService;

class PolicyObserver
{
    /**
     * Handle the Policy "created" event.
     */
    public function created(Policy $policy): void
    {
        FingerprintService::controlObserver($policy, 'Create');
    }

    /**
     * Handle the Policy "updated" event.
     */
    public function updated(Policy $policy): void
    {
        FingerprintService::controlObserver($policy, 'Update');
    }

    /**
     * Handle the Policy "deleted" event.
     */
    public function deleted(Policy $policy): void
    {
        FingerprintService::controlObserver($policy, 'Delete');
    }
}
