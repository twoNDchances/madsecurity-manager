<?php

namespace App\Http\Controllers;

use App\Models\Defender;
use App\Services\AuthenticationService;
use App\Services\TagFieldService;
use App\Validators\API\DefenderValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DefenderController extends Controller
{
    public function list(Request $request)
    {
        $user = AuthenticationService::get();
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
        $defender->load([
            'decisions',
            'groups',
            'groups.rules',
            'groups.rules.getTarget',
            'tags',
            'reports',
        ]);
        return $defender;
    }

    public function create(Request $request)
    {
        $validator = DefenderValidator::class;
        $validation = Validator::make($request->all(), [
            'name' => $validator::name(),
            'group_ids' => $validator::groupIds(),
            'group_ids.*' => $validator::groupId(),
            'url' => $validator::url(),
            'path' => $validator::path(),
            'method' => $validator::method(),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'description' => $validator::description(),
            'important' => $validator::important(),
            'protection' => $validator::protection(),
            'username' => $validator::username(),
            'password' => $validator::password(),
            'decision_ids' => $validator::decisionIds(),
            'decision_ids.*' => $validator::decisionId(),
        ]);
        if ($validation->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validation->errors(),
            ], 400);
        }
        $validated = $validation->validated();
        $defender = Defender::create($validated);
        TagFieldService::syncTags($validated, $defender);
        $defender->load([
            'decisions',
            'groups',
            'groups.rules',
            'groups.rules.getTarget',
            'tags',
        ]);
        return $defender;
    }
}
