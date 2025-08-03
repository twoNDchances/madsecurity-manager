<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Models\Wordlist;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\WordlistValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WordlistController extends Controller
{
    private function relationships($user)
    {
        return [
            'target' => 'targets',
            'rule' => 'rules',
            'decision' => 'decisions',
            'user' => [
                'getOwner' => function($query) use ($user)
                {
                    if (!$user->important)
                    {
                        $query = $query->where('important', false);
                    }
                    return $query;
                },
            ],
            'tag' => 'tags',
        ];
    }

    public function list(Request $request)
    {
        $wordlists = Wordlist::query();
        if ($request->boolean('all'))
        {
            return $wordlists->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $wordlists->paginate($pageSize);
    }

    public function show($id)
    {
        $wordlist = Wordlist::findOrFail($id);
        IdentificationService::load(
            $wordlist,
            $this->relationships(IdentificationService::get()),
        );
        $wordlist->load('words');
        return $wordlist;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), WordlistValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $user = IdentificationService::get();
        $validated['user_id'] = $user->id;
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
        IdentificationService::load($wordlist, $this->relationships($user));
        $wordlist->load('words');
        return $wordlist;
    }

    public function update(Request $request, $id)
    {
        $wordlist = Wordlist::findOrFail($id);
        $validator = Validator::make($request->all(), WordlistValidator::build(
            false,
            $wordlist->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $wordlist->update($validated);
        if (isset($validated['words']))
        {
            if (!$request->boolean('append'))
            {
                $wordlist->words()->delete();
            }
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
        IdentificationService::load(
            $wordlist,
            $this->relationships(IdentificationService::get()),
        );
        $wordlist->load('words');
        return $wordlist;
    }

    public function delete($id)
    {
        $wordlist = Wordlist::findOrFail($id);
        $wordlist->delete();
        return response()->json([
            'message' => "Wordlist $wordlist->id deleted"
        ]);
    }
}
