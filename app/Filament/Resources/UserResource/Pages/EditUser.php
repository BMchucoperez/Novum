<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function mutateFormDataBeforeSave(array $data): array
    {
        $getUser = User::where('email', $data['email'])->first();
        if ($getUser) {
            if (empty($data['password'])) {
                $data['password'] = $getUser->password;
            }
        }
        return $data;
    }

    public function getTitle(): string
    {
        return trans('filament-users::user.resource.title.edit');
    }

    protected function getActions(): array
    {
        $actions = [];

        if (config('filament-users.impersonate')) {
            $actions[] = Impersonate::make()
                ->record($this->getRecord())
                ->label('Suplantar')
                ->color('warning')
                ->icon('heroicon-o-identification');
        }

        $actions[] = DeleteAction::make()
            ->label('Eliminar')
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->modalHeading('Eliminar Usuario')
            ->modalDescription('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')
            ->modalSubmitActionLabel('Sí, eliminar')
            ->modalCancelActionLabel('No, cancelar');

        return $actions;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Usuario actualizado correctamente';
    }
}
