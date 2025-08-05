<?php

namespace App\Filament\Resources\WordlistResource\Pages;

use App\Filament\Resources\WordlistResource;
use App\Models\Word;
use App\Services\IdentificationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateWordlist extends CreateRecord
{
    protected static string $resource = WordlistResource::class;

    // Complex Logic
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = IdentificationService::get()?->id;
        $words = array_filter(array_map(
            'trim',
            explode("\n", $data['content'])
        ));
        $batchSize = 10000;
        $data['content'] = array_chunk($words, $batchSize);
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $wordlist = static::getModel()::create([
            'name' => $data['name'],
            'alias' => $data['alias'],
            'description' => $data['description'],
            'user_id' => $data['user_id'],
        ]);
        $now = now();
        foreach ($data['content'] as $chunk)
        {
            $records = [];
            foreach ($chunk as $line)
            {
                $records[] = [
                    'content' => $line,
                    'wordlist_id' => $wordlist->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            Word::insert($records);
        }
        return $wordlist;
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
