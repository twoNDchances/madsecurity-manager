<?php

namespace App\Observers;

use App\Models\Word;
use App\Models\Wordlist;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class WordlistObserver
{
    /**
     * Handle the Decision "creating" event.
     */
    public function creating(Wordlist $wordlist): void
    {
        if ($wordlist::$skipObserver)
        {
            return;
        }
        $wordlist->user_id = IdentificationService::get()->id;
        dd($wordlist);
    }

    /**
     * Handle the Wordlist "created" event.
     */
    public function created(Wordlist $wordlist): void
    {
        FingerprintService::controlObserver($wordlist, 'Create');
        $batchSize = 10000;
        $chunked = array_chunk(
            array_filter(array_map(
                'trim',
                explode("\n", $wordlist->temporary)
            )),
            $batchSize,
        );
        $now = now();
        foreach ($chunked as $chunk)
        {
            $records = [];
            foreach ($chunk as $line)
            {
                $records[] = [
                    'content' => $line,
                    'wordlist_id' => $wordlist->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            Word::insert($records);
        }
        $wordlist::$skipObserver = true;
        $wordlist->update(['temporary' => null]);
        $wordlist::$skipObserver = false;
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
