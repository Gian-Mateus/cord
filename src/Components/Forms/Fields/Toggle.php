<?php

namespace Cord\Forms\Fields;

use Cord\Support\Abstracts\Field;

class Toggle extends Field
{
    protected ?string $onLabel = null;

    protected ?string $offLabel = null;

    protected ?string $onColor = null;

    protected ?string $offColor = null;

    public function getView(): string
    {
        return 'cord::fields.toggle';
    }

    // --- Labels ---

    public function onLabel(string $label): static
    {
        return $this->set('onLabel', $label);
    }

    public function getOnLabel(): ?string
    {
        return $this->onLabel;
    }

    public function offLabel(string $label): static
    {
        return $this->set('offLabel', $label);
    }

    public function getOffLabel(): ?string
    {
        return $this->offLabel;
    }

    // --- Colors ---

    public function onColor(string $color): static
    {
        return $this->set('onColor', $color);
    }

    public function getOnColor(): ?string
    {
        return $this->onColor;
    }

    public function offColor(string $color): static
    {
        return $this->set('offColor', $color);
    }

    public function getOffColor(): ?string
    {
        return $this->offColor;
    }

    // --- Default override (boolean) ---

    public function getDefaultValue(): mixed
    {
        return $this->evaluate($this->default) ?? false;
    }
}
