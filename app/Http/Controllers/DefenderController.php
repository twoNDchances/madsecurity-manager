<?php

namespace App\Http\Controllers;

use App\Models\Defender;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\DefenderValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DefenderController extends Controller
{
    public function list(Request $request)
    {
        $user = IdentificationService::get();
        $defenders = Defender::query();
        if (!$user->important)
        {
            $defenders->where('important', false);
        }
        if ($request->boolean('all'))
        {
            return $defenders->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $defenders->paginate($pageSize);
    }

    public function show($id)
    {
        $defender = Defender::findOrFail($id);
        IdentificationService::load($defender, [
            'decision' => 'decisions',
            'group' => 'groups',
            'report' => 'reports',
            'tag' => 'tags',
            'user' => 'getOwner',
        ]);
        return $defender;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), DefenderValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $validated['user_id'] = IdentificationService::get()->id;
        $defender = Defender::create($validated);
        if (isset($validated['group_ids']))
        {
            $defender->groups()->sync($validated['group_ids']);
        }
        if (isset($validated['decision_ids']))
        {
            $defender->decisions()->sync($validated['decision_ids']);
        }
        TagFieldService::syncTags($validated, $defender);
        IdentificationService::load($defender, [
            'decision' => 'decisions',
            'group' => 'groups',
            'tag' => 'tags',
            'user' => 'getOwner',
        ]);
        return $defender;
    }

    public function update(Request $request, $id)
    {
        $defender = Defender::findOrFail($id);
        $validator = Validator::make($request->all(), DefenderValidator::build(
            false,
            $id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $defender->update($validated);
        if (isset($validated['group_ids']))
        {
            $defender->groups()->sync($validated['group_ids']);
        }
        if (isset($validated['decision_ids']))
        {
            $defender->decisions()->sync($validated['decision_ids']);
        }
        TagFieldService::syncTags($validated, $defender);
        IdentificationService::load($defender, [
            'decision' => 'decisions',
            'group' => 'groups',
            'tag' => 'tags',
            'user' => 'getOwner',
        ]);
        return $defender;
    }

    public function delete($id)
    {
        $defender = Defender::findOrFail($id);
        $defender->delete();
        return response()->json([
            'message' => "Defender $defender->id deleted"
        ]);
    }
}
