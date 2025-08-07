<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\PolicyValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PolicyController extends Controller
{
    private function relationships()
    {
        return [
            'permission' => 'permissions',
            'user' => [
                'users' => IdentificationService::important(),
                'getOwner' => IdentificationService::important(),
            ],
            'tag' => 'tags',
        ];
    }

    public function list(Request $request)
    {
        $policies = Policy::query();
        if ($request->boolean('all'))
        {
            return $policies->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $policies->paginate($pageSize);
    }

    public function show($id)
    {
        $policy = Policy::findOrFail($id);
        IdentificationService::load($policy, $this->relationships());
        return $policy;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), PolicyValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $policy = Policy::create($validated);
        if (isset($validated['permission_ids']))
        {
            $policy->permissions()->sync($validated['permission_ids']);
        }
        if (isset($validated['user_ids']))
        {
            $policy->users()->sync($validated['user_ids']);
        }
        TagFieldService::syncTags($validated, $policy);
        IdentificationService::load($policy, $this->relationships());
        return $policy;
    }

    public function update(Request $request, $id)
    {
        $policy = Policy::findOrFail($id);
        $validator = Validator::make($request->all(), PolicyValidator::build(
            false,
            $policy->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $policy->update($validated);
        if (isset($validated['permission_ids']))
        {
            $policy->permissions()->sync($validated['permission_ids']);
        }
        if (isset($validated['user_ids']))
        {
            $policy->users()->sync($validated['user_ids']);
        }
        TagFieldService::syncTags($validated, $policy);
        IdentificationService::load($policy, $this->relationships());
        return $policy;
    }

    public function delete($id)
    {
        $policy = Policy::findOrFail($id);
        $policy->delete();
        return response()->json([
            'message' => "Policy $policy->id deleted"
        ]);
    }
}
