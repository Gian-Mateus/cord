<?php

namespace Cord\Tables\Columns;

class BooleanColumn extends Column
{
    protected ?string $trueLabel = null;

    protected ?string $falseLabel = null;

    protected string $trueColor = 'success';

    protected string $falseColor = 'danger';

    public function getView(): string
    {
        return 'cord::table.columns.boolean-column';
    }

    // --- Labels ---

    public function trueLabel(string $label): static
    {
        return $this->set('trueLabel', $label);
    }

    public function getTrueLabel(): ?string
    {
        return $this->trueLabel;
    }

    public function falseLabel(string $label): static
    {
        return $this->set('falseLabel', $label);
    }

    public function getFalseLabel(): ?string
    {
        return $this->falseLabel;
    }

    // --- Colors ---

    public function trueColor(string $color): static
    {
        return $this->set('trueColor', $color);
    }

    public function getTrueColor(): string
    {
        return $this->trueColor;
    }

    public function falseColor(string $color): static
    {
        return $this->set('falseColor', $color);
    }

    public function getFalseColor(): string
    {
        return $this->falseColor;
    }

    /**
     * Retorna se o valor do record é truthy.
     */
    public function isTrue(mixed $record): bool
    {
        return (bool) $this->getValue($record);
    }
}
