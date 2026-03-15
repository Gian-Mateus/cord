<?php

namespace Cord\Support\Concerns;

trait EvaluatesClosures
{
    protected function evaluate(mixed $value, array $named = []): mixed
    {
        if (! $value instanceof \Closure) {
            return $value;
        }

        return app()->call($value, $named);
    }
}
