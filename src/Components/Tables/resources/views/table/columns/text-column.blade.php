@php
    /** @var \Cord\Tables\Columns\TextColumn $component */
    $value = $component->getDisplayValue($record);
@endphp

@if ($component->isCopyable())
    <span
        x-data
        x-on:click="navigator.clipboard.writeText('{{ addslashes($component->getValue($record)) }}'); $store.notifications.notify('Copiado!')"
        role="button"
        class="cord-column__text cord-column__text--copyable"
        title="Clique para copiar"
    >
        {{ $value }}
    </span>
@else
    <span class="cord-column__text {{ ! $value ? 'cord-column__text--placeholder' : '' }}">
        {{ $value }}
    </span>
@endif
