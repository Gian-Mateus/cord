<?php

namespace Cord\Components\Tables;

use Cord\Components\BaseComponent;

class TextColumn extends BaseComponent
{
    protected string $view = 'cord::components.tables.text-column';

    protected string $name;
    protected ?string $label = null;
    protected bool $searchable = false;
    protected bool $sortable = false;

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

    public function searchable(bool $condition = true): static
    {
        $this->searchable = $condition;
        return $this;
    }

    public function sortable(bool $condition = true): static
    {
        $this->sortable = $condition;
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

    public function getValue(mixed $record): mixed
    {
        return data_get($record, $this->getName());
    }
}
