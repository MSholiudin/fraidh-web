@extends('layouts.app')

@section('title', 'Materi Faraidh')

@section('content')
<div x-data="{
        sidebarOpen: false,
        isDesktop: window.innerWidth >= 1024,
        init() {
            window.addEventListener('resize', () => {
                this.isDesktop = window.innerWidth >= 1024;
                if (this.isDesktop) this.sidebarOpen = false;
            });
        }
     }"
     class="bg-gray-50">

    {{-- ================================================== --}}
    {{-- OVERLAY MOBILE --}}
    {{-- ================================================== --}}
    <div x-show="sidebarOpen && !isDesktop"
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-gray-900/40 backdrop-blur-sm">
    </div>

    {{-- ================================================== --}}
    {{-- SIDEBAR --}}
    {{-- ================================================== --}}
    <aside :class="(isDesktop || sidebarOpen) ? 'translate-x-0' : '-translate-x-full'"
       class="fixed top-16 left-0 z-40 h-[calc(100vh-4rem)] w-64 bg-white border-r border-gray-100 shadow-sm transition-transform duration-300 flex-shrink-0 isolate">
        <div class="h-full overflow-y-auto">
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
                        ['href' => '#dalil',      'label' => 'Dalil'],
                        ['href' => '#ahliwaris',  'label' => 'Aturan Pembagian Ahli Waris'],
                        ['href' => '#istilah',    'label' => 'Istilah Penting'],
                        ['href' => '#rukun',      'label' => 'Rukun Waris'],
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
        </div>
    </aside>

    {{-- ================================================== --}}
    {{-- MAIN CONTENT --}}
    {{-- ================================================== --}}
    <main class="flex-1 min-w-0 transition-all duration-300"
      	:style="isDesktop ? 'margin-left: 16rem;' : 'margin-left: 0;'">
        <div x-show="!isDesktop"
     		@click="sidebarOpen = true"
            class="fixed left-0 top-1/4 -translate-y-1/2 z-30 lg:hidden cursor-pointer"
            style="filter: drop-shadow(2px 0 6px rgba(0,0,0,0.08));">
            <div class="bg-white border border-l-0 border-gray-200 rounded-r-xl px-2 py-3 flex flex-col items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="text-gray-400 font-medium"
                    style="font-size:10px; writing-mode:vertical-rl; transform:rotate(180deg); letter-spacing:0.08em;">
                    NAVIGASI
                </span>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-6 py-8 space-y-8">
            <div class="pb-4 border-b border-gray-100 text-center">
                <h1 class="text-3xl font-black text-gray-900">Materi Faraidh</h1>
                <p class="text-sm text-gray-400 mt-1">Pelajari dasar-dasar ilmu waris Islam</p>
            </div>

            {{-- ============================================ --}}
            {{-- PENGERTIAN --}}
            {{-- ============================================ --}}
            <section id="pengertian" class="scroll-mt-10">
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
            </section>

            {{-- ============================================ --}}
            {{-- DALIL --}}
            {{-- ============================================ --}}
            <section id="dalil" class="scroll-mt-20">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900">Dalil</h2>
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
                            <p class="text-right text-2xl leading-loose text-gray-800 mb-4" 
   								style="font-family: 'Amiri', serif;" dir="rtl">
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
            {{-- SKEMA AHLI WARIS --}}
            {{-- ============================================ --}}
            <section id="ahliwaris" class="scroll-mt-20">
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-1 h-6 bg-rose-500 rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900">Aturan Pembagian Ahli Waris</h2>
                </div>
                <p class="text-sm text-gray-500 mb-6 ml-4">
                    Daftar aturan pembagian masing-masing ahli waris berdasarkan kelompoknya.
                </p>

                {{-- Grid Card Ahli Waris per Kelompok --}}
                <div class="mt-8 space-y-6">
                    @foreach($ahliWaris as $kelompok => $list)
                    <div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">{{ $kelompok }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($list as $ahli)
                            <a href="{{ route('materi.ahli-waris', $ahli->slug) }}"
                            class="flex flex-col gap-2 p-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all group">
                                
                                {{-- Baris atas: icon + nama --}}
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 group-hover:bg-blue-100 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-800 capitalize truncate">{{ $ahli->nama_ahli_waris }}</p>
                                </div>

                                {{-- Deskripsi --}}
                                <p class="text-xs text-gray-400 leading-relaxed line-clamp-2">{{ Str::limit($ahli->deskripsi_aturan, 60) }}</p>

                                {{-- CTA eksplisit --}}
                                <div class="flex items-center gap-1 text-xs font-bold text-blue-500 group-hover:text-blue-700 transition-colors mt-auto">
                                    <span>Lihat aturan lengkap</span>
                                    <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                            @endforeach
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
        </div>
    </main>
</div>
@endsection