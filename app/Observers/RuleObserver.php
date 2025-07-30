<?php

namespace App\Observers;

use App\Models\Rule;
use App\Services\FingerprintService;

class RuleObserver
{
    /**
     * Handle the Rule "created" event.
     */
    public function created(Rule $rule): void
    {
        FingerprintService::controlObserver($rule, 'Create');
    }

    /**
     * Handle the Rule "updated" event.
     */
    public function updated(Rule $rule): void
    {
        FingerprintService::controlObserver($rule, 'Update');
    }

    /**
     * Handle the Rule "deleted" event.
     */
    public function deleted(Rule $rule): void
    {
        FingerprintService::controlObserver($rule, 'Delete');
    }
}
