<?php

namespace App\Observers;

use App\Models\Wordlist;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class WordlistObserver
{
    /**
     * Handle the Wordlist "creating" event.
     */
    public function creating(Wordlist $wordlist): void
    {
        if ($wordlist::$skipObserver)
        {
            return;
        }
        $wordlist->user_id = IdentificationService::get()->id;
    }

    /**
     * Handle the Wordlist "created" event.
     */
    public function created(Wordlist $wordlist): void
    {
        FingerprintService::controlObserver($wordlist, 'Create');
    }

    /**
     * Handle the Wordlist "updated" event.
     */
    public function updated(Wordlist $wordlist): void
    {
        FingerprintService::controlObserver($wordlist, 'Update');
    }

    /**
     * Handle the Wordlist "deleted" event.
     */
    public function deleted(Wordlist $wordlist): void
    {
        FingerprintService::controlObserver($wordlist, 'Delete');
    }
}
