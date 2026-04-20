<?php

namespace Cord\Forms\Fields;

use Cord\Support\Abstracts\Field;

class TextInput extends Field
{
    protected string $type = 'text';

    protected ?string $placeholder = null;

    protected ?string $prefix = null;

    protected ?string $suffix = null;

    protected ?int $maxLength = null;

    public function getView(): string
    {
        return 'cord::fields.text-input';
    }

    // --- Tipo de input ---

    public function type(string $type): static
    {
        return $this->set('type', $type);
    }

    public function password(): static
    {
        return $this->type('password');
    }

    public function email(): static
    {
        $this->rule('email');

        return $this->type('email');
    }

    public function number(): static
    {
        return $this->type('number');
    }

    public function tel(): static
    {
        return $this->type('tel');
    }

    public function url(): static
    {
        $this->rule('url');

        return $this->type('url');
    }

    public function getType(): string
    {
        return $this->type;
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

    // --- Prefix / Suffix ---

    public function prefix(string $prefix): static
    {
        return $this->set('prefix', $prefix);
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function suffix(string $suffix): static
    {
        return $this->set('suffix', $suffix);
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    // --- Max length (atributo HTML) ---

    public function maxLength(int $maxLength): static
    {
        return $this->set('maxLength', $maxLength);
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }
}
