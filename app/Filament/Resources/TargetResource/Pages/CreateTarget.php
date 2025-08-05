<?php

namespace App\Filament\Resources\TargetResource\Pages;

use App\Filament\Resources\TargetResource;
use App\Services\IdentificationService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTarget extends CreateRecord
{
    protected static string $resource = TargetResource::class;

    // Complex Logic
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['final_datatype'] = match ($data['engine']) {
            'indexOf' => 'string',
            'length' => 'number',
            default => $data['datatype'],
        };
        $data['user_id'] = IdentificationService::get()?->id;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
