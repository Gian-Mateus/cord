<?php

namespace Cord\Resources\Pages;

use Illuminate\Support\Facades\Route;

abstract class ListRecords extends ResourcePage
{
    public static function registerRoutes(string $panelPath): void
    {
        Route::livewire(static::getSlug(), static::class)
            ->name(static::getRouteName());
    }

    public static function getRouteName(): string
    {
        return static::getSlug().'.index';
    }

    protected function resolveView(): string
    {
        return 'cord::resources.pages.list-records';
    }
}
