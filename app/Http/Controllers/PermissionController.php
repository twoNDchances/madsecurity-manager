<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\PermissionValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    private function relationships($user)
    {
        return [
            'policy' => 'policies',
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
        $permissions = Permission::query();
        if ($request->boolean('all'))
        {
            return $permissions->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $permissions->paginate($pageSize);
    }

    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        IdentificationService::load(
            $permission,
            $this->relationships(IdentificationService::get()),
        );
        return $permission;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), PermissionValidator::build());
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
        $permission = Permission::create($validated);
        if (isset($validated['policy_ids']))
        {
            $permission->policies()->sync($validated['policy_ids']);
        }
        TagFieldService::syncTags($validated, $permission);
        IdentificationService::load($permission, $this->relationships($user));
        return $permission;
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $validator = Validator::make($request->all(), PermissionValidator::build(
            false,
            $permission->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $permission->update($validated);
        if (isset($validated['policy_ids']))
        {
            $permission->policies()->sync($validated['policy_ids']);
        }
        TagFieldService::syncTags($validated, $permission);
        IdentificationService::load(
            $permission,
            $this->relationships(IdentificationService::get()),
        );
        return $permission;
    }

    public function delete($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json([
            'message' => "Permission $permission->id deleted"
        ]);
    }
}
