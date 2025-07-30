<?php

namespace App\Observers;

use App\Models\Defender;
use App\Services\FingerprintService;

class DefenderObserver
{
    /**
     * Handle the Defender "created" event.
     */
    public function created(Defender $defender): void
    {
        FingerprintService::controlObserver($defender, 'Create');
    }

    /**
     * Handle the Defender "updated" event.
     */
    public function updated(Defender $defender): void
    {
        FingerprintService::controlObserver($defender, 'Update');
    }

    /**
     * Handle the Defender "deleted" event.
     */
    public function deleted(Defender $defender): void
    {
        FingerprintService::controlObserver($defender, 'Delete');
    }
}
