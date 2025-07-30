<?php

namespace App\Filament\Resources\DecisionResource\Pages;

use App\Filament\Resources\DecisionResource;
use App\Services\FingerprintService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDecision extends ViewRecord
{
    protected static string $resource = DecisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->icon('heroicon-o-pencil-square'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        FingerprintService::generate($this->record, 'View');
        return $data;
    }
}
