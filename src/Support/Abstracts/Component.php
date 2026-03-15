<?php

namespace Cord\Support\Abstracts;

use Cord\Support\Concerns\EvaluatesClosures;
use Cord\Support\Concerns\HasAttributes;
use Cord\Support\Concerns\HasFluentApi;

abstract class Component
{
    use HasFluentApi;
    use HasAttributes;
    use EvaluatesClosures;

    public function __construct(
        protected string $name = '',
    ) {}

    // Cada componente concreto declara qual view usa
    abstract public function getView(): string;

    public function getName(): string
    {
        return $this->name;
    }

    // Dados passados para a view
    // A view recebe $component e acessa tudo via métodos
    public function viewData(): array
    {
        return [
            'component'  => $this,
            'attributes' => $this->getAttributeBag(),
        ];
    }

    // Renderiza o componente como uma view Blade anônima
    public function render(): \Illuminate\Contracts\View\View
    {
        return view($this->getView(), $this->viewData());
    }

    public function __toString(): string
    {
        return $this->render()->render();
    }
}