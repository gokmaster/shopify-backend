<!DOCTYPE html>
<html lang="en" class="h-full bg-neutral-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'RS Market Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans text-neutral-800 antialiased">
    <div class="flex h-full min-h-screen">
        <aside class="hidden w-64 shrink-0 flex-col border-r border-neutral-200 bg-white lg:flex">
            <div class="flex items-center gap-2 px-6 py-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-white font-semibold">RS</span>
                <div class="leading-tight">
                    <p class="font-semibold text-neutral-900">RS Market</p>
                    <p class="text-xs text-neutral-400">Product Manager</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-2">
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </x-slot:icon>
                    Dashboard
                </x-nav-link>

                <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375C2.754 3.75 2.25 4.254 2.25 4.875v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </x-slot:icon>
                    Products
                </x-nav-link>

                <x-nav-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </x-slot:icon>
                    Categories
                </x-nav-link>

                <x-nav-link href="{{ route('products.import') }}" :active="request()->routeIs('products.import')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </x-slot:icon>
                    Import CSV
                </x-nav-link>

                <div class="!mt-6 border-t border-neutral-100 pt-3">
                    <x-nav-link href="{{ route('settings.shopify') }}" :active="request()->routeIs('settings.*')">
                        <x-slot:icon>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.752.43.992l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.752-.43-.992l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </x-slot:icon>
                        Shopify Settings
                    </x-nav-link>
                </div>
            </nav>

            <div class="border-t border-neutral-100 p-4 text-xs text-neutral-400">
                @if (\App\Services\Shopify\ShopifyCredentials::configured())
                    <span class="inline-flex items-center gap-1.5 text-emerald-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Shopify connected
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-amber-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Shopify not connected
                    </span>
                @endif
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between border-b border-neutral-200 bg-white px-4 py-4 lg:px-8">
                <h1 class="text-lg font-semibold text-neutral-900">{{ $header ?? 'Dashboard' }}</h1>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-white font-semibold lg:hidden">RS</div>
            </header>

            <main class="flex-1 px-4 py-6 lg:px-8">
                <x-flash-messages />

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
