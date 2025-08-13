<?php

namespace App\Observers;

use App\Models\Asset;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class AssetObserver
{
    /**
     * Handle the Asset "creating" event.
     */
    public function creating(Asset $asset): void
    {
        if ($asset::$skipObserver)
        {
            return;
        }
        $asset->user_id = IdentificationService::get()->id;
    }

    /**
     * Handle the Asset "created" event.
     */
    public function created(Asset $asset): void
    {
        FingerprintService::controlObserver($asset, 'Create');
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        FingerprintService::controlObserver($asset, 'Update');
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        FingerprintService::controlObserver($asset, 'Delete');
    }
}
