<?php

namespace Cord\Forms;

use Closure;
use Cord\Support\Abstracts\Field;
use Cord\Support\Concerns\EvaluatesClosures;
use Cord\Support\Concerns\HasFluentApi;

class Form
{
    use HasFluentApi;
    use EvaluatesClosures;

    protected string $name = '';

    protected array $schema = [];

    protected string $statePath = 'data';

    protected ?Closure $mutateBeforeValidation = null;

    protected ?Closure $mutateBeforeSave = null;

    // --- Schema ---

    public function schema(array $fields): static
    {
        $this->schema = $fields;

        return $this;
    }

    /**
     * Retorna apenas os campos visíveis, com statePath prefixado.
     *
     * @return Field[]
     */
    public function getFields(array $named = []): array
    {
        return collect($this->schema)
            ->filter(fn (Field $field) => $field->isVisible($named))
            ->each(fn (Field $field) => $field->prefixStatePath($this->statePath))
            ->values()
            ->all();
    }

    /**
     * Retorna todos os campos (incluindo ocultos).
     * Útil para construir regras de validação completas.
     *
     * @return Field[]
     */
    public function getAllFields(): array
    {
        return collect($this->schema)
            ->each(fn (Field $field) => $field->prefixStatePath($this->statePath))
            ->values()
            ->all();
    }

    // --- State path ---

    public function statePath(string $path): static
    {
        return $this->set('statePath', $path);
    }

    public function getStatePath(): string
    {
        return $this->statePath;
    }

    // --- Mutators ---

    public function mutateBeforeValidation(Closure $callback): static
    {
        return $this->set('mutateBeforeValidation', $callback);
    }

    public function mutateBeforeSave(Closure $callback): static
    {
        return $this->set('mutateBeforeSave', $callback);
    }

    public function applyMutateBeforeValidation(array $data): array
    {
        if ($this->mutateBeforeValidation) {
            return app()->call($this->mutateBeforeValidation, ['data' => $data]);
        }

        return $data;
    }

    public function applyMutateBeforeSave(array $data): array
    {
        if ($this->mutateBeforeSave) {
            return app()->call($this->mutateBeforeSave, ['data' => $data]);
        }

        return $data;
    }

    // --- Validação ---

    /**
     * Monta as regras de validação de todos os campos visíveis.
     * Retorna no formato esperado pelo Validator do Laravel:
     * ['data.name' => ['required', 'max:255'], ...]
     */
    public function getValidationRules(array $named = []): array
    {
        $rules = [];

        foreach ($this->getFields($named) as $field) {
            $fieldRules = $field->getValidationRules($named);

            if (! empty($fieldRules)) {
                $rules[$field->getStatePath()] = $fieldRules;
            }
        }

        return $rules;
    }

    /**
     * Monta as mensagens customizadas de validação.
     * Retorna no formato: ['data.name.required' => 'O nome é obrigatório', ...]
     */
    public function getValidationMessages(): array
    {
        $messages = [];

        foreach ($this->getAllFields() as $field) {
            foreach ($field->getValidationMessages() as $rule => $message) {
                $messages[$field->getStatePath().'.'.$rule] = $message;
            }
        }

        return $messages;
    }

    /**
     * Monta os valores default de todos os campos.
     * Retorna: ['name' => '', 'type' => 'person', ...]
     */
    public function getDefaults(): array
    {
        $defaults = [];

        foreach ($this->getAllFields() as $field) {
            $default = $field->getDefaultValue();

            if ($default !== null) {
                $defaults[$field->getName()] = $default;
            }
        }

        return $defaults;
    }

    // --- Renderização ---

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('cord::forms.form', [
            'form' => $this,
            'fields' => $this->getFields(),
        ]);
    }

    public function __toString(): string
    {
        return $this->render()->render();
    }
}
