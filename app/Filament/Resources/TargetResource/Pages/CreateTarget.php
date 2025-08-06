<?php

namespace App\Filament\Resources\TargetResource\Pages;

use App\Filament\Resources\TargetResource;
use App\Services\IdentificationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTarget extends CreateRecord
{
    protected static string $resource = TargetResource::class;

    // Complex Logic
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['final_datatype'] = match ($data['engine']) {
            'indexOf' => 'string',
            'length' => 'number',
            default => $data['datatype'],
        };
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
