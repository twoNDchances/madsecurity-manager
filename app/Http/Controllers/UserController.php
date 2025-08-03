<?php

namespace App\Http\Controllers;

use App\Mail\VerificationMail;
use App\Models\User;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\UserValidator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private function relationships($user)
    {
        return [
            'decision' => 'getDecisions',
            'defender' => [
                'getDefenders' => function($query) use ($user)
                {
                    if (!$user->important)
                    {
                        $query = $query->where('important', false);
                    }
                    return $query;
                },
            ],
            'user' => [
                'getSuperior' => function($query) use ($user)
                {
                    if (!$user->important)
                    {
                        $query = $query->where('important', false);
                    }
                    return $query;
                },
                'getSubordinates' => function($query) use ($user)
                {
                    if (!$user->important)
                    {
                        $query = $query->where('important', false);
                    }
                    return $query;
                },
            ],
            'permission' => 'getPermissions',
            'policy' => [
                'getPolicies',
                'policies',
            ],
            'rule' => 'getRules',
            'target' => 'getTargets',
            'wordlist' => 'getWordlists',
            'token' => [
                'getTokens',
                'tokens',
            ],
            'tag' => [
                'getTags',
                'tags',
            ],
        ];
    }

    public function list(Request $request)
    {
        $user = IdentificationService::get();
        $users = User::query();
        if (!$user->important)
        {
            $users->where('important', false);
        }
        if ($request->boolean('all'))
        {
            return $users->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $users->paginate($pageSize);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $currentUser = IdentificationService::get();
        if ($user->important && !$currentUser->important)
        {
            abort(404);
        }
        IdentificationService::load($user, $this->relationships($currentUser));
        return $user;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), UserValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $currentUser = IdentificationService::get();
        $validated['user_id'] = $currentUser->id;
        if ($validated['verification'])
        {
            $validated['token'] = Str::uuid();
            try
            {
                Mail::to($validated['email'])->send(
                    new VerificationMail(
                        $validated['name'],
                        $validated['token'],
                    ),
                );
            }
            catch (Exception $_)
            {
                $validated['email_verified_at'] = now();
            }
        }
        else
        {
            $validated['email_verified_at'] = now();
        }
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        if (isset($validated['policy_ids']))
        {
            $user->policies()->sync($validated['policy_ids']);
        }
        if (isset($validated['token_ids']))
        {
            $user->tokens()->sync($validated['policy_ids']);
        }
        TagFieldService::syncTags($validated, $user);
        IdentificationService::load($user, $this->relationships($currentUser));
        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = IdentificationService::get();
        if ($user->important && !$currentUser->important)
        {
            abort(404);
        }
        $validator = Validator::make($request->all(), UserValidator::build(
            false,
            $user->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        if (isset($validated['password']))
        {
            $validated['password'] = Hash::make($validated['password']);
        }
        $user->update($validated);
        if (isset($validated['policy_ids']))
        {
            $user->policies()->sync($validated['policy_ids']);
        }
        if (isset($validated['token_ids']))
        {
            $user->tokens()->sync($validated['policy_ids']);
        }
        TagFieldService::syncTags($validated, $user);
        IdentificationService::load($user, $this->relationships($currentUser));
        return $user;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $currentUser = IdentificationService::get();
        if ($user->important && !$currentUser->important)
        {
            abort(404);
        }
        if ($user->id == $currentUser->id)
        {
            abort(404);
        }
        return response()->json([
            'message' => "User $user->id deleted"
        ]);
    }

    public function verify($token)
    {
        $user = User::where('token', $token)->first();
        if (!$user || $user->email_verified_at)
        {
            abort(404);
        }
        Auth::login($user);
        $user->update([
            'token' => null,
            'email_verified_at' => now(),
        ]);
        return redirect()->route('filament.manager.home');
    }
}
