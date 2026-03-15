<?php

namespace Cord\Support\Concerns;

use Illuminate\View\ComponentAttributeBag;

trait HasAttributes
{
    protected string|\Closure|null $label    = null;
    protected string|\Closure|null $tooltip  = null;
    protected bool|\Closure        $isVisible  = true;
    protected bool|\Closure        $isDisabled = false;
    protected bool|\Closure        $isReadonly = false;
    protected ?string              $key       = null;
    protected array                $extraAttributes = [];

    // --- Label ---

    public function label(string|\Closure $label): static
    {
        return $this->set('label', $label);
    }

    public function getLabel(): string
    {
        $label = $this->evaluate($this->label);

        if ($label !== null) {
            return $label;
        }

        return (string) str($this->name)
            ->snake()
            ->replace('_', ' ')
            ->ucfirst();
    }

    // --- Tooltip ---

    public function tooltip(string|\Closure $tooltip): static
    {
        return $this->set('tooltip', $tooltip);
    }

    public function getTooltip(): ?string
    {
        return $this->evaluate($this->tooltip);
    }

    // --- Visibilidade ---

    public function visible(bool|\Closure $condition = true): static
    {
        return $this->set('isVisible', $condition);
    }

    public function hidden(bool|\Closure $condition = true): static
    {
        if ($condition instanceof \Closure) {
            return $this->set('isVisible', fn(...$args) => ! app()->call($condition, $args[0] ?? []));
        }

        return $this->set('isVisible', ! $condition);
    }

    public function isVisible(array $named = []): bool
    {
        return (bool) $this->evaluate($this->isVisible, $named);
    }

    public function isHidden(array $named = []): bool
    {
        return ! $this->isVisible($named);
    }

    // --- Disabled ---

    public function disabled(bool|\Closure $condition = true): static
    {
        return $this->set('isDisabled', $condition);
    }

    public function isDisabled(array $named = []): bool
    {
        return (bool) $this->evaluate($this->isDisabled, $named);
    }

    // --- Readonly ---

    public function readonly(bool|\Closure $condition = true): static
    {
        return $this->set('isReadonly', $condition);
    }

    public function isReadonly(array $named = []): bool
    {
        return (bool) $this->evaluate($this->isReadonly, $named);
    }

    // --- Key ---

    public function key(string $key): static
    {
        return $this->set('key', $key);
    }

    public function getKey(): string
    {
        return $this->key ?? $this->name;
    }

    // --- Extra attributes ---

    public function extraAttributes(array $attributes): static
    {
        // Classes acumulam, outros atributos sobrescrevem por chave
        if (isset($attributes['class'], $this->extraAttributes['class'])) {
            $attributes['class'] = $this->extraAttributes['class'] . ' ' . $attributes['class'];
        }

        $this->extraAttributes = array_merge($this->extraAttributes, $attributes);

        return $this;
    }

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    // Retorna um ComponentAttributeBag real do Laravel
    // Permite usar $field->getAttributeBag()->merge([...]) nas views
    public function getAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->extraAttributes);
    }
}