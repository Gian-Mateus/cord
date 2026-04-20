@php
    /** @var \Cord\Forms\Fields\Toggle $component */
@endphp

<div class="cord-field">
    <div class="cord-field__toggle-wrapper">
        <label for="{{ $component->getKey() }}" class="cord-field__label">
            {{ $component->getLabel() }}
        </label>

        <button
            type="button"
            role="switch"
            id="{{ $component->getKey() }}"
            x-data="{ checked: $wire.{{ $statePath }} ?? false }"
            x-on:click="checked = !checked; $wire.set('{{ $statePath }}', checked)"
            :aria-checked="checked.toString()"
            :class="checked ? 'cord-toggle--on' : 'cord-toggle--off'"
            @if ($component->isDisabled()) disabled @endif
            class="cord-toggle"
        >
            <span
                :class="checked ? 'cord-toggle__dot--on' : 'cord-toggle__dot--off'"
                class="cord-toggle__dot"
            ></span>
        </button>
    </div>

    @if ($component->getOnLabel() || $component->getOffLabel())
        <p class="cord-field__hint" x-data x-text="$wire.{{ $statePath }} ? '{{ $component->getOnLabel() }}' : '{{ $component->getOffLabel() }}'"></p>
    @endif

    @if ($component->getTooltip())
        <p class="cord-field__tooltip">{{ $component->getTooltip() }}</p>
    @endif

    @error($statePath)
        <p class="cord-field__error">{{ $message }}</p>
    @enderror
</div>
