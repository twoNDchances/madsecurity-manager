<?php

namespace App\Observers;

use App\Models\Report;
use App\Services\FingerprintService;

class ReportObserver
{
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
