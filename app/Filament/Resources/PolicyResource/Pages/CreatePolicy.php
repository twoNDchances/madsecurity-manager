<?php

namespace App\Filament\Resources\PolicyResource\Pages;

use App\Filament\Resources\PolicyResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicy extends CreateRecord
{
    protected static string $resource = PolicyResource::class;

    // public static function callByStatic(array $data): Model
    // {
    //     $form = (new static())->mutateFormDataBeforeCreate($data);
    //     return (new static())->handleRecordCreation($form);
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
