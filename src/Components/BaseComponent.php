<?php

namespace Cord\Components;

use Cord\Support\Concerns\Makeable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Macroable;

abstract class BaseComponent implements Htmlable
{
    use Makeable;
    use Macroable;

    protected string $view;

    public function view(string $view): static
    {
        $this->view = $view;
        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function render(): View
    {
        return view($this->getView(), ['component' => $this]);
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }
}