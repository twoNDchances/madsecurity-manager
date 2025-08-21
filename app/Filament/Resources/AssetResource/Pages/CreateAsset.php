<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Services\AssetGeneratorService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $result = AssetGeneratorService::perform(Storage::get($data['path']));
        $data['output'] = json_encode($result['result'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $data['total_asset'] = $result['total_asset'];
        $data['total_resource'] = $result['total_resource'];
        $data['fail_resource'] = $result['fail_resource'];
        return $data;
    }
}
