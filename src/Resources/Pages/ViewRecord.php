<?php

namespace Cord\Resources\Pages;

use Illuminate\Support\Facades\Route;

abstract class ViewRecord extends ResourcePage
{
    public ?int $recordId = null;

    public static function registerRoutes(string $panelPath): void
    {
        Route::livewire(static::getSlug() . '/{record}/view', static::class)
            ->name(static::getRouteName());
    }

    public static function getRouteName(): string
    {
        return static::getSlug() . '.view';
    }

    public function mount(mixed $record): void
    {
        $this->recordId = static::getResource()::getModel()::findOrFail($record)->getKey();
    }

    protected function resolveView(): string
    {
        return 'cord::resources.pages.view-record';
    }
}
