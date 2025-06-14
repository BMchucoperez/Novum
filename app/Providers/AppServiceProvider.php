<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar componentes de Blade para iconos personalizados
        $this->loadViewComponentsAs('icons', [
            // Los componentes se cargarán automáticamente desde resources/views/components/icons
        ]);

        // Registrar estilos globales para Filament
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('filament.global-styles')
        );
    }
}
