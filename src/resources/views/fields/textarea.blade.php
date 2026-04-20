@php
    /** @var \Cord\Forms\Fields\Textarea $component */
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

    <textarea
        {{ $attributes->merge($wireModel) }}
        id="{{ $component->getKey() }}"
        rows="{{ $component->getRows() }}"
        @if ($component->getPlaceholder()) placeholder="{{ $component->getPlaceholder() }}" @endif
        @if ($component->getMaxLength()) maxlength="{{ $component->getMaxLength() }}" @endif
        @if ($component->isDisabled()) disabled @endif
        @if ($component->isReadonly()) readonly @endif
        @if ($component->isAutosize()) x-data x-init="
            $el.style.overflow = 'hidden';
            const resize = () => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'; };
            resize();
            $el.addEventListener('input', resize);
        " @endif
        class="cord-field__input cord-field__textarea"
    ></textarea>

    @if ($component->getMaxLength())
        <p class="cord-field__hint" x-data x-text="`${$wire.{{ $statePath }}?.length ?? 0} / {{ $component->getMaxLength() }}`"></p>
    @endif

    @error($statePath)
        <p class="cord-field__error">{{ $message }}</p>
    @enderror
</div>
