<?php

namespace Cord;

class Cord
{
    protected array $panels = [];

    public static function panel(string $id): Panel
    {
        $instance = app(static::class);

        if (! isset($instance->panels[$id])) {
            $instance->panels[$id] = new Panel($id);
        }

        return $instance->panels[$id];
    }

    public static function getPanels(): array
    {
        return app(static::class)->panels;
    }
}
