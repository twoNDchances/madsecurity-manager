<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

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
