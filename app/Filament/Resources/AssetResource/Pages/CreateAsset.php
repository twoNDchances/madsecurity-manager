<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Services\AssetGeneratorService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Yaml\Yaml;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    private function processGeneral(array $assets, string $name, callable $callback, array &$result)
    {
        if (isset($assets[$name]) && $assets[$name] != null)
        {
            if (isset($result[$name]))
            {
                $result[$name] = [];
            }
            foreach ($assets[$name] as $asset)
            {
                $result[$name][] = $callback($asset);
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $assets = Yaml::parse(Storage::get($data['path']));
        $assetNames = [
            'decisions' => [AssetGeneratorService::class, 'generateDecision'],
            'defenders' => [AssetGeneratorService::class, 'generateDefender'],
            'groups' => [AssetGeneratorService::class, 'generateGroup'],
            'permissions' => [AssetGeneratorService::class, 'generatePermission'],
            'policies' => [AssetGeneratorService::class, 'generatePolicy'],
            'rules' => [AssetGeneratorService::class, 'generateRule'],
            'tags' => [AssetGeneratorService::class, 'generateTag'],
            'targets' => [AssetGeneratorService::class, 'generateTarget'],
            'users' => [AssetGeneratorService::class, 'generateUser'],
            'wordlists' => [AssetGeneratorService::class, 'generateWordlist'],
        ];
        $result = [];
        foreach ($assetNames as $assetName => $assetCallback)
        {
            $this->processGeneral($assets, $assetName, $assetCallback, $result);
        }
        dd($result);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
