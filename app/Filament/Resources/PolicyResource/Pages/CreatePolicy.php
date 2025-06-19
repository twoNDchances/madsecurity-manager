<?php

namespace App\Filament\Resources\PolicyResource\Pages;

use App\Filament\Resources\PolicyResource;
use App\Services\AuthenticationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePolicy extends CreateRecord
{
    protected static string $resource = PolicyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = AuthenticationService::get()?->id;
        return $data;
    }

    public static function callByStatic(array $data): Model
    {
        $mutater = (new static())->mutateFormDataBeforeCreate($data);
        return (new static())->handleRecordCreation($mutater);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
