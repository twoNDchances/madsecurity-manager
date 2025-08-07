<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\IdentificationService;
use App\Validators\API\TagValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    private function relationships()
    {
        return [
            'decision' => 'decisions',
            'defender' => [
                'defenders' => IdentificationService::important(),
            ],
            'group' => 'groups',
            'permission' => 'permissions',
            'policy' => 'policies',
            'rule' => 'rules',
            'target' => 'targets',
            'token' => 'tokens',
            'user' => [
                'getOwner' => IdentificationService::important(),
                'users' => IdentificationService::important(),
            ],
            'wordlist' => 'wordlists',
        ];
    }

    public function list(Request $request)
    {
        $tags = Tag::query();
        if ($request->boolean('all'))
        {
            return $tags->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $tags->paginate($pageSize);
    }

    public function show($id)
    {
        $tag = Tag::findOrFail($id);
        IdentificationService::load($tag, $this->relationships());
        return $tag;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), TagValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $tag = Tag::create($validated);
        return $tag;
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $validator = Validator::make($request->all(), TagValidator::build(
            false,
            $tag->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $tag->update($validated);
        IdentificationService::load($tag, $this->relationships());
        return $tag;
    }

    public function delete($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return response()->json([
            'message'=> "Tag $tag->id deleted",
        ]);
    }
}
