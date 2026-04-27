<?php

// src/Resources/Resource.php

namespace Cord\Resources;

use Cord\Contracts\Registrable;
use Illuminate\Support\Str;

abstract class Resource implements Registrable
{
    protected static string $model = '';

    protected static string $slug = '';

    // O dev declara explicitamente as pages do resource
    abstract public static function getPages(): array;

    // --- Registrable ---

    public static function registerRoutes(string $panelPath): void
    {
        // Delega o registro para cada Page declarada
        foreach (static::getPages() as $page) {
            $page::registerRoutes($panelPath);
        }
    }

    public static function getSlug(): string
    {
        if (static::$slug !== '') {
            return static::$slug;
        }

        // UserResource → users
        return Str::of(class_basename(static::class))
            ->beforeLast('Resource')
            ->plural()
            ->kebab()
            ->toString();
    }

    public static function getRouteName(): string
    {
        return static::getSlug();
    }

    // --- Model ---

    public static function getModel(): string
    {
        return static::$model;
    }

    public static function getFillable(): array
    {
        return (new static::$model)->getFillable();
    }

    public static function filterFillable(array $data): array
    {
        $fillable = static::getFillable();

        return array_filter(
            $data,
            fn ($key) => in_array($key, $fillable),
            ARRAY_FILTER_USE_KEY
        );
    }
}
