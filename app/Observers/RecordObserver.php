<?php

namespace App\Observers;

use App\Models\Record;
use App\Services\FingerprintService;

class RecordObserver
{
    /**
     * Handle the Record "created" event.
     */
    public function created(Record $record): void
    {
        FingerprintService::controlObserver($record, 'Create');
    }

    /**
     * Handle the Record "deleted" event.
     */
    public function deleted(Record $record): void
    {
        FingerprintService::controlObserver($record, 'Delete');
    }
}
