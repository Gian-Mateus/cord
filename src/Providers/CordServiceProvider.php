<?php

namespace Cord\Providers;

use Cord\Context\PanelContext;
use Cord\Cord;
use Illuminate\Support\ServiceProvider;

class CordServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Instancia a classe Cord apenas uma vez e compartilha a mesma instância
        // globalmente com quem a injetar (útil para manter o estado e configurações do pacote).
        $this->app->singleton(Cord::class);

        // Semelhante ao singleton, mas garante que o objeto seja destruído e recriado
        // entre diferentes requisições em ambientes assíncronos (como Laravel Octane).
        // Isso evita que dados sensíveis de um painel/usuário vazem para o próximo.
        $this->app->scoped(PanelContext::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'cord');

        // Registro de Componentes Livewire usando Programação Defensiva:
        // Verificamos se a classe do Livewire existe antes de registrar os componentes.
        // Isso evita que a aplicação quebre caso o Livewire tenha sido desativado
        // ou se torne uma dependência opcional no futuro.
        if (class_exists(\Livewire\Livewire::class)) {
            // \Livewire\Livewire::component('cord::meu-componente', MeuComponente::class);
        }
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
