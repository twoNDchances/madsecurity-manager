<?php

namespace App\Filament\Resources\RuleResource\Pages;

use App\Filament\Resources\RuleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRule extends CreateRecord
{
    protected static string $resource = RuleResource::class;

    // Complex Logic
    protected function mutateFormDataBeforeCreate(array $data): array
    {
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

    public static function callByStatic(array $data): Model
    {
        $form = (new static())->mutateFormDataBeforeCreate($data);
        return (new static())->handleRecordCreation($form);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
