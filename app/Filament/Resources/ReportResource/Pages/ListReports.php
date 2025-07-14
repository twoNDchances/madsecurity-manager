<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Actions\ReportAction;
use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected $listeners = ['refreshReportTable' => '$refresh'];

    protected function getHeaderActions(): array
    {
        $action = ReportAction::class;
        return [
            $action::refresh(),
        ];
    }
}
