<?php

namespace App\Observers;

use App\Models\Target;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class TargetObserver
{
    /**
     * Handle the Target "creating" event.
     */
    public function creating(Target $target): void
    {
        if ($target::$skipObserver)
        {
            return;
        }
        $target->user_id = IdentificationService::get()->id;
    }

    /**
     * Handle the Target "created" event.
     */
    public function created(Target $target): void
    {
        FingerprintService::controlObserver($target, 'Create');
    }

    /**
     * Handle the Target "updated" event.
     */
    public function updated(Target $target): void
    {
        FingerprintService::controlObserver($target, 'Update');
    }

    /**
     * Handle the Target "deleted" event.
     */
    public function deleted(Target $target): void
    {
        FingerprintService::controlObserver($target, 'Delete');
    }
}
