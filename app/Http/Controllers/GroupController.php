<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\GroupValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function list(Request $request)
    {
        $pageSize = $request->integer('pageSize', 10);
        return Group::paginate($pageSize);
    }

    public function show($id)
    {
        $group = Group::findOrFail($id);
        IdentificationService::load($group, [
            'defender' => 'defenders',
            'rule' => 'rules',
            'tag' => 'tags',
            'user' => 'getOwner',
        ]);
        return $group;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), GroupValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $validated['user_id'] = IdentificationService::get()->id;
        $group = Group::create($validated);
        if (isset($validated['defender_ids']))
        {
            $group->defenders()->sync($validated['defender_ids']);
        }
        if (isset($validated['rule_ids']))
        {
            $group->rules()->sync($validated['rule_ids']);
        }
        TagFieldService::syncTags($validated, $group);
        IdentificationService::load($group, [
            'defender' => 'defenders',
            'rule' => 'rules',
            'tag' => 'tags',
            'user' => 'getOwner',
        ]);
        return $group;
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $validator = Validator::make($request->all(), GroupValidator::build(
            false,
            $group->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        if (isset($validated['defender_ids']))
        {
            $group->defenders()->sync($validated['defender_ids']);
        }
        if (isset($validated['rule_ids']))
        {
            $group->rules()->sync($validated['rule_ids']);
        }
        TagFieldService::syncTags($validated, $group);
        IdentificationService::load($group, [
            'defender' => 'defenders',
            'rule' => 'rules',
            'tag' => 'tags',
            'user' => 'getOwner',
        ]);
        return $group;
    }

    public function delete($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
        return response()->json([
            'message' => "Group $group->id deleted"
        ]);
    }
}
