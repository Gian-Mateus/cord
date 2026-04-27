<?php

namespace Cord\Pages;

use Cord\Contracts\Registrable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class Page extends Component implements Registrable
{
    // O dev pode sobrescrever para customizar a view
    protected string $view = '';

    // O dev pode sobrescrever para customizar o slug
    protected static string $slug = '';

    // --- Registrable ---

    public static function registerRoutes(string $panelPath): void
    {
        Route::livewire(static::getSlug(), static::class)
            ->name(static::getRouteName());
    }

    public static function getSlug(): string
    {
        if (static::$slug !== '') {
            return static::$slug;
        }

        // DashboardPage → dashboard
        // UserProfilePage → user-profile
        return Str::of(class_basename(static::class))
            ->beforeLast('Page')
            ->kebab()
            ->toString();
    }

    public static function getRouteName(): string
    {
        return static::getSlug();
    }

    // --- Render ---

    public function render(): View
    {
        $view = $this->view !== ''
            ? $this->view
            : $this->resolveView();

        return view($view);
    }

    protected function resolveView(): string
    {
        // Convenção: DashboardPage → cord::pages.dashboard
        $name = Str::of(class_basename(static::class))
            ->beforeLast('Page')
            ->kebab()
            ->toString();

        return "cord::pages.{$name}";
    }
}
