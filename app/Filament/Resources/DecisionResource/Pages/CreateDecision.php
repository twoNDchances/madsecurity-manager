<?php

namespace App\Filament\Resources\DecisionResource\Pages;

use App\Filament\Resources\DecisionResource;
use App\Services\AuthenticationService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDecision extends CreateRecord
{
    protected static string $resource = DecisionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = AuthenticationService::get()?->id;
        if ($data['action'] == 'kill')
        {
            $data['action_configuration'] = implode(',', [$data['kill_header'], $data['kill_path']]);
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
