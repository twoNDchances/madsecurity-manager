<?php

namespace App\Services;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class FilamentFormService
{
    public function textInput(
        string $name, ?string $label = null, ?string $placeholder = null, ?array $rules = null
    )
    {
        return TextInput::make($name)
        ->label($label)
        ->placeholder($placeholder)
        ->maxLength(255)
        ->rules($rules);
    }

    public function textarea(
        string $name, ?string $label = null, ?string $placeholder = null
    )
    {
        return Textarea::make($name)
        ->label($label)
        ->placeholder($placeholder);
    }

    public function select(
        string $name, ?string $label = null, array|callable|null $options = null
    )
    {
        return Select::make($name)
        ->label($label)
        ->options($options);
    }
}
