<?php

namespace App\Filament\Resources\WordlistResource\Pages;

use App\Filament\Resources\WordlistResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWordlist extends CreateRecord
{
    protected static string $resource = WordlistResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
