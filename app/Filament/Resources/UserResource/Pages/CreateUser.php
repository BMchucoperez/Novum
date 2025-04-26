<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return trans('filament-users::user.resource.title.create');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Usuario creado correctamente';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isset($data['email_verified_at'])) {
            $data['email_verified_at'] = now();
        }

        return $data;
    }
}
