<?php

namespace App\Providers\Manager;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class HttpRequestProvider extends ServiceProvider
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
        Http::macro(
            'agent',
            fn() => Http::withUserAgent(env('MANAGER_HTTP_USER_AGENT'))
        );
    }
}
