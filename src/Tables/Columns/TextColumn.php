<?php

namespace Cord\Tables\Columns;

class TextColumn extends Column
{
    protected ?int $characterLimit = null;

    protected ?string $placeholder = null;

    protected bool $isCopyable = false;

    public function getView(): string
    {
        return 'cord::table.columns.text-column';
    }

    // --- Character limit ---

    public function limit(int $characterLimit): static
    {
        return $this->set('characterLimit', $characterLimit);
    }

    public function getCharacterLimit(): ?int
    {
        return $this->characterLimit;
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

    // --- Copyable ---

    public function copyable(bool $condition = true): static
    {
        return $this->set('isCopyable', $condition);
    }

    public function isCopyable(): bool
    {
        return $this->isCopyable;
    }

    /**
     * Retorna o valor já formatado e limitado.
     */
    public function getDisplayValue(mixed $record): string
    {
        $value = $this->getFormattedValue(
            $this->getValue($record),
            $record
        );

        if ($value === null || $value === '') {
            return $this->placeholder ?? '';
        }

        if ($this->characterLimit && mb_strlen((string) $value) > $this->characterLimit) {
            return mb_substr((string) $value, 0, $this->characterLimit).'…';
        }

        return (string) $value;
    }
}
