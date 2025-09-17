<?php

namespace App\Filament\Resources\StructureAndMachineryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StructureAndMachineryResource;
use Filament\Facades\Filament;

class CreateStructureAndMachinery extends CreateRecord
{
    protected static string $resource = StructureAndMachineryResource::class;

    public function mount(): void
    {
        // Verificar si el usuario tiene el rol "Armador" y redirigir si es así
        if (auth()->check() && auth()->user()->hasRole('Armador')) {
            Filament::notify('danger', 'No tienes permisos para crear registros de estructura y maquinaria.');
            $this->redirect(static::$resource::getUrl('index'));
            return;
        }

        parent::mount();
    }
}
