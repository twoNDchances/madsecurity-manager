<?php

namespace App\Filament\Resources\ComposerResource\Pages;

use App\Filament\Resources\ComposerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComposers extends ListRecords
{
    protected static string $resource = ComposerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-o-plus'),
        ];
    }
}
