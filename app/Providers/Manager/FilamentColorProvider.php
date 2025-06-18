<?php

namespace App\Providers\Manager;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;

class FilamentColorProvider extends ServiceProvider
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
        $colors = [
            'indigo' => Color::Indigo,
            'purple' => Color::Purple,
            'cyan' => Color::Cyan,
            'sky' => Color::Sky,
            'teal' => Color::Teal,
            'slate' => Color::Slate,
            'rose' => Color::Rose,
            'pink' => Color::Pink,
        ];

        FilamentColor::register($colors);
    }
}
