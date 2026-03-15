<?php

namespace Cord\Support\Concerns;

trait HasFluentApi
{
    public static function make(string $name = ''): static
    {
        $instance = app(static::class);

        if ($name !== '') {
            $instance->name = $name;
        }

        return $instance;
    }

    protected function set(string $property, mixed $value): static
    {
        $this->{$property} = $value;

        return $this;
    }

    public function customize(callable $callback): static
    {
        $callback($this);

        return $this;
    }
}
