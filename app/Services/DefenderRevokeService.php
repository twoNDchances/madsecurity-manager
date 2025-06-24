<?php

namespace App\Services;

use App\Models\Defender;

class DefenderRevokeService
{
    public static function perform(Defender $defender): Defender
    {
        return $defender;
    }
}
