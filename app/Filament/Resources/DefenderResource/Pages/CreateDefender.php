<?php

namespace App\Filament\Resources\DefenderResource\Pages;

use App\Filament\Resources\DefenderResource;
use App\Services\IdentificationService;
use Filament\Resources\Pages\CreateRecord;

class CreateDefender extends CreateRecord
{
    protected static string $resource = DefenderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = IdentificationService::get()?->id;
        return $data;
    }
}
