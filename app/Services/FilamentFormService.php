<?php

namespace App\Services;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;

class FilamentFormService
{
    public static function textInput($name, $label = null, $placeholder = null, $rules = [])
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

    public static function select($name, $label = null, $rules = [], $options = null)
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

    public static function toggleButton($name, $label = null, $rules = [], $options = [], $colors = [])
    {
        return ToggleButtons::make($name)
        ->label($label)
        ->rules($rules)
        ->options($options)
        ->colors($colors)
        ->inline();
    }

    public static function colorPicker($name, $label = null)
    {
        return ColorPicker::make($name)
        ->label($label)
        ->default('#000000');
    }

    public static function checkboxList($name, $label = null, $options = [])
    {
        return CheckboxList::make($name)
        ->label($label)
        ->options($options);
    }

    public static function fileUpload($name, $label = null, $rules = [])
    {
        return FileUpload::make($name)
        ->label($label)
        ->visibility('private')
        ->disk('local')
        ->rules($rules);
    }

    public static function placeholder($name, $heperText = null)
    {
        return Placeholder::make($name)
        ->helperText($heperText);
    }

    public static function repeater($name, $schema)
    {
        return Repeater::make($name)
        ->schema($schema);
    }

    public static function owner()
    {
        $user = AuthenticationService::get();
        return Hidden::make('user_id')
        ->default($user->id);
    }
}
