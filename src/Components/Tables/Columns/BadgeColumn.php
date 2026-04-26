<?php

namespace Cord\Tables\Columns;

use Closure;

class BadgeColumn extends Column
{
    protected array|Closure $colorMap = [];

    public function getView(): string
    {
        return 'cord::table.columns.badge-column';
    }

    // --- Color map ---

    /**
     * Mapeia valores para cores semânticas.
     * Ex: ['active' => 'success', 'inactive' => 'danger', 'pending' => 'warning']
     */
    public function colors(array|Closure $colorMap): static
    {
        return $this->set('colorMap', $colorMap);
    }

    public function getColor(mixed $record): string
    {
        $value = $this->getValue($record);
        $map = $this->evaluate($this->colorMap);

        return $map[$value] ?? 'gray';
    }

    /**
     * Retorna o valor formatado para exibição no badge.
     */
    public function getDisplayValue(mixed $record): string
    {
        $value = $this->getFormattedValue(
            $this->getValue($record),
            $record
        );

        return (string) ($value ?? '');
    }
}
