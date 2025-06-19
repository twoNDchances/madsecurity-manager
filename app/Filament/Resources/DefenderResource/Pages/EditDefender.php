<?php

namespace App\Filament\Resources\DefenderResource\Pages;

use App\Filament\Resources\DefenderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDefender extends EditRecord
{
    protected static string $resource = DefenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
