<?php

namespace Cord\Support\Concerns;

use Closure;

/**
 * @method static required(bool|\Closure $condition = true)
 * @method static nullable()
 * @method static string()
 * @method static integer()
 * @method static numeric()
 * @method static boolean()
 * @method static array()
 * @method static email()
 * @method static url()
 * @method static uuid()
 * @method static ip()
 * @method static date()
 * @method static min(int $value)
 * @method static max(int $value)
 * @method static between(int $min, int $max)
 * @method static size(int $value)
 * @method static digits(int $value)
 * @method static digitsBetween(int $min, int $max)
 * @method static minDigits(int $value)
 * @method static maxDigits(int $value)
 * @method static alpha()
 * @method static alphaNum()
 * @method static alphaDash()
 * @method static startsWith(string ...$values)
 * @method static endsWith(string ...$values)
 * @method static confirmed()
 * @method static same(string $field)
 * @method static different(string $field)
 * @method static in(string ...$values)
 * @method static notIn(string ...$values)
 * @method static regex(string $pattern)
 * @method static notRegex(string $pattern)
 * @method static accepted()
 * @method static declined()
 * @method static filled()
 * @method static present()
 * @method static prohibited()
 * @method static after(string $date)
 * @method static before(string $date)
 * @method static afterOrEqual(string $date)
 * @method static beforeOrEqual(string $date)
 * @method static image()
 * @method static mimes(string ...$types)
 * @method static mimetypes(string ...$types)
 */
trait HasValidation
{
    protected array $rules = [];

    protected array $messages = [];

    // --- API pública ---

    // Aceita array de regras — strings ou objetos Rule
    public function rules(array $rules, bool|Closure $condition = true): static
    {
        foreach ($rules as $rule) {
            $this->rules[] = ['rule' => $rule, 'condition' => $condition];
        }

        return $this;
    }

    // required() tem tratamento especial pois suporta condição
    public function required(bool|Closure $condition = true): static
    {
        $this->rules[] = ['rule' => 'required', 'condition' => $condition];

        return $this;
    }

    // nullable() sem condição — semântica diferente de required
    public function nullable(): static
    {
        return $this->rules(['nullable']);
    }

    // Regras com semântica específica que o __call() não resolveria bem
    public function unique(string $table, string $column = 'NULL'): static
    {
        return $this->rules(["unique:{$table},{$column}"]);
    }

    public function exists(string $table, string $column = 'id'): static
    {
        return $this->rules(["exists:{$table},{$column}"]);
    }

    // Mensagem customizada por regra
    public function validationMessage(string $rule, string $message): static
    {
        $this->messages[$rule] = $message;

        return $this;
    }

    // --- Resolução ---

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

    // --- Fallback para qualquer regra Laravel via __call() ---

    public function __call(string $name, array $arguments): static
    {

        /** 
         * Ver uma forma de usar o validator do laravel para verificar se a regra é válida para não gerar um erro silêncioso de regra inexistente.
         * 
        */

        // Converte camelCase para snake_case
        // minDigits(8) → 'min_digits:8'
        $rule = str($name)->snake()->toString();

        // Monta os parâmetros separados por vírgula
        // between(1, 100) → 'between:1,100'
        // email()         → 'email'
        if (! empty($arguments)) {
            $params = implode(',', $arguments);
            $rule = "{$rule}:{$params}";
        }

        return $this->rules([$rule]);
    }
}
