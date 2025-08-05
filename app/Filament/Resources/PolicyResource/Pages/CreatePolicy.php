<?php

namespace App\Filament\Resources\PolicyResource\Pages;

use App\Filament\Resources\PolicyResource;
use App\Services\IdentificationService;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicy extends CreateRecord
{
    protected static string $resource = PolicyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
