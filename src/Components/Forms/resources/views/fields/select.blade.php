@php
    /** @var \Cord\Forms\Fields\Select $component */
@endphp

<div class="cord-field">
    <label for="{{ $component->getKey() }}" class="cord-field__label">
        {{ $component->getLabel() }}

        @if ($required)
            <span class="cord-field__required" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($component->getTooltip())
        <p class="cord-field__tooltip">{{ $component->getTooltip() }}</p>
    @endif

    <select
        {{ $attributes->merge($wireModel) }}
        id="{{ $component->getKey() }}"
        @if ($component->isDisabled()) disabled @endif
        @if ($component->isMultiple()) multiple @endif
        class="cord-field__input cord-field__select"
    >
        @if ($component->getPlaceholder())
            <option value="">{{ $component->getPlaceholder() }}</option>
        @endif

        @foreach ($component->getOptions() as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>

    @error($statePath)
        <p class="cord-field__error">{{ $message }}</p>
    @enderror
</div>
