<?php

namespace Cord\Support\Abstracts;

use Closure;
use Cord\Support\Concerns\HasValidation;

abstract class Field extends Component
{
    use HasValidation;
    protected string $statePath = '';

    protected bool $isLive = false;

    protected bool $isLazy = false;

    protected mixed $default = null;

    protected array $rules = [];

    protected array $messages = [];

    // --- State path ---

    public function prefixStatePath(string $prefix): void
    {
        $this->statePath = $prefix.'.'.$this->getName();
    }

    public function getStatePath(): string
    {
        return $this->statePath ?: $this->getName();
    }

    // --- Wire model ---

    public function live(bool $condition = true): static
    {
        return $this->set('isLive', $condition);
    }

    public function lazy(): static
    {
        return $this->set('isLazy', true);
    }

    public function getWireModel(): string
    {
        $modifier = match (true) {
            $this->isLive => '.live',
            $this->isLazy => '.lazy',
            default => '',
        };

        return "wire:model{$modifier}=\"{$this->getStatePath()}\"";
    }

    // --- Default ---

    public function default(mixed $value): static
    {
        return $this->set('default', $value);
    }

    public function getDefaultValue(): mixed
    {
        return $this->evaluate($this->default);
    }

    // --- Validation ---

    public function required(bool|Closure $condition = true): static
    {
        return $this->rule('required', $condition);
    }

    public function email(): static
    {
        return $this->rule('email');
    }

    public function min(int $value): static
    {
        return $this->rule("min:{$value}");
    }

    public function max(int $value): static
    {
        return $this->rule("max:{$value}");
    }

    public function rule(string|object $rule, bool|Closure $condition = true): static
    {
        $this->rules[] = ['rule' => $rule, 'condition' => $condition];

        return $this;
    }

    public function validationMessages(array $messages): static
    {
        $this->messages = array_merge($this->messages, $messages);

        return $this;
    }

    public function getValidationRules(array $named = []): array
    {
        return collect($this->rules)
            ->filter(fn ($item) => (bool) $this->evaluate($item['condition'], $named))
            ->map(fn ($item) => $item['rule'])
            ->values()
            ->all();
    }

    public function getValidationMessages(): array
    {
        return $this->messages;
    }

    public function isRequired(): bool
    {
        return collect($this->rules)
            ->filter(fn ($item) => $item['rule'] === 'required')
            ->filter(fn ($item) => (bool) $this->evaluate($item['condition']))
            ->isNotEmpty();
    }

    // --- View data ---

    public function viewData(): array
    {
        return array_merge(parent::viewData(), [
            'statePath' => $this->getStatePath(),
            'wireModel' => $this->getWireModel(),
            'required' => $this->isRequired(),
        ]);
    }
}
