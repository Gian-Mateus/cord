<?php

namespace Cord\Providers;

use Cord\Context\PanelContext;
use Cord\Cord;
use Illuminate\Support\ServiceProvider;

class CordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton de configuração — vive durante todo o ciclo da app
        $this->app->singleton(Cord::class);

        // Singleton por request — mesmo objeto dentro do ciclo,
        // descartado entre requests. Nunca entra no snapshot Livewire.
        $this->app->scoped(PanelContext::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'cord');

        // Rotas carregadas depois que o dev já registrou tudo no AppServiceProvider
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/cord'),
            ], 'cord-views');

            $this->publishes([
                __DIR__ . '/../../config/cord.php' => config_path('cord.php'),
            ], 'cord-config');
        }
    }
}
