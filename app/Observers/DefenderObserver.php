<?php

namespace App\Observers;

use App\Models\Defender;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class DefenderObserver
{
    /**
     * Handle the Defender "creating" event.
     */
    public function creating(Defender $defender): void
    {
        if ($defender::$skipObserver)
        {
            return;
        }
        $defender->user_id = IdentificationService::get()->id;
    }

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
