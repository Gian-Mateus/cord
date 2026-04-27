<?php

namespace Cord\Resources\Pages;

use Cord\Contracts\Registrable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class ResourcePage extends Component implements Registrable
{
    // O Resource ao qual essa page pertence
    protected static string $resource = '';

    // O dev pode sobrescrever a view
    protected string $view = '';

    // --- Inferência do Resource pelo namespace ---

    public static function getResource(): string
    {
        if (static::$resource !== '') {
            return static::$resource;
        }

        // App\Cord\Admin\Resources\UserResource\Pages\ListUsers
        // → App\Cord\Admin\Resources\UserResource\UserResource
        $namespace = Str::of(static::class)
            ->beforeLast('\\Pages\\')
            ->toString();

        $resourceName = Str::of($namespace)
            ->afterLast('\\')
            ->toString();

        return $namespace.'\\'.$resourceName;
    }

    // --- Slug herdado do Resource ---

    public static function getSlug(): string
    {
        return static::getResource()::getSlug();
    }

    // --- Render ---

    public function render(): View
    {
        $view = $this->view !== ''
            ? $this->view
            : $this->resolveView();

        return view($view);
    }

    abstract protected function resolveView(): string;
}
