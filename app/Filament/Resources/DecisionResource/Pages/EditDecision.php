<?php

namespace App\Filament\Resources\DecisionResource\Pages;

use App\Filament\Resources\DecisionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDecision extends EditRecord
{
    protected static string $resource = DecisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($data['action'] == 'kill')
        {
            $actionConfiguration = explode(',', $data['action_configuration']);
            $data['kill_header'] = $actionConfiguration[0];
            $data['kill_path'] = $actionConfiguration[1];
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (in_array($data['action'], ['deny', 'suspect', 'tag', 'warn', 'bait']))
        {
            $data['action_configuration'] = null;
        }
        if (in_array($data['action'], ['deny', 'suspect', 'redirect', 'kill']))
        {
            $data['wordlist_id'] = null;
        }
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
