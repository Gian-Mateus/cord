<?php

namespace Cord\Support\Abstracts;

use Cord\Support\Concerns\HasValidation;

abstract class Field extends Component
{
    use HasValidation;

    protected string $statePath = '';

    protected bool $isLive = false;

    protected bool $isLazy = false;

    protected mixed $default = null;

    // --- State path ---

    public function prefixStatePath(string $prefix): void
    {
        $this->statePath = $prefix.'.'.$this->getName();
    }

    public function getStatePath(): string
    {
        return $this->statePath ?: $this->getName();
    }

    // --- Wire model ---

    public function live(bool $condition = true): static
    {
        return $this->set('isLive', $condition);
    }

    public function lazy(): static
    {
        return $this->set('isLazy', true);
    }

    // Retorna array compatível com ComponentAttributeBag->merge()
    // Uso na view: $attributes->merge($component->getWireModelAttribute())
    public function getWireModelAttribute(): array
    {
        $modifier = match (true) {
            $this->isLive => '.live',
            $this->isLazy => '.lazy',
            default => '',
        };

        return ["wire:model{$modifier}" => $this->getStatePath()];
    }

    // --- Default ---

    public function default(mixed $value): static
    {
        return $this->set('default', $value);
    }

    public function getDefaultValue(): mixed
    {
        return $this->evaluate($this->default);
    }

    // --- View data ---

    public function viewData(): array
    {
        return array_merge(parent::viewData(), [
            'statePath' => $this->getStatePath(),
            'wireModel' => $this->getWireModelAttribute(),
            'required'  => $this->isRequired(),
        ]);
    }
}
