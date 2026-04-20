<?php

namespace Cord\Actions;

use Closure;
use Cord\Support\Abstracts\Component;

class Action extends Component
{
    protected string|Closure|null $url = null;

    protected ?string $method = null;

    protected string $color = 'primary';

    protected ?string $icon = null;

    protected bool $isOutlined = false;

    protected bool $requiresConfirmation = false;

    protected ?string $confirmationMessage = null;

    protected ?Closure $action = null;

    public function getView(): string
    {
        return 'cord::components.action';
    }

    // --- URL (para navegação) ---

    public function url(string|Closure $url): static
    {
        return $this->set('url', $url);
    }

    public function getUrl(mixed $record = null): ?string
    {
        return $this->evaluate($this->url, ['record' => $record]);
    }

    // --- HTTP Method (para forms) ---

    public function method(string $method): static
    {
        return $this->set('method', strtoupper($method));
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    // --- Color ---

    public function color(string $color): static
    {
        return $this->set('color', $color);
    }

    public function danger(): static
    {
        return $this->color('danger');
    }

    public function warning(): static
    {
        return $this->color('warning');
    }

    public function success(): static
    {
        return $this->color('success');
    }

    public function getColor(): string
    {
        return $this->color;
    }

    // --- Icon ---

    public function icon(string $icon): static
    {
        return $this->set('icon', $icon);
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    // --- Outlined ---

    public function outlined(bool $condition = true): static
    {
        return $this->set('isOutlined', $condition);
    }

    public function isOutlined(): bool
    {
        return $this->isOutlined;
    }

    // --- Confirmation ---

    public function requiresConfirmation(bool $condition = true): static
    {
        return $this->set('requiresConfirmation', $condition);
    }

    public function confirmationMessage(string $message): static
    {
        $this->requiresConfirmation = true;

        return $this->set('confirmationMessage', $message);
    }

    public function doesRequireConfirmation(): bool
    {
        return $this->requiresConfirmation;
    }

    public function getConfirmationMessage(): string
    {
        return $this->confirmationMessage ?? 'Tem certeza que deseja continuar?';
    }

    // --- Action callback ---

    public function action(Closure $callback): static
    {
        return $this->set('action', $callback);
    }

    public function getAction(): ?Closure
    {
        return $this->action;
    }

    /**
     * Executa a action com o record como contexto.
     */
    public function execute(mixed $record = null): mixed
    {
        if (! $this->action) {
            return null;
        }

        return $this->evaluate($this->action, ['record' => $record]);
    }

    /**
     * Verifica se a action é um link (URL) ou um callback.
     */
    public function isLink(): bool
    {
        return $this->url !== null;
    }
}
