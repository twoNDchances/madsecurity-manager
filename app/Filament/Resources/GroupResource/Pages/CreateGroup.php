<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use App\Services\AuthenticationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = AuthenticationService::get()?->id;
        $data['status'] = false;
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
