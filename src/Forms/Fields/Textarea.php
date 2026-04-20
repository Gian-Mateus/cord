<?php

namespace Cord\Forms\Fields;

use Cord\Support\Abstracts\Field;

class Textarea extends Field
{
    protected int $rows = 3;

    protected ?int $maxLength = null;

    protected bool $isAutosize = false;

    public function getView(): string
    {
        return 'cord::fields.textarea';
    }

    // --- Rows ---

    public function rows(int $rows): static
    {
        return $this->set('rows', $rows);
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    // --- Max length ---

    public function maxLength(int $maxLength): static
    {
        return $this->set('maxLength', $maxLength);
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    // --- Autosize ---

    public function autosize(bool $condition = true): static
    {
        return $this->set('isAutosize', $condition);
    }

    public function isAutosize(): bool
    {
        return $this->isAutosize;
    }
}
