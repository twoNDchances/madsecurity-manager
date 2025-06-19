<?php

namespace App\Validators;

use App\Services\HttpRequestService;
use Illuminate\Validation\Rule;

class DefenderValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function url()
    {
        return [
            'required',
            'string',
            'url',
            function($record)
            {
                if ($record)
                {
                    return Rule::unique('defenders', 'url')->ignore($record->id);
                }
                return 'unique:defenders,url';
            },
            fn($get) => function($attribute, $value, $fail) use ($get)
            {
                if (!$get('health'))
                {
                    $fail('Health path is required.');
                    return;
                }
                $url = $value . $get('health');
                $response = null;
                if ((bool) $get('protection'))
                {
                    $username = $get('username');
                    $password = $get('password');
                    if ($username && $password)
                    {
                        $response = HttpRequestService::perform(
                            'get',
                            $url,
                            null,
                            false,
                            $username,
                            $password,
                        );
                    }
                    else
                    {
                        $fail('Username & Password are required when Protection is enabled.');
                        return;
                    }
                }
                else
                {
                    $response = HttpRequestService::perform(
                        'get',
                        $url,
                        null,
                        false,
                    );
                }
                if (is_string($response))
                {
                    $fail($response);
                    return;
                }
                if (!$response->successful())
                {
                    $fail("Fail to Health check from $attribute: Status: " . $response->status() . '| Body: ' . $response->body() . '.');
                    return;
                }
            },
        ];
    }

    public static function path()
    {
        return [
            'required',
            'string',
            'starts_with:/'
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function protection()
    {
        return [
            'required',
            'boolean',
        ];
    }

    public static function username()
    {
        return [
            'required_if:protection,true',
            'string',
            'max:255',
        ];
    }

    public static function password()
    {
        return [
            'required_if:protection,true',
            'string',
            'min:8',
            'max:255',
        ];
    }
}
