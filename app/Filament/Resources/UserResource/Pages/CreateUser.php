<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\VerificationMail;
use App\Services\NotificationService;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // Complex Logic
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        if ($data['force_verification'])
        {
            $data['token'] = Str::uuid();
            try
            {
                Mail::to($data['email'])->send(new VerificationMail($data['name'], $data['token']));
            }
            catch (Exception $exception)
            {
                NotificationService::notify(
                    'warning',
                    'Unable to send verification email',
                    $exception->getMessage(),
                );
                $data['email_verified_at'] = now();
            }
        }
        else
        {
            $data['email_verified_at'] = now();
        }
        return $data;
    }

    public static function callByStatic(array $data): Model
    {
        $form = (new static())->mutateFormDataBeforeCreate($data);
        return (new static())->handleRecordCreation($form);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
