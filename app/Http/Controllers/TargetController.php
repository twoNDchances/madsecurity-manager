<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Services\IdentificationService;
use App\Validators\API\TargetValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TargetController extends Controller
{
    private function relationships($user)
    {
        return [
            'rule' => 'rules',
            'wordlist' => 'getWordlist',
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
        $targets = Target::query();
        if ($request->boolean('all'))
        {
            return $targets->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $targets->paginate($pageSize);
    }

    public function show($id)
    {
        $target = Target::findOrFail($id);
        IdentificationService::load(
            $target,
            $this->relationships(IdentificationService::get()),
        );
        return $target;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), TargetValidator::build($request));
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
        
    }
}
