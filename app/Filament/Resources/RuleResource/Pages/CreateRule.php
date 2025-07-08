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
        if ($data['comparator'] == '@setVariable')
        {
            $data['value'] = implode(',', [$data['key_variable'], $data['value_variable']]);
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
