<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\VerificationMail;
use App\Services\AuthenticationService;
use App\Services\NotificationService;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $data['user_id'] = AuthenticationService::get()?->id;
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
