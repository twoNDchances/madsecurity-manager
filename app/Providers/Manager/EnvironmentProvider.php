<?php

namespace App\Providers\Manager;

use App\Exceptions\EnvironmentException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class EnvironmentProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $variables = [
            'MANAGER_USER_NAME' => env('MANAGER_USER_NAME', 'root'),
            'MANAGER_USER_MAIL' => env('MANAGER_USER_MAIL', 'root@madsecurity.com'),
            'MANAGER_USER_PASS' => env('MANAGER_USER_PASS', 'root'),

            'MANAGER_PATH_PREFIX' => env('MANAGER_PATH_PREFIX', 'manager'),
            'MANAGER_HTTP_USER_AGENT' => env('MANAGER_HTTP_USER_AGENT', 'M&DSecurity@Manager'),

            'MANAGER_VERIFICATION_ROUTE' => env('MANAGER_VERIFICATION_ROUTE', 'verify'),
            'MANAGER_COLLECTION_ROUTE' => env('MANAGER_COLLECTION_ROUTE', 'collect'),

            'MANAGER_TOKEN_KEY' => env('MANAGER_TOKEN_KEY', 'X-Manager-Token'),
        ];

        $errors = [];

        foreach ($variables as $key => $value)
        {
            if (strlen($value) == 0)
            {
                $errors[] = "{$key} can not null or empty";
            }
            else
            {
                if ($key == 'MANAGER_USER_MAIL')
                {
                    $validator = Validator::make(
                        ['email' => $value],
                        ['email' => 'email'],
                    );
                    if ($validator->fails())
                    {
                        $errors[] = $validator->errors();
                    }
                }

                if ($key == 'MANAGER_USER_PASS')
                {
                    if (strlen($value) < 4)
                    {
                        $errors[] = "{$key} length must greater than or equal 4";
                    }
                }
            }
        }
        if (count($errors) > 0)
        {
            throw new EnvironmentException(implode(",", $errors));
        }
    }
}
