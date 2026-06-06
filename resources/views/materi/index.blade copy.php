@extends('layouts.app')

@section('title', 'Materi Faraidh')

@section('content')
<div x-data="{ sidebarOpen: false }" class="flex min-h-screen bg-gray-50">

    {{-- ================================================== --}}
    {{-- OVERLAY MOBILE --}}
    {{-- ================================================== --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-900/40 backdrop-blur-sm lg:hidden">
    </div>

    {{-- ================================================== --}}
    {{-- SIDEBAR --}}
    {{-- ================================================== --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:sticky top-0 left-0 z-50 h-screen w-64 bg-white border-r border-gray-100 shadow-sm transition-transform duration-300 flex-shrink-0 overflow-y-auto">

        <div class="p-5">
            {{-- Header Sidebar --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Navigasi Materi</p>
                <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-lg hover:bg-gray-100 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Nav: Teori --}}
            <nav class="space-y-1 mb-6">
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest px-3 mb-2">Teori Dasar</p>
                @foreach([
                    ['href' => '#pengertian', 'label' => 'Pengertian Faraidh'],
                    ['href' => '#dalil',      'label' => 'Dalil Utama'],
                    ['href' => '#istilah',    'label' => 'Istilah Penting'],
                    ['href' => '#rukun',      'label' => 'Rukun Waris'],
                    ['href' => '#ahliwaris',  'label' => 'Skema Ahli Waris'],
                ] as $nav)
                <a href="{{ $nav['href'] }}"
                   @click="if(window.innerWidth < 1024) sidebarOpen = false"
                   class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors font-medium">
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    {{ $nav['label'] }}
                </a>
                @endforeach
            </nav>

            {{-- Nav: Ahli Waris --}}
            <div x-data="{ open: true }">
                <button @click="open = !open"
                        class="flex items-center justify-between w-full px-3 py-2 text-[10px] font-black text-gray-300 uppercase tracking-widest mb-1">
                    <span>Detail Ahli Waris</span>
                    <svg :class="open ? 'rotate-180' : ''" class="w-3 h-3 transition-transform text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" class="space-y-0.5 max-h-64 overflow-y-auto">
                    @foreach($ahliWaris->flatten() as $ahli)
                    <a href="{{ route('materi.ahli-waris', $ahli->slug) }}"
                       class="flex items-center gap-2 px-3 py-1.5 text-xs text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors capitalize">
                        <span class="w-1 h-1 rounded-full bg-gray-200"></span>
                        {{ $ahli->nama_ahli_waris }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </aside>

    {{-- ================================================== --}}
    {{-- MAIN CONTENT --}}
    {{-- ================================================== --}}
    <main class="flex-1 min-w-0">

        {{-- Topbar mobile --}}
        <div class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-gray-100 px-4 py-3 flex items-center gap-3 lg:hidden">
            <button @click="sidebarOpen = true" class="p-2 rounded-xl hover:bg-gray-100 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-sm font-black text-gray-700">Materi Faraidh</span>
        </div>

        <div class="max-w-4xl mx-auto px-6 py-10 space-y-16">

            {{-- ============================================ --}}
            {{-- PENGERTIAN --}}
            {{-- ============================================ --}}
            <section id="pengertian" class="scroll-mt-20">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900">Pengertian Faraidh</h2>
                </div>

                @foreach($materi->get('pengertian', collect()) as $item)
                <div class="mb-6">
                    <h3 class="text-base font-black text-gray-800 mb-2">{{ $item->judul }}</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">{{ $item->konten }}</p>
                </div>
                @endforeach

                {{-- Hadis box --}}
                <div class="mt-6 p-5 bg-blue-50 border-l-4 border-blue-500 rounded-r-2xl">
                    <p class="text-blue-800 text-sm italic leading-relaxed">
                        "Pelajarilah Al-Qur'an dan ajarkanlah kepada orang-orang, pelajarilah ilmu faraidh dan ajarkanlah..."
                    </p>
                    <p class="text-blue-500 text-xs font-bold mt-2">HR. Tirmidzi & Nasa'i</p>
                </div>
            </section>

            {{-- ============================================ --}}
            {{-- DALIL --}}
            {{-- ============================================ --}}
            <section id="dalil" class="scroll-mt-20">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900">Dalil Utama</h2>
                </div>

                <div class="space-y-4">
                    @foreach($materi->get('dalil', collect()) as $item)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-3">
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-black rounded-lg">Dalil</span>
                            <h3 class="text-sm font-black text-gray-800">{{ $item->judul }}</h3>
                        </div>
                        <div class="p-5">
                            {{-- Pisahkan teks Arab dan terjemahan --}}
                            @php
                                $parts = explode("\n\n", $item->konten, 2);
                                $arab  = $parts[0] ?? '';
                                $indo  = $parts[1] ?? '';
                            @endphp
                            @if($arab)
                            <p class="text-right text-xl leading-loose text-gray-800 mb-4 font-arabic" dir="rtl">
                                {{ $arab }}
                            </p>
                            @endif
                            @if($indo)
                            <p class="text-sm text-gray-600 leading-relaxed italic border-t border-gray-100 pt-4">
                                {{ $indo }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ============================================ --}}
            {{-- ISTILAH --}}
            {{-- ============================================ --}}
            <section id="istilah" class="scroll-mt-20">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-1 h-6 bg-purple-500 rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900">Istilah Penting</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($materi->get('istilah', collect()) as $item)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:border-purple-200 transition-colors">
                        <h3 class="font-black text-blue-600 mb-2 text-sm">{{ $item->judul }}</h3>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $item->konten }}</p>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ============================================ --}}
            {{-- RUKUN --}}
            {{-- ============================================ --}}
            <section id="rukun" class="scroll-mt-20">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-1 h-6 bg-amber-500 rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900">Rukun & Ketentuan Waris</h2>
                </div>

                <div class="space-y-4">
                    @foreach($materi->get('contoh', collect()) as $item)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h3 class="font-black text-gray-800 mb-3 text-sm flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-600 text-[10px] font-black flex items-center justify-center">
                                {{ $loop->iteration }}
                            </span>
                            {{ $item->judul }}
                        </h3>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $item->konten }}</p>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- ============================================ --}}
            {{-- SKEMA AHLI WARIS --}}
            {{-- ============================================ --}}
            <section id="ahliwaris" class="scroll-mt-20">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-1 h-6 bg-rose-500 rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900">Skema Ahli Waris</h2>
                </div>
                <p class="text-sm text-gray-500 mb-6 ml-4">
                    Klik pada ikon untuk melihat detail aturan pembagian masing-masing ahli waris.
                </p>

                {{-- Pohon Silsilah --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 overflow-x-auto">
                    <div class="min-w-[800px]">

                        @php
                        // Helper: cari ahli waris by nama
                        $cari = fn($nama) => $ahliWaris->flatten()->firstWhere('nama_ahli_waris', $nama);

                        // Node component helper
                        $node = function($nama, $icon, $size = 'sm', $color = 'gray') use ($cari) {
                            $ahli = $cari($nama);
                            $url  = $ahli ? route('materi.ahli-waris', $ahli->slug) : '#';
                            $wh   = $size === 'lg' ? 'w-16 h-16' : 'w-11 h-11';
                            $border = [
                                'blue'   => 'border-blue-400',
                                'pink'   => 'border-pink-400',
                                'purple' => 'border-purple-400',
                                'green'  => 'border-green-400',
                                'orange' => 'border-orange-400',
                                'gray'   => 'border-gray-300',
                            ][$color] ?? 'border-gray-300';
                            return ['url' => $url, 'wh' => $wh, 'border' => $border, 'icon' => $icon];
                        };
                        @endphp

                        {{-- LEVEL 1: KAKEK NENEK --}}
                        <div class="flex justify-center gap-16 mb-2">
                            {{-- Nenek Ibu --}}
                            @php $n = $cari('nenek pihak ibu'); @endphp
                            <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                               class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                <img src="{{ asset('images/nenek.png') }}" class="w-11 h-11 rounded-full border-2 border-pink-300 bg-white shadow-sm group-hover:border-pink-500">
                                <span class="text-[9px] font-bold text-gray-500 text-center">Nenek<br>(Pihak Ibu)</span>
                            </a>

                            <div class="w-24"></div>{{-- spacer --}}

                            {{-- Kakek --}}
                            @php $n = $cari('kakek'); @endphp
                            <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                               class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                <img src="{{ asset('images/kakek.png') }}" class="w-11 h-11 rounded-full border-2 border-blue-300 bg-white shadow-sm group-hover:border-blue-500">
                                <span class="text-[9px] font-bold text-gray-500 text-center">Kakek<br>(Pihak Bapak)</span>
                            </a>

                            {{-- Nenek Bapak --}}
                            @php $n = $cari('nenek pihak bapak'); @endphp
                            <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                               class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                <img src="{{ asset('images/nenek.png') }}" class="w-11 h-11 rounded-full border-2 border-pink-300 bg-white shadow-sm group-hover:border-pink-500">
                                <span class="text-[9px] font-bold text-gray-500 text-center">Nenek<br>(Pihak Bapak)</span>
                            </a>
                        </div>

                        {{-- Garis ke level 2 --}}
                        <div class="flex justify-center mb-2">
                            <div class="w-px h-6 bg-gray-300"></div>
                        </div>

                        {{-- LEVEL 2: IBU BAPAK + SAUDARA --}}
                        <div class="flex items-start justify-center gap-6">

                            {{-- Saudara Seibu --}}
                            <div class="flex flex-col items-center gap-1 border border-orange-100 bg-orange-50 rounded-2xl p-3 mt-8">
                                <span class="text-[8px] font-black text-orange-500 uppercase tracking-wider mb-1">Jalur Seibu</span>
                                @php $n = $cari('saudara seibu'); @endphp
                                <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                   class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                    <div class="flex gap-2">
                                        <img src="{{ asset('images/suami.png') }}" class="w-9 h-9 rounded-full border border-orange-200 bg-white">
                                        <img src="{{ asset('images/istri.png') }}" class="w-9 h-9 rounded-full border border-orange-200 bg-white">
                                    </div>
                                    <span class="text-[8px] text-gray-500 text-center">Saudara<br>Seibu</span>
                                </a>
                            </div>

                            {{-- Ibu --}}
                            <div class="flex flex-col items-center">
                                @php $n = $cari('ibu'); @endphp
                                <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                   class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                    <img src="{{ asset('images/istri.png') }}" class="w-16 h-16 rounded-full border-4 border-pink-400 bg-white shadow-md group-hover:border-pink-600">
                                    <span class="text-xs font-black text-pink-600">IBU</span>
                                </a>
                            </div>

                            {{-- Saudara Kandung --}}
                            <div class="flex flex-col items-center border border-blue-100 bg-blue-50 rounded-2xl p-3 mt-8">
                                <span class="text-[8px] font-black text-blue-500 uppercase tracking-wider mb-1">Kandung</span>
                                <div class="flex gap-2">
                                    @php $n = $cari('saudara laki-laki sekandung'); @endphp
                                    <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                       class="flex flex-col items-center gap-1 hover:scale-110 transition-transform">
                                        <img src="{{ asset('images/suami.png') }}" class="w-9 h-9 rounded-full border border-blue-200 bg-white">
                                        <span class="text-[7px] text-gray-500 text-center">Sdr<br>Lk</span>
                                    </a>
                                    @php $n = $cari('saudara perempuan sekandung'); @endphp
                                    <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                       class="flex flex-col items-center gap-1 hover:scale-110 transition-transform">
                                        <img src="{{ asset('images/istri.png') }}" class="w-9 h-9 rounded-full border border-blue-200 bg-white">
                                        <span class="text-[7px] text-gray-500 text-center">Sdr<br>Pr</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Bapak --}}
                            <div class="flex flex-col items-center">
                                @php $n = $cari('bapak'); @endphp
                                <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                   class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                    <img src="{{ asset('images/suami.png') }}" class="w-16 h-16 rounded-full border-4 border-blue-400 bg-white shadow-md group-hover:border-blue-600">
                                    <span class="text-xs font-black text-blue-600">BAPAK</span>
                                </a>
                            </div>

                            {{-- Saudara Sebapak --}}
                            <div class="flex flex-col items-center border border-green-100 bg-green-50 rounded-2xl p-3 mt-8">
                                <span class="text-[8px] font-black text-green-500 uppercase tracking-wider mb-1">Sebapak</span>
                                <div class="flex gap-2">
                                    @php $n = $cari('saudara laki-laki sebapak'); @endphp
                                    <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                       class="flex flex-col items-center gap-1 hover:scale-110 transition-transform">
                                        <img src="{{ asset('images/suami.png') }}" class="w-9 h-9 rounded-full border border-green-200 bg-white">
                                        <span class="text-[7px] text-gray-500 text-center">Sdr<br>Lk</span>
                                    </a>
                                    @php $n = $cari('saudara perempuan sebapak'); @endphp
                                    <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                       class="flex flex-col items-center gap-1 hover:scale-110 transition-transform">
                                        <img src="{{ asset('images/istri.png') }}" class="w-9 h-9 rounded-full border border-green-200 bg-white">
                                        <span class="text-[7px] text-gray-500 text-center">Sdr<br>Pr</span>
                                    </a>
                                </div>
                            </div>

                        </div>

                        {{-- Garis ke Pewaris --}}
                        <div class="flex justify-center my-2">
                            <div class="w-px h-6 bg-gray-300"></div>
                        </div>

                        {{-- LEVEL 3: PEWARIS + PASANGAN --}}
                        <div class="flex items-center justify-center gap-6">
                            {{-- Suami --}}
                            @php $n = $cari('suami'); @endphp
                            <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                               class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                <img src="{{ asset('images/suami.png') }}" class="w-14 h-14 rounded-full border-4 border-blue-400 bg-white shadow-md group-hover:border-blue-600">
                                <span class="text-xs font-black text-blue-600">Suami</span>
                            </a>

                            <div class="flex flex-col gap-1 items-center">
                                <div class="w-12 h-px border-t-2 border-dashed border-gray-300"></div>
                            </div>

                            {{-- Pewaris (almarhum) --}}
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <img src="{{ asset('images/suami.png') }}" class="w-16 h-16 rounded-full border-2 border-dashed border-gray-400 bg-white opacity-40 grayscale">
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[7px] font-black px-1.5 py-0.5 rounded-full">Wafat</span>
                                </div>
                                <span class="text-[10px] font-black text-gray-400 mt-1">Pewaris</span>
                            </div>

                            <div class="flex flex-col gap-1 items-center">
                                <div class="w-12 h-px border-t-2 border-dashed border-gray-300"></div>
                            </div>

                            {{-- Istri --}}
                            @php $n = $cari('istri'); @endphp
                            <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                               class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                <img src="{{ asset('images/istri.png') }}" class="w-14 h-14 rounded-full border-4 border-pink-400 bg-white shadow-md group-hover:border-pink-600">
                                <span class="text-xs font-black text-pink-600">Istri</span>
                            </a>
                        </div>

                        {{-- Garis ke Anak --}}
                        <div class="flex justify-center my-2">
                            <div class="w-px h-6 bg-gray-300"></div>
                        </div>

                        {{-- LEVEL 4: ANAK --}}
                        <div class="flex justify-center gap-16">

                            {{-- Anak Laki-laki --}}
                            <div class="flex flex-col items-center">
                                @php $n = $cari('anak laki-laki'); @endphp
                                <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                   class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                    <img src="{{ asset('images/anak_lk.png') }}" class="w-14 h-14 rounded-full border-4 border-blue-400 bg-white shadow-md group-hover:border-blue-600">
                                    <span class="text-xs font-black text-blue-600">Anak Laki-laki</span>
                                </a>

                                <div class="w-px h-5 bg-gray-300 my-1"></div>

                                {{-- Cucu --}}
                                <div class="flex gap-4 p-3 bg-blue-50 border border-blue-100 rounded-2xl">
                                    @php $n = $cari('cucu laki-laki'); @endphp
                                    <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                       class="flex flex-col items-center gap-1 hover:scale-110 transition-transform">
                                        <img src="{{ asset('images/anak_lk.png') }}" class="w-10 h-10 rounded-full border border-blue-200 bg-white">
                                        <span class="text-[8px] text-gray-500 text-center">Cucu<br>Laki-laki</span>
                                    </a>
                                    @php $n = $cari('cucu perempuan'); @endphp
                                    <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                       class="flex flex-col items-center gap-1 hover:scale-110 transition-transform">
                                        <img src="{{ asset('images/anak_pr.png') }}" class="w-10 h-10 rounded-full border border-pink-200 bg-white">
                                        <span class="text-[8px] text-gray-500 text-center">Cucu<br>Perempuan</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Anak Perempuan --}}
                            <div class="flex flex-col items-center">
                                @php $n = $cari('anak perempuan'); @endphp
                                <a href="{{ $n ? route('materi.ahli-waris', $n->slug) : '#' }}"
                                   class="flex flex-col items-center gap-1 hover:scale-110 transition-transform group">
                                    <img src="{{ asset('images/anak_pr.png') }}" class="w-14 h-14 rounded-full border-4 border-pink-400 bg-white shadow-md group-hover:border-pink-600">
                                    <span class="text-xs font-black text-pink-600">Anak Perempuan</span>
                                </a>
                                <p class="text-[8px] text-gray-400 mt-2 text-center italic max-w-[90px]">
                                    Keturunan anak perempuan bukan ahli waris
                                </p>
                            </div>
                        </div>

                        {{-- Legenda --}}
                        <div class="flex items-center justify-center gap-6 mt-8 pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full border-2 border-blue-400 bg-white"></div>
                                <span class="text-[10px] text-gray-500">Laki-laki</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full border-2 border-pink-400 bg-white"></div>
                                <span class="text-[10px] text-gray-500">Perempuan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full border-2 border-dashed border-gray-400 bg-white opacity-50"></div>
                                <span class="text-[10px] text-gray-500">Pewaris (wafat)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-px border-t-2 border-dashed border-gray-400"></div>
                                <span class="text-[10px] text-gray-500">Ikatan pernikahan</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Grid Card Ahli Waris per Kelompok --}}
                <div class="mt-8 space-y-6">
                    @foreach($ahliWaris as $kelompok => $list)
                    <div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">{{ $kelompok }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($list as $ahli)
                            <a href="{{ route('materi.ahli-waris', $ahli->slug) }}"
                               class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-blue-300 hover:shadow-md transition-all group">
                                <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 shrink-0">    @php
                                        $ikonMap = [
                                            'suami'                       => ['icon' => 'fa-user', 'color' => 'text-blue-500'],
                                            'istri'                       => ['icon' => 'fa-user', 'color' => 'text-pink-500'],
                                            'bapak'                       => ['icon' => 'fa-user', 'color' => 'text-blue-500'],
                                            'ibu'                         => ['icon' => 'fa-user', 'color' => 'text-pink-500'],
                                            'anak-laki-laki'              => ['icon' => 'fa-user', 'color' => 'text-blue-400'],
                                            'anak-perempuan'              => ['icon' => 'fa-user', 'color' => 'text-pink-400'],
                                            'cucu-laki-laki'              => ['icon' => 'fa-user', 'color' => 'text-blue-300'],
                                            'cucu-perempuan'              => ['icon' => 'fa-user', 'color' => 'text-pink-300'],
                                            'kakek'                       => ['icon' => 'fa-user', 'color' => 'text-blue-600'],
                                            'nenek-pihak-bapak'           => ['icon' => 'fa-user', 'color' => 'text-pink-600'],
                                            'nenek-pihak-ibu'             => ['icon' => 'fa-user', 'color' => 'text-pink-600'],
                                            'saudara-seibu'               => ['icon' => 'fa-users', 'color' => 'text-purple-400'],
                                            'saudara-laki-laki-sekandung' => ['icon' => 'fa-users', 'color' => 'text-blue-500'],
                                            'saudara-perempuan-sekandung' => ['icon' => 'fa-users', 'color' => 'text-pink-500'],
                                            'saudara-laki-laki-sebapak'   => ['icon' => 'fa-users', 'color' => 'text-blue-400'],
                                            'saudara-perempuan-sebapak'   => ['icon' => 'fa-users', 'color' => 'text-pink-400'],
                                        ];
                                        $ikon  = $ikonMap[$ahli->slug]['icon']  ?? 'fa-user';
                                        $warna = $ikonMap[$ahli->slug]['color'] ?? 'text-blue-500';
                                    @endphp
                                    <i class="fa-solid {{ $ikon }} {{ $warna }} text-lg"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 capitalize truncate">{{ $ahli->nama_ahli_waris }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ Str::limit($ahli->deskripsi_aturan, 50) }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

        </div>
    </main>
</div>
@endsection