<?php

namespace App\Observers;

use App\Models\Decision;
use App\Services\FingerprintService;

class DecisionObserver
{
    /**
     * Handle the Decision "created" event.
     */
    public function created(Decision $decision): void
    {
        FingerprintService::controlObserver($decision, 'Create');
    }

    /**
     * Handle the Decision "updated" event.
     */
    public function updated(Decision $decision): void
    {
        FingerprintService::controlObserver($decision, 'Update');
    }

    /**
     * Handle the Decision "deleted" event.
     */
    public function deleted(Decision $decision): void
    {
        FingerprintService::controlObserver($decision, 'Delete');
    }
}
