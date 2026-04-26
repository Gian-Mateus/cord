<?php

namespace Cord\Forms\Fields;

use Closure;
use Cord\Support\Abstracts\Field;

class Select extends Field
{
    protected array|Closure $options = [];

    protected ?string $placeholder = null;

    protected bool $isSearchable = false;

    protected bool $isMultiple = false;

    public function getView(): string
    {
        return 'cord::fields.select';
    }

    // --- Options ---

    public function options(array|Closure $options): static
    {
        return $this->set('options', $options);
    }

    public function getOptions(): array
    {
        return $this->evaluate($this->options);
    }

    // --- Placeholder ---

    public function placeholder(string $placeholder): static
    {
        return $this->set('placeholder', $placeholder);
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    // --- Searchable ---

    public function searchable(bool $condition = true): static
    {
        return $this->set('isSearchable', $condition);
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    // --- Multiple ---

    public function multiple(bool $condition = true): static
    {
        return $this->set('isMultiple', $condition);
    }

    public function isMultiple(): bool
    {
        return $this->isMultiple;
    }
}
