<?php

namespace App\Services;

use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;

class FilamentTableService
{
    public static function actionGroup($view = true, $edit = true, $delete = true, $more = [])
    {
        $actionGroup = [];
        if ($view)
        {
            $actionGroup[] = ViewAction::make();
        }
        if ($edit)
        {
            $actionGroup[] = EditAction::make();
        }
        if (count($more) > 0)
        {
            foreach ($more as $action)
            {
                $actionGroup[] = $action;
            }
        }
        if ($delete)
        {
            $actionGroup[] = DeleteAction::make();
        }
        return ActionGroup::make($actionGroup);
    }

    public static function text($name, $label = null)
    {
        return TextColumn::make($name)
        ->label($label)
        ->searchable()
        ->sortable()
        ->toggleable();
    }

    public static function icon($name, $label = null)
    {
        return IconColumn::make($name)
        ->label($label)
        ->searchable()
        ->sortable()
        ->toggleable();
    }

    public static function toggle($name, $label = null)
    {
        return ToggleColumn::make($name)
        ->label($label)
        ->searchable()
        ->sortable()
        ->toggleable();
    }

    public static function color($name, $label = null)
    {
        return ColorColumn::make($name)
        ->label($label)
        ->searchable()
        ->sortable()
        ->toggleable()
        ->copyable()
        ->copyMessage('Color code copied')
        ->copyMessageDuration(1500);
    }

    public static function textInput($name, $label = null, $rules = null)
    {
        return TextInputColumn::make($name)
        ->label($label)
        ->searchable()
        ->sortable()
        ->toggleable()
        ->rules($rules)
        ->width(3);
    }
}
