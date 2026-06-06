@extends('layouts.app')

@section('title', 'Beranda — WEWARIS')

@section('content')
<div class="bg-gray-50 min-h-screen">

    {{-- ================================================== --}}
    {{-- HERO --}}
    {{-- ================================================== --}}
    <div class="bg-blue-600 relative overflow-hidden">
        {{-- Background decoration --}}
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 24px 24px;">
        </div>
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-500 rounded-full opacity-30"></div>
        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-blue-700 rounded-full opacity-40"></div>

        <div class="relative z-10 max-w-5xl mx-auto px-6 py-16 md:py-20">
            <div class="max-w-2xl">
                <p class="text-blue-200 text-xs font-black uppercase tracking-widest mb-3">
                    Sistem Pakar Faraidh
                </p>
                <h1 class="text-3xl md:text-4xl font-black text-white leading-tight mb-4">
                    Kalkulator Pembagian<br>Waris Islam
                </h1>
                <p class="text-blue-100 text-sm leading-relaxed mb-8 max-w-lg">
                    Hitung pembagian warisan sesuai syariat Islam secara otomatis dan akurat,
                    dilengkapi rekomendasi islah berbasis kondisi ekonomi ahli waris.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('kalkulator.index') }}"
                       class="px-6 py-3 bg-white text-blue-600 font-black rounded-xl text-sm hover:bg-blue-50 transition-all shadow-lg active:scale-95">
                        Mulai Hitung Waris →
                    </a>
                    <a href="{{ route('materi.index') }}"
                       class="px-6 py-3 bg-white/10 text-white font-black rounded-xl text-sm hover:bg-white/20 transition-all border border-white/20 active:scale-95">
                        Pelajari Faraidh
                    </a>
                </div>
            </div>
        </div>

        {{-- Ayat --}}
        <div class="relative z-10 max-w-5xl mx-auto px-6 pb-10">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/20 max-w-2xl">
                <p class="text-right text-lg leading-loose text-white mb-2 font-['Amiri']" dir="rtl">
                    "يُوصِيكُمُ ٱللَّهُ فِىٓ أَوْلَـٰدِكُمْ ۖ لِلذَّكَرِ مِثْلُ حَظِّ ٱلْأُنثَيَيْنِ"
                </p>
                <p class="text-blue-100 text-xs italic leading-relaxed">
                    "Allah mensyariatkan bagimu tentang pembagian pusaka untuk anak-anakmu. Bagian seorang anak laki-laki sama dengan bagian dua orang anak perempuan..."
                </p>
                <p class="text-blue-300 text-[10px] font-black uppercase tracking-widest mt-2">QS. An-Nisa' : 11</p>
            </div>
        </div>
    </div>

    {{-- ================================================== --}}
    {{-- FITUR UTAMA --}}
    {{-- ================================================== --}}
    <div class="max-w-5xl mx-auto px-6 py-12 space-y-12">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach([
                [
                    'title'  => 'Materi Faraidh',
                    'desc'   => 'Pelajari ilmu waris Islam dari dasar hingga kompleks — pengertian, dalil, istilah, dan aturan tiap ahli waris.',
                    'link'   => route('materi.index'),
                    'label'  => 'Pelajari',
                    'color'  => 'blue',
                    'icon'   => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                ],
                [
                    'title'  => 'Kalkulator Waris',
                    'desc'   => 'Hitung pembagian waris sesuai hukum Islam secara otomatis. Hasil akurat dengan penjelasan aturan tiap ahli waris.',
                    'link'   => route('kalkulator.index'),
                    'label'  => 'Hitung',
                    'color'  => 'emerald',
                    'icon'   => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                ],
                [
                    'title'  => 'Ahli Waris',
                    'desc'   => 'Kenali 16 ahli waris dalam Islam beserta aturan pembagian, dalil, dan contoh kasus masing-masing.',
                    'link'   => route('materi.index') . '#ahliwaris',
                    'label'  => 'Lihat Skema',
                    'color'  => 'amber',
                    'icon'   => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                ],
            ] as $f)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:border-{{ $f['color'] }}-200 hover:shadow-md transition-all group">
                <div class="w-10 h-10 bg-{{ $f['color'] }}-50 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-{{ $f['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-black text-gray-900 mb-2 text-sm uppercase tracking-tight">{{ $f['title'] }}</h3>
                <p class="text-xs text-gray-500 leading-relaxed mb-4">{{ $f['desc'] }}</p>
                <a href="{{ $f['link'] }}"
                   class="text-{{ $f['color'] }}-600 text-xs font-black hover:underline underline-offset-4">
                    {{ $f['label'] }} →
                </a>
            </div>
            @endforeach
        </div>

        {{-- ================================================== --}}
        {{-- ALUR PENGGUNAAN --}}
        {{-- ================================================== --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <div class="flex items-center gap-3 mb-8">
                <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                <h2 class="font-black text-gray-900 text-sm uppercase tracking-tight">Cara Menggunakan WEWARIS</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach([
                    ['step' => '1', 'title' => 'Input Data Pewaris', 'desc' => 'Masukkan nama pewaris, jenis kelamin, dan total harta warisan.', 'color' => 'blue'],
                    ['step' => '2', 'title' => 'Pilih Ahli Waris', 'desc' => 'Centang ahli waris yang ada beserta jumlahnya.', 'color' => 'blue'],
                    ['step' => '3', 'title' => 'Lihat Hasil Faraidh', 'desc' => 'Sistem menghitung bagian tiap ahli waris sesuai syariat.', 'color' => 'emerald'],
                    ['step' => '4', 'title' => 'Rekomendasi Islah', 'desc' => 'Opsional — input kondisi ekonomi untuk rekomendasi musyawarah.', 'color' => 'purple'],
                ] as $s)
                <div class="flex flex-col items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-{{ $s['color'] }}-100 text-{{ $s['color'] }}-600 flex items-center justify-center text-sm font-black shrink-0">
                        {{ $s['step'] }}
                    </div>
                    <div>
                        <p class="font-black text-gray-800 text-sm mb-1">{{ $s['title'] }}</p>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $s['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- ================================================== --}}
        {{-- CTA LOGIN (hanya untuk guest) --}}
        {{-- ================================================== --}}
        @guest
        <div class="bg-blue-600 rounded-2xl p-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10"
                 style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h3 class="text-white font-black text-lg mb-1">Daftar untuk Simpan Riwayat</h3>
                    <p class="text-blue-200 text-sm">Login untuk menyimpan hasil perhitungan dan menggunakan fitur islah ekonomi.</p>
                </div>
                <div class="flex gap-3 shrink-0">
                    <a href="{{ route('login') }}"
                       class="px-5 py-2.5 bg-white/10 text-white font-black rounded-xl text-sm hover:bg-white/20 transition-all border border-white/20">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-5 py-2.5 bg-white text-blue-600 font-black rounded-xl text-sm hover:bg-blue-50 transition-all shadow-lg">
                        Daftar Gratis
                    </a>
                </div>
            </div>
        </div>
        @endguest

        {{-- ================================================== --}}
        {{-- DISCLAIMER --}}
        {{-- ================================================== --}}
        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 flex gap-4">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <p class="text-amber-900 font-black text-sm mb-1">Pernyataan Penting</p>
                <p class="text-amber-700 text-xs leading-relaxed">
                    Sistem ini adalah alat bantu pembelajaran dan perhitungan faraidh. Untuk keputusan hukum yang mengikat,
                    tetap konsultasikan dengan ulama atau ahli hukum waris Islam yang kompeten.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection