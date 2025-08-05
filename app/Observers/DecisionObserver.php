<?php

namespace App\Observers;

use App\Models\Decision;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class DecisionObserver
{
    /**
     * Handle the Decision "creating" event.
     */
    public function creating(Decision $decision): void
    {
        if ($decision::$skipObserver)
        {
            return;
        }
        $decision->user_id = IdentificationService::get()->id;
        if ($decision->action == 'kill')
        {
            // $decision->action_configuration = implode(',', [$data['kill_header'], $data['kill_path']]);
        }
    }

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
