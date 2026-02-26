@props([
    'icon' => 'bi-info-circle',
    'title' => 'No results found',
    'message' => 'There are no items to display.',
    'actionText' => null,
    'actionUrl' => null,
    'actionIcon' => null,
    'card' => true
])

<div class="text-center py-5 {{ $card ? 'card border-0 shadow-sm rounded-4' : '' }}">
    <div class="{{ $card ? 'card-body' : '' }}">
        <i class="bi {{ $icon }} display-4 text-muted opacity-25 mb-3 d-block"></i>
        <h5 class="text-muted">{{ $title }}</h5>
        <p class="text-muted small mb-0">{{ $message }}</p>

        @if($actionText && $actionUrl)
            <a href="{{ $actionUrl }}" class="btn btn-outline-primary rounded-pill mt-3">
                @if($actionIcon)
                    <i class="bi {{ $actionIcon }} me-1"></i>
                @endif
                {{ $actionText }}
            </a>
        @endif
        
        {{ $slot }}
    </div>
</div>
