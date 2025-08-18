<?php

namespace App\Filament\Resources\RuleResource\Pages;

use App\Filament\Resources\RuleResource;
use App\Models\Rule;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRule extends EditRecord
{
    protected static string $resource = RuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($data['action_configuration'])
        {
            if ($data['action'] == 'request')
            {
                $actionConfiguration = explode(',', $data['action_configuration']);
                $data['request_method'] = $actionConfiguration[0];
                $data['request_url'] = $actionConfiguration[1];
            }
            if ($data['action'] == 'setVariable')
            {
                $actionConfiguration = explode(',', $data['action_configuration']);
                $data['key_variable'] = $actionConfiguration[0];
                $data['value_variable'] = $actionConfiguration[1];
            }
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
