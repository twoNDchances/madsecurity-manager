<?php

namespace App\Observers;

use App\Models\Tag;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class TagObserver
{
    /**
     * Handle the Decision "creating" event.
     */
    public function creating(Tag $tag): void
    {
        if ($tag::$skipObserver)
        {
            return;
        }
        $tag->user_id = IdentificationService::get()->id;
    }

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
