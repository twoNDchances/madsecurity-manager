<?php

namespace App\Filament\Resources\WordlistResource\Pages;

use App\Filament\Resources\WordlistResource;
use App\Models\Word;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditWordlist extends EditRecord
{
    protected static string $resource = WordlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'name' => $data['name'],
            'alias' => $data['alias'],
            'description' => $data['description'],
        ]);
        $words = array_filter(array_map(
            'trim',
            explode("\n", $data['content'])
        ));
        $record->words()->delete();
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
                    'wordlist_id' => $record->id,
                ];
            }
            Word::insert($records);
        }
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
