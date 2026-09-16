@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->class([
        'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
        'bg-emerald-50 text-emerald-700' => $active,
        'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' => ! $active,
    ]) }}
>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
        {{ $icon }}
    </svg>
    {{ $slot }}
</a>
