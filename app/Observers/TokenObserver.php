<?php

namespace App\Observers;

use App\Models\Token;
use App\Services\FingerprintService;
use App\Services\IdentificationService;

class TokenObserver
{
    /**
     * Handle the Decision "creating" event.
     */
    public function creating(Token $token): void
    {
        if ($token::$skipObserver)
        {
            return;
        }
        $token->user_id = IdentificationService::get()->id;
    }

    /**
     * Handle the Token "created" event.
     */
    public function created(Token $token): void
    {
        FingerprintService::controlObserver($token, 'Create');
    }

    /**
     * Handle the Token "updated" event.
     */
    public function updated(Token $token): void
    {
        FingerprintService::controlObserver($token, 'Update');
    }

    /**
     * Handle the Token "deleted" event.
     */
    public function deleted(Token $token): void
    {
        FingerprintService::controlObserver($token, 'Delete');
    }
}
