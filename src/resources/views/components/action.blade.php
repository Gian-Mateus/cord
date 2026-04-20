@php
    /** @var \Cord\Actions\Action $component */
@endphp

@if ($component->isLink())
    <a
        href="{{ $component->getUrl() }}"
        {{ $attributes }}
        class="cord-action cord-action--{{ $component->getColor() }} {{ $component->isOutlined() ? 'cord-action--outlined' : '' }}"
    >
        @if ($component->getIcon())
            <x-dynamic-component :component="$component->getIcon()" class="cord-action__icon" />
        @endif

        {{ $component->getLabel() }}
    </a>
@elseif ($component->doesRequireConfirmation())
    <div x-data="confirmable('{{ $component->getConfirmationMessage() }}')">
        <button
            type="button"
            x-on:click="confirm(() => { /* callback set by parent */ })"
            {{ $attributes }}
            class="cord-action cord-action--{{ $component->getColor() }} {{ $component->isOutlined() ? 'cord-action--outlined' : '' }}"
        >
            @if ($component->getIcon())
                <x-dynamic-component :component="$component->getIcon()" class="cord-action__icon" />
            @endif

            {{ $component->getLabel() }}
        </button>

        {{-- Confirmation modal --}}
        <div x-show="open" x-cloak class="cord-action__confirm-overlay">
            <div class="cord-action__confirm-modal">
                <p x-text="message" class="cord-action__confirm-message"></p>
                <div class="cord-action__confirm-actions">
                    <button type="button" x-on:click="cancel()" class="cord-action cord-action--gray cord-action--outlined">
                        Cancelar
                    </button>
                    <button type="button" x-on:click="proceed()" class="cord-action cord-action--{{ $component->getColor() }}">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
@else
    <button
        type="button"
        {{ $attributes }}
        class="cord-action cord-action--{{ $component->getColor() }} {{ $component->isOutlined() ? 'cord-action--outlined' : '' }}"
    >
        @if ($component->getIcon())
            <x-dynamic-component :component="$component->getIcon()" class="cord-action__icon" />
        @endif

        {{ $component->getLabel() }}
    </button>
@endif
