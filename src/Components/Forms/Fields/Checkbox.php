<?php

namespace Cord\Forms\Fields;

use Closure;
use Cord\Support\Abstracts\Field;

class Checkbox extends Field
{
    protected array|Closure $options = [];

    protected bool $isBulk = false;

    public function getView(): string
    {
        // Checkbox com options → checkbox list
        // Checkbox sem options → checkbox único
        if (! empty($this->options)) {
            return 'cord::fields.checkbox-list';
        }

        return 'cord::fields.checkbox';
    }

    // --- Options (para checkbox list) ---

    public function options(array|Closure $options): static
    {
        return $this->set('options', $options);
    }

    public function getOptions(): array
    {
        return $this->evaluate($this->options);
    }

    // --- Bulk select/deselect ---

    public function bulk(bool $condition = true): static
    {
        return $this->set('isBulk', $condition);
    }

    public function isBulk(): bool
    {
        return $this->isBulk;
    }

    // --- Default override (boolean para checkbox único) ---

    public function getDefaultValue(): mixed
    {
        $default = $this->evaluate($this->default);

        if (! empty($this->options)) {
            return $default ?? [];
        }

        return $default ?? false;
    }
}
