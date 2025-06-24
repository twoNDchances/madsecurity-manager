<?php

namespace App\Services;

use App\Models\Defender;

class DefenderApplyService
{
    public static function perform(Defender $defender): Defender
    {
        return $defender;
    }
}
