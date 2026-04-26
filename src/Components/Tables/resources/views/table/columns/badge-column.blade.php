@php
    /** @var \Cord\Tables\Columns\BadgeColumn $component */
    $value = $component->getDisplayValue($record);
    $color = $component->getColor($record);
@endphp

@if ($value)
    <span class="cord-column__badge cord-column__badge--{{ $color }}">
        {{ $value }}
    </span>
@endif
