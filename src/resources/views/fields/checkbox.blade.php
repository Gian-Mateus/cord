@php
    /** @var \Cord\Forms\Fields\Checkbox $component */
@endphp

<div class="cord-field">
    <div class="cord-field__checkbox-wrapper">
        <input
            {{ $attributes->merge($wireModel) }}
            type="checkbox"
            id="{{ $component->getKey() }}"
            @if ($component->isDisabled()) disabled @endif
            class="cord-field__checkbox"
        />

        <label for="{{ $component->getKey() }}" class="cord-field__label cord-field__label--inline">
            {{ $component->getLabel() }}

            @if ($required)
                <span class="cord-field__required" aria-hidden="true">*</span>
            @endif
        </label>
    </div>

    @if ($component->getTooltip())
        <p class="cord-field__tooltip">{{ $component->getTooltip() }}</p>
    @endif

    @error($statePath)
        <p class="cord-field__error">{{ $message }}</p>
    @enderror
</div>
