<?php

namespace App\Filament\Resources\DefenderResource\Pages;

use App\Actions\DefenderAction;
use App\Filament\Resources\DefenderResource;
use App\Models\Defender;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDefender extends EditRecord
{
    protected static string $resource = DefenderResource::class;

    protected $listeners = ['refreshDefenderForm' => 'reloadForm'];

    protected function getHeaderActions(): array
    {
        $action = DefenderAction::class;
        return [
            $action::checkHealth(),
            $action::sync(),
            $action::apply(),
            $action::revoke(),
            $action::implement(),
            $action::suspend(),
            Actions\DeleteAction::make()->icon('heroicon-o-trash'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['output'])
        {
            $data['output'] = array_values(array_filter(array_map(
                'trim',
                explode("\n", $data['output'])
            )));
        }
        if (!$data['protection'])
        {
            $data['username'] = $data['password'] = null;
        }
        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $defender = Defender::find($data['id']);
        $data['total_groups'] = $defender->groups->count();
        $data['current_applied'] = $defender->groups()->wherePivot('status', true)->count();
        return $data;
    }

    public function reloadForm(): void
    {
        $this->mutateFormDataBeforeFill($this->data);
        $this->fillForm();
    }
}
