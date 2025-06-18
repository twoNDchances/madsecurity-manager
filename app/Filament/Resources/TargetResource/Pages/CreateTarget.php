<?php

namespace App\Filament\Resources\TargetResource\Pages;

use App\Filament\Resources\TargetResource;
use App\Services\AuthenticationService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTarget extends CreateRecord
{
    protected static string $resource = TargetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['final_datatype'] = match ($data['engine']) {
            'indexOf' => 'string',
            'length' => 'number',
            default => $data['datatype'],
        };
        $data['user_id'] = AuthenticationService::get()?->id;
        return $data;
    }

    public static function callByStatic(array $data)
    {
        $mutater = (new static())->mutateFormDataBeforeCreate($data);
        return (new static())->handleRecordCreation($mutater);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
