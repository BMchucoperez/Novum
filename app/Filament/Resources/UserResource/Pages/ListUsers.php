<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Gestión de Usuarios';
    }

    public function getSubheading(): ?string
    {
        return 'Administra todos los usuarios del sistema, sus roles y permisos';
    }

    protected function getActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Usuario')
                ->icon('heroicon-o-user-plus')
                ->color('primary'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserResource\Widgets\UserStatsOverview::class,
        ];
    }
}
