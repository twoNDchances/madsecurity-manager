<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

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
