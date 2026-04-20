@php
    /** @var \Cord\Forms\Fields\Radio $component */
@endphp

<fieldset class="cord-field">
    <legend class="cord-field__label">
        {{ $component->getLabel() }}

        @if ($required)
            <span class="cord-field__required" aria-hidden="true">*</span>
        @endif
    </legend>

    @if ($component->getTooltip())
        <p class="cord-field__tooltip">{{ $component->getTooltip() }}</p>
    @endif

    <div class="cord-field__radio-list {{ $component->isInline() ? 'cord-field__radio-list--inline' : '' }}">
        @foreach ($component->getOptions() as $value => $label)
            <div class="cord-field__radio-wrapper">
                <input
                    {{ $attributes->merge($wireModel) }}
                    type="radio"
                    id="{{ $component->getKey() }}_{{ $value }}"
                    value="{{ $value }}"
                    @if ($component->isDisabled()) disabled @endif
                    class="cord-field__radio"
                />

                <label for="{{ $component->getKey() }}_{{ $value }}" class="cord-field__label cord-field__label--inline">
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>

    @error($statePath)
        <p class="cord-field__error">{{ $message }}</p>
    @enderror
</fieldset>
