<?php

namespace App\Services;

use App\Models\Defender;

class DefenderSyncService
{
    public static function perform(Defender $defender): Defender
    {
        return $defender;
    }
}
