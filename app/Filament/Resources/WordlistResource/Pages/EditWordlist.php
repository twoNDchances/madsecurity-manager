<?php

namespace App\Filament\Resources\WordlistResource\Pages;

use App\Filament\Resources\WordlistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWordlist extends EditRecord
{
    protected static string $resource = WordlistResource::class;

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
