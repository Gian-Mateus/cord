<?php

namespace Cord\Forms;

use Cord\Support\Abstracts\Field;
use Cord\Support\Concerns\EvaluatesClosures;
use Cord\Support\Concerns\HasFluentApi;

class Form
{
    use HasFluentApi;
    use EvaluatesClosures;

    /** @var Field[] */
    protected array  $components = [];
    protected mixed  $livewire   = null;
    protected mixed  $record     = null;
    protected string $statePath  = '';

    // --- Schema ---

    public function schema(array $fields): static
    {
        $this->components = $fields;
        return $this;
    }

    // --- Contexto ---

    public function record(mixed $record): static
    {
        return $this->set('record', $record);
    }

    public function statePath(string $path): static
    {
        $this->statePath = $path;

        foreach ($this->components as $field) {
            $field->prefixStatePath($path);
        }

        return $this;
    }

    public function withLivewire(mixed $livewire): static
    {
        $this->livewire = $livewire;
        return $this;
    }

    protected function getEvaluationContext(): array
    {
        return array_filter([
            'record'   => $this->record,
            'state'    => $this->livewire?->data ?? [],
            'livewire' => $this->livewire,
        ]);
    }

    // --- API pública ---

    /** @return Field[] */
    public function getComponents(): array
    {
        return $this->components;
    }

    /** @return Field[] */
    public function getVisibleComponents(): array
    {
        $context = $this->getEvaluationContext();

        return array_values(array_filter(
            $this->components,
            fn(Field $field) => $field->isVisible(named: $context),
        ));
    }

    public function getDefaultState(): array
    {
        return collect($this->components)
            ->mapWithKeys(fn(Field $f) => [
                $f->getName() => $f->getDefaultValue()
            ])
            ->all();
    }

    public function getValidationRules(): array
    {
        $context = $this->getEvaluationContext();
        $prefix  = $this->statePath;

        return collect($this->components)
            ->mapWithKeys(function (Field $field) use ($prefix, $context) {
                $key   = $prefix
                    ? "{$prefix}.{$field->getName()}"
                    : $field->getName();

                $rules = $field->getValidationRules($context);

                return $rules ? [$key => $rules] : [];
            })
            ->filter()
            ->all();
    }

    public function getValidationMessages(): array
    {
        $prefix = $this->statePath;

        return collect($this->components)
            ->flatMap(function (Field $field) use ($prefix) {
                $key = $prefix
                    ? "{$prefix}.{$field->getName()}"
                    : $field->getName();

                return collect($field->getValidationMessages())
                    ->mapWithKeys(fn($msg, $rule) => [
                        "{$key}.{$rule}" => $msg
                    ])
                    ->all();
            })
            ->all();
    }
}