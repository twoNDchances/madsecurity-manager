<?php

namespace App\Services;

use App\Mail\VerificationMail;
use App\Models\Token;
use App\Models\User;
use App\Models\Word;
use App\Models\Wordlist;
use App\Validators\API\TokenValidator;
use App\Validators\API\UserValidator;
use App\Validators\API\WordlistValidator;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AssetGeneratorService
{
    private static function bypass($resource, $action)
    {
        return IdentificationService::can(
            IdentificationService::get(),
            $resource,
            $action,
        );
    }

    public static function generateTarget(array $data)
    {
        // $validator = Validator::make();
    }

    public static function generateToken(array $data)
    {
        if (!self::bypass('user', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, TokenValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
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
        return [
            'status' => true,
            'id' => $token->id,
            'errors' => null,
        ];
    }

    public static function generateUser(array $data)
    {
        if (!self::bypass('user', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, UserValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
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
        return [
            'status' => true,
            'id' => $user->id,
            'errors' => null,
        ];
    }

    public static function generateWordlist(array $data): array
    {
        if (!self::bypass('wordlist', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, WordlistValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
        $wordlist = Wordlist::create($validated);
        if (isset($validated['words']))
        {
            $words = array_chunk($validated['words'], 10000);
            $now = now();
            foreach ($words as $content)
            {
                $records = [];
                foreach ($content as $line)
                {
                    $records[] = [
                        'content' => $line,
                        'wordlist_id' => $wordlist->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                Word::insert($records);
            }
        }
        TagFieldService::syncTags($validated, $wordlist);
        return [
            'status' => true,
            'id' => $wordlist->id,
            'errors' => null,
        ];
    }
}
