<?php

namespace App\Filament\Resources\PolicyResource\Pages;

use App\Filament\Resources\PolicyResource;
use App\Services\AuthenticationService;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicy extends CreateRecord
{
    protected static string $resource = PolicyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = AuthenticationService::get()?->id;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
