@php
    /** @var \Cord\Tables\Table $table */
    $columns = $table->getColumns();
    $actions = $table->getActions();
    $bulkActions = $table->getBulkActions();
    $hasActions = ! empty($actions);
    $hasBulkActions = ! empty($bulkActions);
@endphp

<div class="cord-table" x-data="{ allSelected: false }">

    {{-- Search --}}
    @if (! empty($table->getSearchableColumns()))
        <div class="cord-table__search">
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Pesquisar..."
                class="cord-table__search-input"
            />
        </div>
    @endif

    {{-- Bulk actions --}}
    @if ($hasBulkActions)
        <div class="cord-table__bulk-actions" x-show="$wire.selectedRecords.length > 0" x-cloak>
            <span class="cord-table__bulk-count" x-text="$wire.selectedRecords.length + ' selecionado(s)'"></span>

            @foreach ($bulkActions as $bulkAction)
                <button
                    type="button"
                    wire:click="executeBulkAction('{{ $bulkAction->getName() }}')"
                    class="cord-table__bulk-btn cord-table__bulk-btn--{{ $bulkAction->getColor() }}"
                >
                    {{ $bulkAction->getLabel() }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- Table --}}
    <div class="cord-table__wrapper">
        <table class="cord-table__table">
            <thead class="cord-table__head">
                <tr>
                    @if ($hasBulkActions)
                        <th class="cord-table__th cord-table__th--checkbox">
                            <input
                                type="checkbox"
                                x-model="allSelected"
                                x-on:change="
                                    if (allSelected) {
                                        $wire.selectedRecords = {{ json_encode(
                                            $records instanceof \Illuminate\Pagination\LengthAwarePaginator
                                                ? $records->pluck('id')->all()
                                                : $records->pluck('id')->all()
                                        ) }};
                                    } else {
                                        $wire.selectedRecords = [];
                                    }
                                "
                                class="cord-field__checkbox"
                            />
                        </th>
                    @endif

                    @foreach ($columns as $column)
                        <th
                            class="cord-table__th {{ $column->getAlignment() ? 'cord-table__th--' . $column->getAlignment() : '' }}"
                            @if ($column->isSortable())
                                wire:click="sort('{{ $column->getName() }}')"
                                role="button"
                            @endif
                        >
                            <span class="cord-table__th-content">
                                {{ $column->getLabel() }}

                                @if ($column->isSortable())
                                    <span class="cord-table__sort-icon">
                                        @if ($sortColumn === $column->getName())
                                            {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                        @else
                                            ↕
                                        @endif
                                    </span>
                                @endif
                            </span>
                        </th>
                    @endforeach

                    @if ($hasActions)
                        <th class="cord-table__th cord-table__th--actions">Ações</th>
                    @endif
                </tr>
            </thead>

            <tbody class="cord-table__body">
                @forelse ($records as $record)
                    <tr class="cord-table__row" wire:key="record-{{ $record->getKey() }}">
                        @if ($hasBulkActions)
                            <td class="cord-table__td cord-table__td--checkbox">
                                <input
                                    type="checkbox"
                                    value="{{ $record->getKey() }}"
                                    wire:model.live="selectedRecords"
                                    class="cord-field__checkbox"
                                />
                            </td>
                        @endif

                        @foreach ($columns as $column)
                            <td class="cord-table__td {{ $column->getAlignment() ? 'cord-table__td--' . $column->getAlignment() : '' }}">
                                {!! $column->render()->with('record', $record) !!}
                            </td>
                        @endforeach

                        @if ($hasActions)
                            <td class="cord-table__td cord-table__td--actions">
                                <div class="cord-table__actions">
                                    @foreach ($actions as $action)
                                        @if ($action->isVisible(['record' => $record]))
                                            @if ($action->isLink())
                                                <a
                                                    href="{{ $action->getUrl($record) }}"
                                                    class="cord-table__action cord-table__action--{{ $action->getColor() }}"
                                                >
                                                    {{ $action->getLabel() }}
                                                </a>
                                            @elseif ($action->doesRequireConfirmation())
                                                <button
                                                    type="button"
                                                    x-data="confirmable('{{ $action->getConfirmationMessage() }}')"
                                                    x-on:click="confirm(() => $wire.executeAction('{{ $action->getName() }}', {{ $record->getKey() }}))"
                                                    class="cord-table__action cord-table__action--{{ $action->getColor() }}"
                                                >
                                                    {{ $action->getLabel() }}
                                                </button>
                                            @else
                                                <button
                                                    type="button"
                                                    wire:click="executeAction('{{ $action->getName() }}', {{ $record->getKey() }})"
                                                    class="cord-table__action cord-table__action--{{ $action->getColor() }}"
                                                >
                                                    {{ $action->getLabel() }}
                                                </button>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="{{ count($columns) + ($hasActions ? 1 : 0) + ($hasBulkActions ? 1 : 0) }}"
                            class="cord-table__empty"
                        >
                            {{ $table->getEmptyStateMessage() }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($table->isPaginated() && $records instanceof \Illuminate\Pagination\LengthAwarePaginator && $records->hasPages())
        <div class="cord-table__pagination">
            {{ $records->links() }}
        </div>
    @endif
</div>
