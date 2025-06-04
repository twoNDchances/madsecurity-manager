<?php

namespace App\Services;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class FilamentFormService
{
    public static function textInput($name, $label = null, $placeholder = null, $rules = null)
    {
        return TextInput::make($name)
        ->label($label)
        ->placeholder($placeholder)
        ->maxLength(255)
        ->rules($rules);
    }

    public static function textarea($name, $label = null, $placeholder = null)
    {
        return Textarea::make($name)
        ->label($label)
        ->placeholder($placeholder)
        ->rows(6);
    }

    public static function select($name, $label = null, $options = null, $rules = [])
    {
        return Select::make($name)
        ->label($label)
        ->options($options)
        ->rules($rules);
    }

    public static function toggle($name, $label = null, $rules = [])
    {
        return Toggle::make($name)
        ->label($label)
        ->rules($rules);
    }
}
