<?php

namespace Cord\Context;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class PanelContext
{
    protected ?Authenticatable $user = null;

    protected ?string $tenant = null;

    protected ?Model $record = null;

    protected array $abilities = [];

    protected array $meta = [];

    // --- Mutações (só Livewire components fazem isso) ---

    public function setUser(Authenticatable $user): static
    {
        $this->user = $user;
        $this->abilities = [];

        return $this;
    }

    public function setTenant(string $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function setRecord(Model $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function set(string $key, mixed $value): static
    {
        $this->meta[$key] = $value;

        return $this;
    }

    // --- Leitura (builders e views consomem isso) ---

    public function user(): ?Authenticatable
    {
        return $this->user;
    }

    public function tenant(): ?string
    {
        return $this->tenant;
    }

    public function record(): ?Model
    {
        return $this->record;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->meta[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->meta);
    }

    // --- Autorização integrada (com cache por request) ---

    public function can(string $ability, mixed ...$arguments): bool
    {
        if (! $this->user) {
            return false;
        }

        $cacheKey = $ability.':'.serialize($arguments);

        return $this->abilities[$cacheKey] ??= $this->user->can($ability, ...$arguments);
    }

    public function cannot(string $ability, mixed ...$arguments): bool
    {
        return ! $this->can($ability, ...$arguments);
    }
}
