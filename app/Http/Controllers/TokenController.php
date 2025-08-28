<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\TokenValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    private function relationships()
    {
        return [
            'user' => [
                'getOwner' => IdentificationService::important(),
                'users' => IdentificationService::important(),
            ],
            'tag' => 'tags',
        ];
    }

    public function list(Request $request)
    {
        $tokens = Token::query();
        if ($request->boolean('all'))
        {
            return $tokens->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $tokens->paginate($pageSize);
    }

    public function show($id)
    {
        $token = Token::findOrFail($id);
        IdentificationService::load($token, $this->relationships());
        return $token;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), TokenValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $value = null;
        while (true)
        {
            $value = Str::random(48);
            $alreadyExists = false;
            Token::cursor()->each(function ($token) use (&$alreadyExists, $value)
            {
                if (Hash::check($value, $token->value))
                {
                    $alreadyExists = true;
                    return false;
                }
            });
            if (!$alreadyExists)
            {
                break;
            }
        }
        $validated['value'] = Hash::make($value);
        $token = Token::create($validated);
        if (isset($validated['user_ids']))
        {
            $token->users()->sync($validated['user_ids']);
        }
        TagFieldService::syncTags($validated, $token);
        IdentificationService::load($token, $this->relationships());
        return collect($token)->merge(['token' => $value]);
    }

    public function update(Request $request, $id)
    {
        $token = Token::findOrFail($id);
        $validator = Validator::make($request->all(), TokenValidator::build(
            false,
            $token->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $value = null;
        if ($request->boolean('refresh', false))
        {
            while (true)
            {
                $value = Str::random(48);
                $alreadyExists = false;
                Token::cursor()->each(function ($token) use (&$alreadyExists, $value)
                {
                    if (Hash::check($value, $token->value))
                    {
                        $alreadyExists = true;
                        return false;
                    }
                });
                if (!$alreadyExists)
                {
                    break;
                }
            }
            $validated['value'] = Hash::make($value);
        }
        $token->update($validated);
        if (isset($validated['user_ids']))
        {
            $token->users()->sync($validated['user_ids']);
        }
        TagFieldService::syncTags($validated, $token);
        IdentificationService::load($token, $this->relationships());
        return $value ? collect($token)->merge(['token' => $value]) : collect($token);
    }

    public function delete($id)
    {
        $token = Token::findOrFail($id);
        $token->delete();
        return response()->json([
            'message' => "Token $token->id deleted"
        ]);
    }
}
