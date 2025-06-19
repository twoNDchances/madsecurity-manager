<?php

namespace App\Filament\Resources\DefenderResource\Pages;

use App\Filament\Resources\DefenderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDefenders extends ListRecords
{
    protected static string $resource = DefenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-o-plus'),
        ];
    }
}
