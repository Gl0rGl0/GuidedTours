@props(['status', 'showIcon' => false, 'classes' => ''])

@php
    $badgeConfig = match($status) {
        \App\Models\Visit::STATUS_EFFECTED => ['class' => 'bg-success text-white', 'icon' => 'check-circle'],
        \App\Models\Visit::STATUS_PROPOSED => ['class' => 'bg-primary text-white', 'icon' => 'calendar'],
        \App\Models\Visit::STATUS_CONFIRMED => ['class' => 'bg-success text-white', 'icon' => 'check-circle'],
        \App\Models\Visit::STATUS_COMPLETE => ['class' => 'bg-success-subtle text-success', 'icon' => 'flag'],
        \App\Models\Visit::STATUS_CANCELLED => ['class' => 'bg-danger text-white', 'icon' => 'x-circle'],
        default => ['class' => 'bg-secondary text-white', 'icon' => 'info-circle'],
    };
@endphp

<span class="badge {{ $badgeConfig['class'] }} rounded-pill px-3 py-2 text-uppercase {{ $classes }}">
    @if($showIcon)
        <i class="bi bi-{{ $badgeConfig['icon'] }} me-1"></i>
    @endif
    {{ __('messages.status.' . $status) }}
</span>
