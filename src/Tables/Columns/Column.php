<?php

namespace Cord\Tables\Columns;

use Closure;
use Cord\Support\Abstracts\Component;

abstract class Column extends Component
{
    protected bool $isSortable = false;

    protected bool $isSearchable = false;

    protected string|Closure|null $formatUsing = null;

    protected ?string $alignment = null;

    // --- Sortable ---

    public function sortable(bool $condition = true): static
    {
        return $this->set('isSortable', $condition);
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
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

    // --- Formatação ---

    public function formatUsing(Closure $callback): static
    {
        return $this->set('formatUsing', $callback);
    }

    public function getFormattedValue(mixed $value, mixed $record = null): mixed
    {
        if ($this->formatUsing) {
            return $this->evaluate($this->formatUsing, [
                'value' => $value,
                'record' => $record,
            ]);
        }

        return $value;
    }

    // --- Alignment ---

    public function alignStart(): static
    {
        return $this->set('alignment', 'start');
    }

    public function alignCenter(): static
    {
        return $this->set('alignment', 'center');
    }

    public function alignEnd(): static
    {
        return $this->set('alignment', 'end');
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }

    /**
     * Extrai o valor do record pelo nome da coluna.
     * Suporta dot notation para relacionamentos.
     */
    public function getValue(mixed $record): mixed
    {
        return data_get($record, $this->getName());
    }
}
