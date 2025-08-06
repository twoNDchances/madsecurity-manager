<?php

namespace App\Filament\Resources\TokenResource\Pages;

use App\Filament\Resources\TokenResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateToken extends CreateRecord
{
    protected static string $resource = TokenResource::class;

    // Complex Logic
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['value'] = Hash::make($data['value']);
        return $data;
    }

    public static function callByStatic(array $data): Model
    {
        $form = (new static())->mutateFormDataBeforeCreate($data);
        return (new static())->handleRecordCreation($form);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
