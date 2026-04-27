<?php

// src/Resources/Pages/EditRecord.php

namespace Cord\Resources\Pages;

use Cord\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

abstract class EditRecord extends ResourcePage
{
    public array $data = [];

    public ?int $recordId = null;

    public static function registerRoutes(string $panelPath): void
    {
        Route::livewire(static::getSlug().'/{record}', static::class)
            ->name(static::getRouteName());
    }

    public static function getRouteName(): string
    {
        return static::getSlug().'.edit';
    }

    public function mount(mixed $record): void
    {
        $model = static::getResource()::getModel()::findOrFail($record);

        $this->recordId = $model->getKey();

        $formKeys = collect($this->getForm()->getComponents())
            ->map->getName()
            ->all();

        $this->data = array_intersect_key(
            $model->toArray(),
            array_flip($formKeys)
        );
    }

    protected function getRecord(): Model
    {
        return static::getResource()::getModel()::findOrFail($this->recordId);
    }

    protected function getForm(): Form
    {
        return static::getResource()::form()
            ->statePath('data')
            ->withLivewire($this);
    }

    public function save(): void
    {
        $this->validate($this->getForm()->getValidationRules());

        $data = static::getResource()::filterFillable($this->data);
        $data = $this->beforeSave($data);
        $record = $this->getRecord();

        $record->update($data);

        $this->afterSave($record);

        $this->redirect(
            route("cord.{$this->getPanelId()}.".static::getResource()::getRouteName().'.index'),
            navigate: true
        );
    }

    protected function beforeSave(array $data): array
    {
        return $data;
    }

    protected function afterSave(mixed $record): void {}

    protected function resolveView(): string
    {
        return 'cord::resources.pages.edit-record';
    }
}
