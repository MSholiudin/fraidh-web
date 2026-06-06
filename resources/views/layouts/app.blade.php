<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WEWARIS — Kalkulator Faraidh')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Poppins', sans-serif; }

        /* WhatsApp float */
        .wa-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
        }
        .wa-tooltip {
            position: absolute;
            right: 60px;
            bottom: 50%;
            transform: translateY(50%);
            background: #1f2937;
            color: #fff;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 11px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            font-weight: 700;
        }
        .wa-float:hover .wa-tooltip { opacity: 1; }
    </style>

    @yield('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    {{-- NAVBAR --}}
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-40"
         x-data="{ mobileOpen: false }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <x-application-logo class="h-8 w-auto"/>
                    <span class="text-lg font-black text-blue-600 tracking-tight">WEWARIS</span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-1">
                    @foreach([
                        ['route' => 'home',            'label' => 'Beranda',    'match' => 'home'],
                        ['route' => 'materi.index',    'label' => 'Materi',     'match' => 'materi.*'],
                        ['route' => 'kalkulator.index','label' => 'Kalkulator', 'match' => 'kalkulator.*'],
                    ] as $nav)
                    <a href="{{ route($nav['route']) }}"
                       class="px-4 py-2 rounded-xl text-sm font-bold transition-colors
                              {{ request()->routeIs($nav['match'])
                                 ? 'bg-blue-50 text-blue-600'
                                 : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                        {{ $nav['label'] }}
                    </a>
                    @endforeach

                    @auth
                    <a href="{{ route('riwayat.index') }}" @click="mobileOpen = false"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-bold transition-colors
                            {{ request()->routeIs('riwayat.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        Riwayat
                    </a>
                    @endauth
                </div>

                {{-- Desktop Auth --}}
                <div class="hidden md:flex items-center gap-3">
                    @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all">
                                <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xs font-black">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs font-black text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')" class="text-sm">
                                Edit Profil
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();"
                                                 class="text-sm text-rose-600">
                                    Keluar
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                    @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-black text-gray-600 hover:text-blue-600 transition-colors">
                        Masuk
                    </a>
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 bg-blue-600 text-white text-sm font-black rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-200 active:scale-95">
                        Daftar
                    </a>
                    @endif
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <button @click="mobileOpen = !mobileOpen"
                        class="md:hidden p-2 rounded-xl hover:bg-gray-100 text-gray-600 transition-colors">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-gray-100 bg-white">

            <div class="px-4 py-3 space-y-1">
                @foreach([
                    ['route' => 'home',            'label' => 'Beranda',    'match' => 'home'],
                    ['route' => 'materi.index',    'label' => 'Materi',     'match' => 'materi.*'],
                    ['route' => 'kalkulator.index','label' => 'Kalkulator', 'match' => 'kalkulator.*'],
                ] as $nav)
                <a href="{{ route($nav['route']) }}" @click="mobileOpen = false"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm font-bold transition-colors
                          {{ request()->routeIs($nav['match'])
                             ? 'bg-blue-50 text-blue-600'
                             : 'text-gray-600 hover:bg-gray-50' }}">
                    {{ $nav['label'] }}
                </a>
                @endforeach

                @auth
                <a href="{{ route('riwayat.index') }}" @click="mobileOpen = false"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm font-bold transition-colors
                          {{ request()->routeIs('riwayat.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                @endauth
            </div>

            <div class="px-4 py-3 border-t border-gray-100">
                @auth
                <div class="flex items-center gap-3 px-3 py-2 mb-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-sm font-black">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" @click="mobileOpen = false"
                   class="flex items-center px-3 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">
                    Edit Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-bold text-rose-500 hover:bg-rose-50 transition-colors">
                        Keluar
                    </button>
                </form>
                @else
                <div class="flex gap-3">
                    <a href="{{ route('login') }}"
                       class="flex-1 text-center py-2.5 border border-gray-200 text-gray-700 font-black text-sm rounded-xl hover:bg-gray-50 transition-all">
                        Masuk
                    </a>
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="flex-1 text-center py-2.5 bg-blue-600 text-white font-black text-sm rounded-xl hover:bg-blue-700 transition-all">
                        Daftar
                    </a>
                    @endif
                </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- PAGE CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- WHATSAPP FLOAT --}}
    <a href="https://wa.me/6283853964821?text=Assalamu'alaikum%20saya%20ingin%20konsultasi%20waris"
       target="_blank"
       class="wa-float w-13 h-13 bg-green-500 hover:bg-green-600 text-white w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110 active:scale-95"
       aria-label="Konsultasi Pakar Waris">
        <span class="wa-tooltip">Konsultasi Pakar</span>
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.52 3.48A11.91 11.91 0 0012.07 0C5.43 0 .06 5.37.06 12c0 2.11.55 4.18 1.6 6.01L0 24l6.15-1.61A11.9 11.9 0 0012.07 24c6.63 0 12-5.37 12-12 0-3.19-1.24-6.19-3.55-8.52z"/>
        </svg>
    </a>
    @yield('scripts')
    @stack('scripts')

</body>
</html>