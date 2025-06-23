<?php

namespace App\Filament\Resources\DefenderResource\Pages;

use App\Filament\Resources\DefenderResource;
use App\Services\AuthenticationService;
use Filament\Resources\Pages\CreateRecord;

class CreateDefender extends CreateRecord
{
    protected static string $resource = DefenderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = AuthenticationService::get()?->id;
        return $data;
    }
}
