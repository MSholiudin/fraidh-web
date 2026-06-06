@extends('layouts.app')

@section('title', 'Detail Riwayat — ' . $kasus->nama_mayit)

@section('content')
<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-6">

        {{-- BREADCRUMB --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('riwayat.index') }}"
               class="text-gray-400 hover:text-blue-600 font-bold transition-colors">
                Riwayat
            </a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-600 font-bold">{{ $kasus->nama_mayit }}</span>
        </div>

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Detail Riwayat</p>
                    <h1 class="text-2xl font-black text-gray-900">{{ $kasus->nama_mayit }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500">
                        <span>Harta Bersih: <strong class="text-emerald-600">Rp {{ number_format($kasus->harta_bersih, 0, ',', '.') }}</strong></span>
                        <span class="text-gray-300">|</span>
                        <span>Disimpan: <strong class="text-gray-700">{{ $kasus->created_at->translatedFormat('d F Y, H:i') }}</strong></span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('riwayat.pdf', $kasus->id) }}"
                    target="_blank"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all">
                        Cetak PDF
                    </a>
                    <form action="{{ route('riwayat.destroy', $kasus->id) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-rose-50 text-rose-500 border border-rose-100 rounded-xl text-sm font-bold hover:bg-rose-100 transition-all">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- HASIL FARAIDH --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm">Hasil Faraidh</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-black text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-3 text-left">Hubungan</th>
                            <th class="px-6 py-3 text-center">Porsi</th>
                            <th class="px-6 py-3 text-right">Nominal Diterima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($hasilFaraidh as $item)
                            @if(!Str::contains(strtolower($item['hubungan']), ['baitul maal', 'radd']))
                                @foreach(range(1, $item['jumlah']) as $index)
                                <tr class="hover:bg-blue-50/20 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800 capitalize">
                                        {{ $item['hubungan'] }} {{ $item['jumlah'] > 1 ? $index : '' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-lg border border-blue-100">
                                            {{ $item['bagian'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-blue-600 tabular-nums">
                                        Rp {{ number_format($item['nominal'] / $item['jumlah'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="hover:bg-blue-50/20 transition-colors bg-gray-50/30">
                                    <td class="px-6 py-4 font-bold text-gray-800 capitalize">
                                        {{ $item['hubungan'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-lg border border-blue-100">
                                            {{ $item['bagian'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-blue-600 tabular-nums">
                                        Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-200">
                            <td colspan="2" class="px-6 py-3 text-xs font-black text-gray-400 uppercase">Total Harta Terbagi</td>
                            <td class="px-6 py-3 text-right font-black text-blue-700 tabular-nums">
                                Rp {{ number_format($hasilFaraidh->sum('nominal'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- HASIL ISLAH (jika ada) --}}
        @if($hasilIslah)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="w-1 h-5 bg-purple-500 rounded-full"></span>
                <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm">Hasil Islah Ekonomi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-black text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-3 text-left">Ahli Waris</th>
                            <th class="px-6 py-3 text-center">Bobot</th>
                            <th class="px-6 py-3 text-right">Faraidh Murni</th>
                            <th class="px-6 py-3 text-right">Hasil Islah</th>
                            <th class="px-6 py-3 text-right">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($hasilIslah as $item)
                        @php
                            $selisih    = $item['total_islah'] - $item['faraidh'];
                            $pctSelisih = $kasus->harta_bersih > 0
                                ? ($selisih / $kasus->harta_bersih) * 100
                                : 0;
                            $bobot = $item['bobot'] ?? 0;
                        @endphp
                        <tr class="hover:bg-purple-50/20 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800 capitalize">
                                {{ $item['hubungan'] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-lg text-xs font-black tabular-nums
                                    @if($bobot >= 0.75) bg-emerald-100 text-emerald-700
                                    @elseif($bobot >= 0.5) bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-500 @endif">
                                    {{ number_format($bobot, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-700 tabular-nums">
                                Rp {{ number_format($item['faraidh'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-purple-700 tabular-nums">
                                Rp {{ number_format($item['total_islah'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($selisih > 0)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-black tabular-nums">
                                    +{{ number_format(abs($pctSelisih), 1) }}%
                                </span>
                                @elseif($selisih < 0)
                                <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-black tabular-nums">
                                    -{{ number_format(abs($pctSelisih), 1) }}%
                                </span>
                                @else
                                <span class="text-gray-400 text-xs font-bold">Tetap</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-200">
                            <td colspan="3" class="px-6 py-3 text-xs font-black text-gray-400 uppercase">Total</td>
                            <td class="px-6 py-3 text-right font-black text-purple-700 tabular-nums">
                                Rp {{ number_format($hasilIslah->sum('total_islah'), 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- TOMBOL --}}
        <div class="flex justify-between items-center pb-6">
            <a href="{{ route('riwayat.index') }}"
               class="flex items-center gap-2 text-gray-400 hover:text-gray-700 font-bold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Riwayat
            </a>
            <a href="{{ route('kalkulator.index') }}"
               class="px-6 py-2.5 bg-blue-600 text-white font-black rounded-xl text-sm hover:bg-blue-700 transition-all shadow-sm shadow-blue-200 active:scale-95">
                + Hitung Baru
            </a>
        </div>

    </div>
</div>
@endsection