<?php

namespace App\Observers;

use App\Models\Defender;
use App\Services\FingerprintService;
use App\Services\IdentificationService;
use Illuminate\Support\Facades\Storage;

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
     * Handle the Defender "updating" event.
     */
    public function updating(Defender $defender): void
    {
        if ($defender::$skipObserver)
        {
            return;
        }
        if ($defender->isDirty('certification'))
        {
            $oldCertificationPath = $defender->getOriginal('certification');
            if ($oldCertificationPath && Storage::disk('local')->exists($oldCertificationPath))
            {
                Storage::disk('local')->delete($oldCertificationPath);
            }
        }
    }

    /**
     * Handle the Defender "updated" event.
     */
    public function updated(Defender $defender): void
    {
        FingerprintService::controlObserver($defender, 'Update');
    }

    /**
     * Handle the Defender "deleting" event.
     */
    public function deleting(Defender $defender): void
    {
        if ($defender::$skipObserver)
        {
            return;
        }
        if ($defender->certification && Storage::disk('local')->exists($defender->certification))
        {
            Storage::disk('local')->delete($defender->certification);
        }
    }

    /**
     * Handle the Defender "deleted" event.
     */
    public function deleted(Defender $defender): void
    {
        FingerprintService::controlObserver($defender, 'Delete');
    }
}
