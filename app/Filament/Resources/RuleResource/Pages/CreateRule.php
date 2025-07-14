<?php

namespace App\Filament\Resources\RuleResource\Pages;

use App\Filament\Resources\RuleResource;
use App\Services\AuthenticationService;
use Filament\Resources\Pages\CreateRecord;

class CreateRule extends CreateRecord
{
    protected static string $resource = RuleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = AuthenticationService::get()?->id;
        if ($data['comparator'] == '@inRange')
        {
            $data['value'] = implode(',', [$data['from'], $data['to']]);
        }
        if ($data['action'])
        {
            $data['action_configuration'] = match ($data['action'])
            {
                'request' => implode(',', [$data['request_method'], $data['request_url']]),
                'setVariable' => implode(',', [$data['key_variable'], $data['value_variable']]),
                default => $data['action_configuration'] ?? null,
            };
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
