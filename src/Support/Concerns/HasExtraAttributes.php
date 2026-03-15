<?php

namespace Cord\Support\Concerns;

trait HasExtraAttributes
{
    protected array $extraAttributes = [];

    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = array_merge($this->extraAttributes, $attributes);

        return $this;
    }

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    /**
     * Converte para string HTML: class="foo" id="bar"
     * Pronto para usar nas views: {!! $component->getExtraAttributesString() !!}
     */
    public function getExtraAttributesString(): string
    {
        return collect($this->extraAttributes)
            ->map(fn ($value, $key) => "{$key}=\"{$value}\"")
            ->implode(' ');
    }
}
