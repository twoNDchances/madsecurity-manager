<?php

namespace App\Observers;

use App\Models\Wordlist;
use App\Services\FingerprintService;

class WordlistObserver
{
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
