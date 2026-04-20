<?php

namespace Cord\Forms\Concerns;

use Cord\Forms\Form;

/**
 * Trait para componentes Livewire que gerenciam formulários.
 *
 * O componente Livewire usa $data como estado.
 * O Form builder é reconstruído a cada request — nunca entra no snapshot.
 *
 * Uso:
 *   class EditUser extends Component {
 *       use InteractsWithForms;
 *       public array $data = [];
 *       public function form(Form $form): Form { ... }
 *       public function save(): void { $data = $this->validateForm(); ... }
 *   }
 */
trait InteractsWithForms
{
    // Cache do form builder dentro do mesmo request
    protected ?Form $cachedForm = null;

    /**
     * Deve ser implementado pelo componente.
     * Define o schema do formulário.
     */
    abstract public function form(Form $form): Form;

    /**
     * Reconstrói o Form builder.
     * Chamado cada vez que o componente precisa do form.
     */
    public function getForm(): Form
    {
        if ($this->cachedForm) {
            return $this->cachedForm;
        }

        return $this->cachedForm = $this->form(Form::make());
    }

    /**
     * Inicializa $data com os defaults dos campos.
     * Chamar no mount() do componente.
     */
    public function fillFormDefaults(): void
    {
        $defaults = $this->getForm()->getDefaults();

        $this->data = array_merge($defaults, $this->data);
    }

    /**
     * Preenche $data com os valores de um model.
     * Chamar no mount() do componente para edição.
     */
    public function fillFormFromModel(mixed $model, ?array $only = null): void
    {
        $fields = $this->getForm()->getAllFields();

        $attributes = $only ?? collect($fields)
            ->map(fn ($field) => $field->getName())
            ->all();

        foreach ($attributes as $attribute) {
            $this->data[$attribute] = $model->{$attribute} ?? null;
        }
    }

    /**
     * Valida $data usando as regras do Form builder.
     * Retorna os dados validados (só os campos do form).
     */
    public function validateForm(): array
    {
        $form = $this->getForm();

        // Aplica mutação pré-validação
        $data = $form->applyMutateBeforeValidation($this->data);

        // Valida com as regras dos campos
        $validated = $this->validate(
            rules: $form->getValidationRules(),
            messages: $form->getValidationMessages(),
        );

        // Extrai apenas os dados do statePath do form
        $statePath = $form->getStatePath();

        return data_get($validated, $statePath, []);
    }

    /**
     * Valida e aplica mutação pré-save.
     * Retorna dados prontos para persistir.
     */
    public function getFormData(): array
    {
        $data = $this->validateForm();

        return $this->getForm()->applyMutateBeforeSave($data);
    }

    /**
     * Renderiza o form.
     * Usar na view: {!! $this->renderForm() !!}
     */
    public function renderForm(): string
    {
        return $this->getForm()->render()->render();
    }
}
