@php
    /** @var \Cord\Forms\Fields\TextInput $component */
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

    <div class="cord-field__input-wrapper">
        @if ($component->getPrefix())
            <span class="cord-field__addon cord-field__addon--prefix">{{ $component->getPrefix() }}</span>
        @endif

        <input
            {{ $attributes->merge($wireModel) }}
            type="{{ $component->getType() }}"
            id="{{ $component->getKey() }}"
            @if ($component->getPlaceholder()) placeholder="{{ $component->getPlaceholder() }}" @endif
            @if ($component->getMaxLength()) maxlength="{{ $component->getMaxLength() }}" @endif
            @if ($component->isDisabled()) disabled @endif
            @if ($component->isReadonly()) readonly @endif
            class="cord-field__input"
        />

        @if ($component->getSuffix())
            <span class="cord-field__addon cord-field__addon--suffix">{{ $component->getSuffix() }}</span>
        @endif
    </div>

    @error($statePath)
        <p class="cord-field__error">{{ $message }}</p>
    @enderror
</div>
