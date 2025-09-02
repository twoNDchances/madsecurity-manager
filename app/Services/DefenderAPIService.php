<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DefenderAPIService
{
    public static function decodeBase64($base64)
    {
        if (is_string($base64) && str_contains($base64, 'base64,')) {
            $base64 = explode('base64,', $base64, 2)[1];
        }
        $pem = base64_decode($base64, true);
        $fileName = (string) Str::uuid() . '.crt';
        $relativePath = "tls/$fileName";
        Storage::disk('local')->put($relativePath, $pem);
        return $relativePath;
    }
}
