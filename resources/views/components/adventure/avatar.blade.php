@props(['key' => 'astro', 'size' => 64])

@php
    $avatar = config("adventure.avatars.$key") ?? config('adventure.avatars.astro');
    $initial = strtoupper(substr($avatar['name'], 0, 1));
@endphp

<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 64 64" role="img" aria-label="{{ $avatar['name'] }}">
    <defs>
        <linearGradient id="g-{{ $key }}" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="{{ $avatar['color'] }}"/>
            <stop offset="100%" stop-color="{{ $avatar['color'] }}" stop-opacity="0.65"/>
        </linearGradient>
    </defs>
    <circle cx="32" cy="32" r="30" fill="url(#g-{{ $key }})"/>
    <circle cx="32" cy="26" r="13" fill="#fff" fill-opacity="0.92"/>
    <circle cx="27" cy="24" r="2.6" fill="#1e293b"/>
    <circle cx="37" cy="24" r="2.6" fill="#1e293b"/>
    <path d="M27 31 q5 4 10 0" stroke="#1e293b" stroke-width="2" fill="none" stroke-linecap="round"/>
    <path d="M12 58 q20 -14 40 0 z" fill="#fff" fill-opacity="0.35"/>
    <text x="32" y="58" text-anchor="middle" font-size="10" font-weight="700" fill="#fff">{{ $initial }}</text>
</svg>
