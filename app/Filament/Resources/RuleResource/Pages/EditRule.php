<?php

namespace App\Filament\Resources\RuleResource\Pages;

use App\Filament\Resources\RuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
