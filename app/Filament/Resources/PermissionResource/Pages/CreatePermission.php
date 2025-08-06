<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

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
