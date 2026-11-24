<!DOCTYPE html>
<html lang="id" data-theme="gizicare">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title . ' · ' : '' }}GiziCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200">

    {{-- Bar atas untuk layar kecil --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand><span class="font-bold">GiziCare</span></x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden"><x-icon name="o-bars-3" class="cursor-pointer" /></label>
        </x-slot:actions>
    </x-nav>

    <x-main>
        <x-slot:sidebar drawer="main-drawer" class="bg-base-100 border-r border-base-300">
            <div class="px-5 pt-6 pb-4">
                <div class="text-xl font-bold">GiziCare</div>
                <div class="text-xs opacity-60">SIMRS · Gizi Rawat Inap</div>
            </div>

            @auth
                <x-menu activate-by-route>
                    @foreach (config('menu.' . auth()->user()->role, []) as $bagian => $items)
                        <x-menu-separator :title="$bagian" />
                        @foreach ($items as [$label, $ikon, $route])
                            <x-menu-item :title="$label" :icon="$ikon" :link="route($route)" />
                        @endforeach
                    @endforeach
                </x-menu>

                {{-- Kartu pengguna di bawah sidebar --}}
                <div class="m-3 mt-6 p-3 rounded-box bg-base-200 flex items-center gap-3">
                    <div class="avatar avatar-placeholder">
                        <div class="w-10 rounded-full bg-primary text-primary-content">
                            <span>{{ str(auth()->user()->name)->explode(' ')->take(2)->map(fn ($k) => $k[0])->join('') }}</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs opacity-60">{{ auth()->user()->role_label }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button icon="o-arrow-right-start-on-rectangle" type="submit" class="btn-ghost btn-sm" tooltip-left="Keluar" />
                    </form>
                </div>
            @endauth
        </x-slot:sidebar>

        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    <x-toast />
</body>
</html>