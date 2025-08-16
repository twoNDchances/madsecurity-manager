<?php

namespace App\Services;

use App\Models\Word;
use App\Models\Wordlist;
use App\Validators\API\UserValidator;
use App\Validators\API\WordlistValidator;
use Illuminate\Support\Facades\Validator;

class AssetGeneratorService
{
    public static function generateTarget(array $data)
    {
        // $validator = Validator::make();
    }

    public static function generateUser(array $data)
    {
        $validator = Validator::make($data, UserValidator::build());
    }

    public static function generateWordlist(array $data): array
    {
        $validator = Validator::make($data, WordlistValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
        $wordlist = Wordlist::create($validated);
        if (isset($validated['words']))
        {
            $words = array_chunk($validated['words'], 10000);
            $now = now();
            foreach ($words as $content)
            {
                $records = [];
                foreach ($content as $line)
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
        }
        TagFieldService::syncTags($validated, $wordlist);
        return [
            'status' => true,
            'errors' => [],
        ];
    }
}
