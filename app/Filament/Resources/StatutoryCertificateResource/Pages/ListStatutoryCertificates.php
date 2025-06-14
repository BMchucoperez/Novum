<?php

namespace App\Filament\Resources\StatutoryCertificateResource\Pages;

use App\Filament\Resources\StatutoryCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class ListStatutoryCertificates extends ListRecords
{
    protected static string $resource = StatutoryCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Certificado')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('filament.statutory-certificates.styles')
        );
    }
}
