<?php

namespace Cord\Forms\Fields;

use Closure;
use Cord\Support\Abstracts\Field;

class Radio extends Field
{
    protected array|Closure $options = [];

    protected bool $isInline = false;

    public function getView(): string
    {
        return 'cord::fields.radio';
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

    // --- Layout ---

    public function inline(bool $condition = true): static
    {
        return $this->set('isInline', $condition);
    }

    public function isInline(): bool
    {
        return $this->isInline;
    }
}
