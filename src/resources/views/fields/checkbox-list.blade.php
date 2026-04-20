@php
    /** @var \Cord\Forms\Fields\Checkbox $component */
    $options = $component->getOptions();
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

    @if ($component->isBulk())
        <div class="cord-field__bulk" x-data>
            <button type="button" class="cord-field__bulk-btn"
                    x-on:click="$wire.set('{{ $statePath }}', {{ json_encode(array_keys($options)) }})">
                Selecionar todos
            </button>
            <button type="button" class="cord-field__bulk-btn"
                    x-on:click="$wire.set('{{ $statePath }}', [])">
                Limpar
            </button>
        </div>
    @endif

    <div class="cord-field__checkbox-list">
        @foreach ($options as $value => $label)
            <div class="cord-field__checkbox-wrapper">
                <input
                    {{ $attributes->merge($wireModel) }}
                    type="checkbox"
                    id="{{ $component->getKey() }}_{{ $value }}"
                    value="{{ $value }}"
                    @if ($component->isDisabled()) disabled @endif
                    class="cord-field__checkbox"
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
