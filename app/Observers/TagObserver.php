<?php

namespace App\Observers;

use App\Models\Tag;
use App\Services\FingerprintService;

class TagObserver
{
    /**
     * Handle the Tag "created" event.
     */
    public function created(Tag $tag): void
    {
        FingerprintService::controlObserver($tag, 'Create');
    }

    /**
     * Handle the Tag "updated" event.
     */
    public function updated(Tag $tag): void
    {
        FingerprintService::controlObserver($tag, 'Update');
    }

    /**
     * Handle the Tag "deleted" event.
     */
    public function deleted(Tag $tag): void
    {
        FingerprintService::controlObserver($tag, 'Delete');
    }
}
