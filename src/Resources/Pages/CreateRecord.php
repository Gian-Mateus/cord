<?php

namespace Cord\Resources\Pages;

use Cord\Forms\Form;
use Illuminate\Support\Facades\Route;

abstract class CreateRecord extends ResourcePage
{
    public array $data = [];

    public static function registerRoutes(string $panelPath): void
    {
        Route::livewire(static::getSlug().'/create', static::class)
            ->name(static::getRouteName());
    }

    public static function getRouteName(): string
    {
        return static::getSlug().'.create';
    }

    public function mount(): void
    {
        $this->data = $this->getForm()->getDefaultState();
    }

    protected function getForm(): Form
    {
        return static::getResource()::form()
            ->statePath('data')
            ->withLivewire($this);
    }

    public function create(): void
    {
        $this->validate($this->getForm()->getValidationRules());

        $data = static::getResource()::filterFillable($this->data);
        $data = $this->beforeCreate($data);

        $record = static::getResource()::getModel()::create($data);

        $this->afterCreate($record);

        $this->redirect(
            route("cord.{$this->getPanelId()}.".static::getResource()::getRouteName().'.index'),
            navigate: true
        );
    }

    protected function beforeCreate(array $data): array
    {
        return $data;
    }

    protected function afterCreate(mixed $record): void {}

    protected function resolveView(): string
    {
        return 'cord::resources.pages.create-record';
    }
}
