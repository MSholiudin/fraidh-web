@extends('layouts.app')

@section('title', 'Riwayat Perhitungan')

@section('content')
<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Akun Saya</p>
                    <h1 class="text-2xl font-black text-gray-900">Riwayat Perhitungan</h1>
                    <p class="text-sm text-gray-500 mt-1">Daftar semua perhitungan waris yang telah disimpan</p>
                </div>
                <a href="{{ route('kalkulator.index') }}"
                   class="px-4 py-2 bg-blue-600 text-white text-sm font-black rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-200 active:scale-95">
                    + Hitung Baru
                </a>
            </div>
        </div>

        {{-- SEARCH --}}
        <form method="GET" action="{{ route('riwayat.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama mayit..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 bg-gray-50"
                    />
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-black rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-200 active:scale-95">
                    Cari
                </button>
                @if(request('q'))
                <a href="{{ route('riwayat.index') }}"
                class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-black rounded-xl hover:bg-gray-200 transition-all">
                    Reset
                </a>
                @endif
            </div>
        </form>

        {{-- SUCCESS/ERROR MESSAGE --}}
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl px-5 py-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm font-bold text-emerald-700">{{ session('success') }}</p>
        </div>
        @endif

        {{-- DAFTAR RIWAYAT --}}
        @if($riwayat->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="font-black text-gray-800 mb-2">Belum Ada Riwayat</h3>
            <p class="text-sm text-gray-500 mb-6">Mulai perhitungan waris dan simpan hasilnya untuk dilihat kembali kapan saja.</p>
            <a href="{{ route('kalkulator.index') }}"
               class="px-6 py-2.5 bg-blue-600 text-white font-black rounded-xl text-sm hover:bg-blue-700 transition-all">
                Mulai Hitung Waris
            </a>
        </div>

        @else
        <div class="space-y-3">
            @foreach($riwayat as $item)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:border-blue-200 hover:shadow-md transition-all">
                <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                    {{-- Info Kasus --}}
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-black text-gray-900">{{ $item->nama_mayit }}</p>
                            <div class="flex flex-wrap items-center gap-3 mt-1">
                                <span class="text-xs text-gray-500">
                                    Harta Bersih: <strong class="text-gray-700">Rp {{ number_format($item->harta_bersih, 0, ',', '.') }}</strong>
                                </span>
                                <span class="text-gray-300">·</span>
                                <span class="text-xs text-gray-500">
                                    {{ $item->jumlah_ahli_waris }} Ahli Waris
                                </span>
                                <span class="text-gray-300">·</span>
                                <span class="text-xs text-gray-400">
                                    {{ $item->created_at->translatedFormat('d F Y') }}
                                </span>
                            </div>
                            {{-- Badge Islah --}}
                            @if($item->hasIslah())
                            <span class="inline-block mt-2 px-2 py-0.5 bg-purple-50 text-purple-600 text-[10px] font-black rounded-lg border border-purple-100">
                                + Islah Ekonomi
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('riwayat.show', $item->id) }}"
                           class="px-4 py-2 bg-blue-50 text-blue-600 text-xs font-black rounded-xl hover:bg-blue-100 transition-all">
                            Lihat Detail
                        </a>
                        <form action="{{ route('riwayat.destroy', $item->id) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-4 py-2 bg-rose-50 text-rose-500 text-xs font-black rounded-xl hover:bg-rose-100 transition-all">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($riwayat->hasPages())
        <div class="flex justify-center">
            {{ $riwayat->links('riwayat.pagination') }}
        </div>
        @endif
        @endif

    </div>
</div>
@endsection