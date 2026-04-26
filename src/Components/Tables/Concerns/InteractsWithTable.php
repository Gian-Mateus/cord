<?php

namespace Cord\Tables\Concerns;

use Cord\Components\Tables\Table;

/**
 * Trait para componentes Livewire que gerenciam tabelas.
 *
 * O componente Livewire gerencia o estado de search/sort/pagination.
 * O Table builder é reconstruído a cada request.
 *
 * Uso:
 *   class ListUsers extends Component {
 *       use InteractsWithTable;
 *       public string $search = '';
 *       public string $sortColumn = '';
 *       public string $sortDirection = 'asc';
 *       public function table(Table $table): Table { ... }
 *   }
 */
trait InteractsWithTable
{
    public string $search = '';

    public string $sortColumn = '';

    public string $sortDirection = 'asc';

    public int $perPage = 15;

    public array $selectedRecords = [];

    // Cache do table builder dentro do mesmo request
    protected ?Table $cachedTable = null;

    /**
     * Deve ser implementado pelo componente.
     * Define as colunas e ações da tabela.
     */
    abstract public function table(Table $table): Table;

    /**
     * Reconstrói o Table builder.
     */
    public function getTable(): Table
    {
        if ($this->cachedTable) {
            return $this->cachedTable;
        }

        return $this->cachedTable = $this->table(Table::make());
    }

    /**
     * Retorna os records aplicando search, sort e pagination.
     */
    public function getTableRecords(): mixed
    {
        return $this->getTable()->applyQuery(
            search: $this->search ?: null,
            sortColumn: $this->sortColumn ?: null,
            sortDirection: $this->sortDirection,
            perPage: $this->perPage,
        );
    }

    /**
     * Chamado pelo wire:model do input de search.
     * Reset da paginação ao pesquisar.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Alterna sort na coluna.
     */
    public function sort(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Executa uma action de linha pelo nome.
     */
    public function executeAction(string $actionName, mixed $recordId): void
    {
        $table = $this->getTable();

        $action = collect($table->getActions())
            ->first(fn ($a) => $a->getName() === $actionName);

        if (! $action) {
            return;
        }

        $record = $table->getQuery()->find($recordId);

        if ($record) {
            $action->execute($record);
        }
    }

    /**
     * Executa uma bulk action com os records selecionados.
     */
    public function executeBulkAction(string $actionName): void
    {
        $table = $this->getTable();

        $action = collect($table->getBulkActions())
            ->first(fn ($a) => $a->getName() === $actionName);

        if (! $action || empty($this->selectedRecords)) {
            return;
        }

        $records = $table->getQuery()
            ->whereIn('id', $this->selectedRecords)
            ->get();

        $action->execute($records);

        $this->selectedRecords = [];
    }

    /**
     * Renderiza a tabela.
     */
    public function renderTable(): string
    {
        return $this->getTable()->render()->render();
    }
}
