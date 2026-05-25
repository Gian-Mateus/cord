<?php

namespace Cord\Bases;

use Livewire\Component;

abstract class BasePage extends Component
{
    public function render()
    {
        return view(static::getView())
            ->layout('cord::components.layouts.app');
    }

    abstract protected static function getView(): string;
}
