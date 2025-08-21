<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Services\AssetGeneratorService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected $listeners = ['refreshAssetForm' => 'reloadForm'];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $result = AssetGeneratorService::perform(Storage::get($data['path']));
        $data['output'] = json_encode($result['result'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $data['total_asset'] = $result['total_asset'];
        $data['total_resource'] = $result['total_resource'];
        $data['fail_resource'] = $result['fail_resource'];
        return $data;
    }

    public function reloadForm(): void
    {
        $this->mutateFormDataBeforeFill($this->data);
        $this->fillForm();
    }

    protected function afterSave(): void
    {
        $this->dispatch('refreshAssetForm');
    }
}
