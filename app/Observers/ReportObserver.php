<?php

namespace App\Observers;

use App\Models\Report;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class ReportObserver
{
    /**
     * Handle the Policy "creating" event.
     */
    public function creating(Report $report): void
    {
        if ($report::$skipObserver)
        {
            return;
        }
        $report->user_id = IdentificationService::get()->id;
    }

    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        FingerprintService::controlObserver($report, 'Create');
    }

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        FingerprintService::controlObserver($report, 'Delete');
    }
}
