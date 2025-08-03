<?php

namespace App\Http\Controllers;

use App\Models\Decision;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\DecisionValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DecisionController extends Controller
{
    private function relationships($user)
    {
        return [
            'defender' => [
                'defenders' => function($query) use ($user)
                {
                    if (!$user->important)
                    {
                        $query = $query->where('important', false);
                    }
                    return $query;
                },
            ],
            'wordlist' => 'getWordlist',
            'tag' => 'tags',
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
        ];
    }

    public function list(Request $request)
    {
        $decisions = Decision::query();
        if ($request->boolean('all'))
        {
            return $decisions->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $decisions->paginate($pageSize);
    }

    public function show($id)
    {
        $decision = Decision::findOrFail($id);
        IdentificationService::load(
            $decision,
            $this->relationships(IdentificationService::get()),
        );
        return $decision;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), DecisionValidator::build($request));
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
        $decision = Decision::create($validated);
        if (isset($validated['defender_ids']))
        {
            $decision->defenders()->sync($validated['defender_ids']);
        }
        TagFieldService::syncTags($validated, $decision);
        IdentificationService::load($decision, $this->relationships($user));
        return $decision;
    }

    public function update(Request $request, $id)
    {
        $decision = Decision::findOrFail($id);
        $validator = Validator::make($request->all(), DecisionValidator::build(
            $request,
            false,
            $decision->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $decision->update($validated);
        if (isset($validated['defender_ids']))
        {
            $decision->defenders()->sync($validated['defender_ids']);
        }
        TagFieldService::syncTags($validated, $decision);
        IdentificationService::load(
            $decision,
            $this->relationships(IdentificationService::get()),
        );
        return $decision;
    }

    public function delete($id)
    {
        $decision = Decision::findOrFail($id);
        $decision->delete();
        return response()->json([
            'message' => "Decision $decision->id deleted"
        ]);
    }
}
