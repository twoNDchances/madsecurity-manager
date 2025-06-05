<?php

namespace App\Filament\Resources\ComposerResource\Pages;

use App\Filament\Resources\ComposerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComposer extends EditRecord
{
    protected static string $resource = ComposerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }
}
