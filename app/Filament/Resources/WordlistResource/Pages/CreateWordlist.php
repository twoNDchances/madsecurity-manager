<?php

namespace App\Filament\Resources\WordlistResource\Pages;

use App\Filament\Resources\WordlistResource;
use App\Models\Word;
use App\Services\AuthenticationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateWordlist extends CreateRecord
{
    protected static string $resource = WordlistResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $wordlist = static::getModel()::create([
            'name' => $data['name'],
            'alias' => $data['alias'],
            'description' => $data['description'],
            'user_id' => AuthenticationService::get()?->id,
        ]);
        $words = array_filter(array_map(
            'trim',
            explode("\n", $data['content'])
        ));
        $now = now();
        $batchSize = 10000;
        $chunks = array_chunk($words, $batchSize);
        foreach ($chunks as $chunk)
        {
            $records = [];
            foreach ($chunk as $line)
            {
                $records[] = [
                    'content' => $line,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'wordlist_id' => $wordlist->id,
                ];
            }
            Word::insert($records);
        }
        return $wordlist;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
