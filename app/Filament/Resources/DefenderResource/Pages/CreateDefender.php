<?php

namespace App\Filament\Resources\DefenderResource\Pages;

use App\Filament\Resources\DefenderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDefender extends CreateRecord
{
    protected static string $resource = DefenderResource::class;

    // public static function callByStatic(array $data): Model
    // {
    //     $form = (new static())->mutateFormDataBeforeCreate($data);
    //     return (new static())->handleRecordCreation($form);
    // }
}
