<?php

namespace Cord\Components\Forms;

use Cord\Components\BaseComponent;

class TextInput extends BaseComponent
{
    protected string $view = 'cord::components.forms.text-input';

    protected string $name;
    protected ?string $label = null;
    protected bool $required = false;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->label = str($name)->replace('_', ' ')->title()->toString();
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function required(bool $condition = true): static
    {
        $this->required = $condition;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label ?? '';
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
