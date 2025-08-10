<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use Filament\Resources\Pages\ListRecords as ListPage;

class ListRecords extends ListPage
{
    protected static string $resource = RecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
