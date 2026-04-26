<?php

namespace Cord\Components\Tables;

use Closure;
use Cord\Actions\Action;
use Cord\Support\Concerns\EvaluatesClosures;
use Cord\Support\Concerns\HasFluentApi;
use Cord\Tables\Columns\Column;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class Table
{
    use EvaluatesClosures;
    use HasFluentApi;

    protected string $name = '';

    protected ?Builder $query = null;

    protected array $columns = [];

    protected array $actions = [];

    protected array $bulkActions = [];

    protected int $perPage = 15;

    protected array $perPageOptions = [10, 15, 25, 50];

    protected bool $isPaginated = true;

    protected ?string $defaultSort = null;

    protected string $defaultSortDirection = 'asc';

    protected ?string $emptyStateMessage = null;

    protected ?Closure $modifyQueryUsing = null;

    // --- Query ---

    public function query(Builder $query): static
    {
        return $this->set('query', $query);
    }

    public function getQuery(): ?Builder
    {
        return $this->query;
    }

    public function modifyQueryUsing(Closure $callback): static
    {
        return $this->set('modifyQueryUsing', $callback);
    }

    // --- Columns ---

    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Retorna apenas as colunas visíveis.
     *
     * @return Column[]
     */
    public function getColumns(array $named = []): array
    {
        return collect($this->columns)
            ->filter(fn (Column $column) => $column->isVisible($named))
            ->values()
            ->all();
    }

    /**
     * Retorna os nomes das colunas searchable para filtragem.
     */
    public function getSearchableColumns(): array
    {
        return collect($this->columns)
            ->filter(fn (Column $column) => $column->isSearchable())
            ->map(fn (Column $column) => $column->getName())
            ->values()
            ->all();
    }

    // --- Actions ---

    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return Action[]
     */
    public function getActions(array $named = []): array
    {
        return collect($this->actions)
            ->filter(fn (Action $action) => $action->isVisible($named))
            ->values()
            ->all();
    }

    public function bulkActions(array $actions): static
    {
        $this->bulkActions = $actions;

        return $this;
    }

    /**
     * @return Action[]
     */
    public function getBulkActions(array $named = []): array
    {
        return collect($this->bulkActions)
            ->filter(fn (Action $action) => $action->isVisible($named))
            ->values()
            ->all();
    }

    // --- Pagination ---

    public function perPage(int $perPage): static
    {
        return $this->set('perPage', $perPage);
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function perPageOptions(array $options): static
    {
        return $this->set('perPageOptions', $options);
    }

    public function getPerPageOptions(): array
    {
        return $this->perPageOptions;
    }

    public function paginated(bool $condition = true): static
    {
        return $this->set('isPaginated', $condition);
    }

    public function isPaginated(): bool
    {
        return $this->isPaginated;
    }

    // --- Default sort ---

    public function defaultSort(string $column, string $direction = 'asc'): static
    {
        $this->defaultSort = $column;
        $this->defaultSortDirection = $direction;

        return $this;
    }

    public function getDefaultSort(): ?string
    {
        return $this->defaultSort;
    }

    public function getDefaultSortDirection(): string
    {
        return $this->defaultSortDirection;
    }

    // --- Empty state ---

    public function emptyStateMessage(string $message): static
    {
        return $this->set('emptyStateMessage', $message);
    }

    public function getEmptyStateMessage(): string
    {
        return $this->emptyStateMessage ?? 'Nenhum registro encontrado.';
    }

    // --- Query execution ---

    /**
     * Aplica search, sort e pagination na query.
     * Retorna os records prontos para renderização.
     */
    public function applyQuery(
        ?string $search = null,
        ?string $sortColumn = null,
        ?string $sortDirection = null,
        ?int $perPage = null,
    ): mixed {
        $query = clone $this->query;

        // Aplica modificador custom
        if ($this->modifyQueryUsing) {
            $this->evaluate($this->modifyQueryUsing, ['query' => $query]);
        }

        // Search
        if ($search) {
            $searchable = $this->getSearchableColumns();

            if (! empty($searchable)) {
                $query->where(function (Builder $q) use ($search, $searchable) {
                    foreach ($searchable as $column) {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }
        }

        // Sort
        $sortColumn ??= $this->defaultSort;
        $sortDirection ??= $this->defaultSortDirection;

        if ($sortColumn) {
            $query->orderBy($sortColumn, $sortDirection);
        }

        // Pagination
        if ($this->isPaginated) {
            return $query->paginate($perPage ?? $this->perPage);
        }

        return $query->get();
    }

    // --- Renderização ---

    public function render(): View
    {
        return view('cord::table.table', [
            'table' => $this,
        ]);
    }

    public function __toString(): string
    {
        return $this->render()->render();
    }
}
