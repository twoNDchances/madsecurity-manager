<?php

namespace App\Filament\Resources\DecisionResource\Pages;

use App\Filament\Resources\DecisionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDecision extends CreateRecord
{
    protected static string $resource = DecisionResource::class;

    // Complex Logic
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['action'] == 'kill')
        {
            $data['action_configuration'] = implode(',', [$data['kill_header'], $data['kill_path']]);
        }
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
