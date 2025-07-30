<?php

namespace App\Filament\Resources\FingerprintResource\Pages;

use App\Filament\Resources\FingerprintResource;
use Filament\Resources\Pages\ListRecords;

class ListFingerprints extends ListRecords
{
    protected static string $resource = FingerprintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
