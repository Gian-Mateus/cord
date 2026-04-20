<?php

namespace Cord\Providers;

use Cord\Context\PanelContext;
use Illuminate\Support\ServiceProvider;

class CordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton por request — mesmo objeto dentro do ciclo,
        // descartado entre requests. Nunca entra no snapshot Livewire.
        $this->app->scoped(PanelContext::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cord');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/cord'),
            ], 'cord-views');
        }
    }
}
