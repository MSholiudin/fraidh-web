<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — WEWARIS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen flex flex-col md:flex-row">

        {{-- ================================================ --}}
        {{-- PANEL KIRI --}}
        {{-- ================================================ --}}
        <div class="hidden md:flex md:w-1/2 bg-blue-600 flex-col justify-between p-10 lg:p-14 relative overflow-hidden">

            {{-- Background decoration --}}
            <div class="absolute inset-0 opacity-10"
                 style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 24px 24px;">
            </div>
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-500 rounded-full opacity-30"></div>
            <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-blue-700 rounded-full opacity-40"></div>

            {{-- Logo --}}
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <img src="{{ asset('images/logo-nobg2.png') }}" alt="Logo" class="w-10 h-10">
                    <span class="text-2xl font-black text-white tracking-tight">WEWARIS</span>
                </a>
                <p class="mt-2 text-blue-200 text-sm">Kalkulator Pembagian Waris Islam</p>
            </div>

            {{-- Ayat --}}
            <div class="relative z-10 bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                <p class="text-right text-2xl leading-loose text-white mb-4 font-['Amiri']" dir="rtl">
                    "يُوصِيكُمُ ٱللَّهُ فِىٓ أَوْلَـٰدِكُمْ ۖ لِلذَّكَرِ مِثْلُ حَظِّ ٱلْأُنثَيَيْنِ"
                </p>
                <p class="text-blue-100 text-sm italic leading-relaxed">
                    "Allah mensyariatkan bagimu tentang pembagian pusaka untuk anak-anakmu. Bagian seorang anak laki-laki sama dengan bagian dua orang anak perempuan..."
                </p>
                <p class="mt-3 text-blue-300 text-xs font-black uppercase tracking-widest">QS. An-Nisa' : 11</p>
            </div>

            {{-- Fitur --}}
            <div class="relative z-10 grid grid-cols-2 gap-3">
                @foreach([
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Sesuai Syariat', 'desc' => 'Perhitungan akurat faraidh'],
                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Rekomendasi Islah', 'desc' => 'Pertimbangan kondisi Ekonomi'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'Simpan Riwayat', 'desc' => 'Akses hasil kapan saja'],
                    ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'Materi Faraidh', 'desc' => 'Belajar ilmu waris Islam'],
                ] as $f)
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10 flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-black">{{ $f['title'] }}</p>
                        <p class="text-blue-200 text-[10px]">{{ $f['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <p class="relative z-10 text-blue-300 text-xs">© 2026 WEWARIS. Memberikan kepastian dalam setiap pembagian.</p>
        </div>

        {{-- ================================================ --}}
        {{-- PANEL KANAN: FORM LOGIN --}}
        {{-- ================================================ --}}
        <div class="w-full md:w-1/2 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">

                {{-- Mobile logo --}}
                <div class="flex justify-center mb-8 md:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo-nobg2.png') }}" class="w-8 h-8">
                        <span class="text-xl font-black text-blue-600">WEWARIS</span>
                    </a>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

                    {{-- Header --}}
                    <div class="mb-8">
                        <h1 class="text-2xl font-black text-gray-900">Selamat Datang</h1>
                        <p class="text-gray-500 text-sm mt-1">Masuk untuk mengelola data waris</p>
                    </div>

                    <x-auth-session-status class="mb-4" :status="session('status')"/>

                    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPass: false }">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                       placeholder="nama@email.com"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition bg-gray-50 focus:bg-white">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1"/>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <input :type="showPass ? 'text' : 'password'" name="password" required
                                       placeholder="••••••••"
                                       class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition bg-gray-50 focus:bg-white">
                                <button type="button" @click="showPass = !showPass"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-500 transition-colors">
                                    <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1"/>
                        </div>

                        {{-- Remember + Lupa Password --}}
                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember"
                                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-gray-600 text-xs font-bold">Ingat saya</span>
                            </label>
                            @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-blue-600 text-xs font-bold hover:text-blue-700 transition-colors">
                                Lupa Password?
                            </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                class="w-full py-3 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 uppercase tracking-wider text-sm mt-2">
                            Masuk ke Akun
                        </button>
                    </form>

                    {{-- Footer --}}
                    <div class="mt-6 text-center space-y-4">
                        <p class="text-gray-500 text-sm">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="text-blue-600 font-black hover:underline underline-offset-4">
                                Daftar Sekarang
                            </a>
                        </p>
                        <a href="{{ route('home') }}"
                           class="inline-flex items-center gap-2 text-xs text-gray-400 hover:text-gray-700 transition-colors font-bold">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>