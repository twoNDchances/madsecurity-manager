<?php

namespace App\Filament\Resources\WordlistResource\Pages;

use App\Filament\Resources\WordlistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWordlists extends ListRecords
{
    protected static string $resource = WordlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-o-plus'),
        ];
    }
}
