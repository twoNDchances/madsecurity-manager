<?php

namespace App\Observers;

use App\Models\Asset;
use App\Services\FingerprintService;
use App\Services\IdentificationService;
use Illuminate\Support\Facades\Storage;

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
     * Handle the Asset "updating" event.
     */
    public function updating(Asset $asset): void
    {
        if ($asset::$skipObserver)
        {
            return;
        }
        if ($asset->isDirty('path'))
        {
            $oldAssetPath = $asset->getOriginal('path');
            if ($oldAssetPath && Storage::disk('local')->exists($oldAssetPath))
            {
                Storage::disk('local')->delete($oldAssetPath);
            }
        }
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        FingerprintService::controlObserver($asset, 'Update');
    }

    /**
     * Handle the Asset "deleting" event.
     */
    public function deleting(Asset $asset): void
    {
        if ($asset::$skipObserver)
        {
            return;
        }
        if ($asset->path && Storage::disk('local')->exists($asset->path))
        {
            Storage::disk('local')->delete($asset->path);
        }
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        FingerprintService::controlObserver($asset, 'Delete');
    }
}
