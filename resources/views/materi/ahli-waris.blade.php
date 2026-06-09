@extends('layouts.app')

@section('title', ucwords($ahliWaris->nama_ahli_waris) . ' — Materi Faraidh')

@section('content')
<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-6">
        {{-- BREADCRUMB --}}
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('materi.index') }}"
               class="text-gray-400 hover:text-blue-600 font-bold transition-colors">
                Materi Faraidh
            </a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-600 font-bold capitalize">{{ $ahliWaris->nama_ahli_waris }}</span>
        </div>

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-black text-blue-500 uppercase tracking-widest mb-1">
                            {{ $ahliWaris->kelompok }}
                        </p>
                        <h1 class="text-2xl font-black text-gray-900 capitalize">
                            {{ $ahliWaris->nama_ahli_waris }}
                        </h1>
                    </div>
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-black rounded-xl border border-blue-100 shrink-0">
                        Ahli Waris
                    </span>
                </div>
            </div>

            {{-- Aturan Pembagian --}}
            <div class="p-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                    <h2 class="text-sm font-black text-gray-700 uppercase tracking-wider">Aturan Pembagian</h2>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-xl p-4 border border-gray-100">
                    {{ $ahliWaris->deskripsi_aturan }}
                </p>
            </div>
        </div>

        {{-- PENGHALANG (HIJAB) --}}
        @if($ahliWaris->hijab_oleh)
        <div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-1 h-4 bg-rose-400 rounded-full"></span>
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-wider">Terhalang Oleh</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach(explode(',', $ahliWaris->hijab_oleh) as $penghalang)
                <span class="px-3 py-1 bg-rose-50 text-rose-600 text-xs font-bold rounded-xl border border-rose-100 capitalize">
                    {{ trim($penghalang) }}
                </span>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-3 leading-relaxed">
                Ahli waris ini tidak mendapatkan warisan apabila salah satu dari penghalang di atas hadir sebagai ahli waris.
            </p>
        </div>
        @endif

        {{-- DALIL --}}
        @if($ahliWaris->dalil_arab)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <span class="w-1 h-4 bg-emerald-500 rounded-full"></span>
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-wider">Dasar Hukum (Dalil)</h2>
            </div>
            <div class="p-6">
                <p class="text-right text-2xl leading-loose text-gray-800 mb-4 font-arabic" dir="rtl">
                    {{ $ahliWaris->dalil_arab }}
                </p>
                <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                    <p class="text-sm text-emerald-800 italic leading-relaxed">
                        "{{ $ahliWaris->dalil_terjemahan }}"
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- STUDI KASUS --}}
        @if($ahliWaris->studi_kasus && is_array($ahliWaris->studi_kasus) && count($ahliWaris->studi_kasus) > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <span class="w-1 h-4 bg-amber-400 rounded-full"></span>
                <h2 class="text-sm font-black text-gray-700 uppercase tracking-wider">Studi Kasus & Contoh Perhitungan</h2>
            </div>
            <div class="p-6 space-y-6">
                @foreach($ahliWaris->studi_kasus as $index => $kasus)
                <div class="border border-gray-100 rounded-xl overflow-hidden hover:border-amber-200 hover:shadow-sm transition-all">

                    {{-- Header Kasus --}}
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">
                            Kasus {{ $index + 1 }}
                        </span>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">
                            {{ $kasus['skenario'] }}
                        </p>
                    </div>

                    {{-- Tabel Perhitungan --}}
                    @if(isset($kasus['tabel_perhitungan']) && !empty($kasus['tabel_perhitungan']['baris']))
                    @php
                        $tabel     = $kasus['tabel_perhitungan'];
                        $hasTashih = !empty($tabel['tashih']);

                        $totalSaham = collect($tabel['baris'])->sum(fn($b) => (int)($b['saham'] ?? 0));
                        $totalSahamTashih = $hasTashih
                            ? collect($tabel['baris'])->sum(fn($b) => (int)($b['saham_tashih'] ?? 0))
                            : null;

                        $asalAktif  = $hasTashih ? $tabel['tashih'] : $tabel['asal_masalah'];
                        $totalAktif = $hasTashih ? $totalSahamTashih : $totalSaham;
                        $sisaHarta  = $asalAktif - $totalAktif;
                    @endphp
                    <div class="px-4 pt-4 pb-2">

                        {{-- ① Info Asal Masalah + Tashih DI ATAS tabel --}}
                        <div class="flex items-center gap-4 mb-3">
                            <div class="text-center">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Asal Masalah</p>
                                <p class="text-xl font-black text-gray-700">{{ $tabel['asal_masalah'] }}</p>
                            </div>
                            @if($hasTashih)
                            <div class="w-px h-8 bg-gray-200"></div>
                            <div class="text-center">
                                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Tashih</p>
                                <p class="text-xl font-black text-blue-600">{{ $tabel['tashih'] }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- ② Tabel ahli waris (tanpa thead asal masalah) --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border border-gray-200">
                                        <th class="px-3 py-2 text-left text-xs font-black text-gray-500 uppercase tracking-wider border-r border-gray-200 w-1/3">
                                            Ahli Waris
                                        </th>
                                        <th class="px-3 py-2 text-center text-xs font-black text-gray-500 uppercase tracking-wider border-r border-gray-200">
                                            Bagian
                                        </th>
                                        @if($hasTashih)
                                        <th class="px-3 py-2 text-center text-xs font-black text-gray-500 uppercase tracking-wider border-r border-gray-200">
                                            Saham
                                        </th>
                                        <th class="px-3 py-2 text-center text-xs font-black text-blue-400 uppercase tracking-wider">
                                            Saham Tashih
                                        </th>
                                        @else
                                        <th class="px-3 py-2 text-center text-xs font-black text-gray-500 uppercase tracking-wider">
                                            Saham
                                        </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Grouping baris berdasarkan bagian ashobah
                                        $grouped = [];
                                        foreach ($tabel['baris'] as $baris) {
                                            $bagian = strtolower($baris['bagian'] ?? '');
                                            $isAshobah = str_contains($bagian, 'ashobah');
                                            
                                            if ($isAshobah) {
                                                $key = $baris['bagian']; // group by bagian label
                                                $grouped[$key][] = $baris;
                                            } else {
                                                $grouped['__solo_' . $baris['ahli_waris']] = [$baris];
                                            }
                                        }
                                    @endphp

                                    @foreach($grouped as $key => $group)
                                        @php
                                            $isAshobahGroup = !str_starts_with($key, '__solo_');
                                            $groupCount = count($group);
                                            $totalSahamGroup = collect($group)->sum(fn($b) => (int)($b['saham'] ?? 0));
                                        @endphp

                                        @foreach($group as $i => $baris)
                                        @php
                                            $isMahjub = strtolower($baris['bagian']) === 'mahjub';
                                            $isFirst  = $i === 0;
                                        @endphp
                                        <tr class="border border-gray-200 {{ $isMahjub ? 'opacity-50' : '' }}">

                                            {{-- Kolom Ahli Waris: selalu per baris --}}
                                            <td class="px-3 py-2 capitalize font-semibold text-gray-700 border-r border-gray-200">
                                                {{ $baris['ahli_waris'] }}
                                            </td>

                                            {{-- Kolom Bagian: rowspan kalau ashobah group --}}
                                            @if($isFirst)
                                            <td class="px-3 py-2 text-center text-gray-600 border-r border-gray-200"
                                                @if($isAshobahGroup && $groupCount > 1) rowspan="{{ $groupCount }}" @endif>
                                                @if($isMahjub)
                                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-500 text-xs font-bold rounded-lg border border-rose-100">
                                                        Terhalang
                                                    </span>
                                                @else
                                                    {{ $baris['bagian'] }}
                                                @endif
                                            </td>
                                            @endif

                                            {{-- Kolom Saham & Saham Tashih: selalu per baris --}}
                                            @if($hasTashih)
                                            <td class="px-3 py-2 text-center font-bold text-gray-700 border-r border-gray-200">
                                                @if(!$isMahjub && $baris['saham'] > 0)
                                                    {{ $baris['saham'] }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-center font-bold text-blue-600">
                                                @if(!$isMahjub && isset($baris['saham_tashih']) && $baris['saham_tashih'] !== null)
                                                    {{ $baris['saham_tashih'] }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            @else
                                            <td class="px-3 py-2 text-center font-bold text-gray-700">
                                                @if(!$isMahjub && $baris['saham'] > 0)
                                                    {{ $baris['saham'] }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            @endif

                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Radd / Sisa --}}
                        @if($sisaHarta > 0)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-xs font-black text-rose-500 uppercase tracking-widest">
                                Radd = {{ $sisaHarta }}
                            </span>
                            <span class="text-xs text-gray-400">(diserahkan ke sabilillah / baitul maal)</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Penyelesaian (teks) --}}
                    <div class="px-4 py-3">
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">
                            Penyelesaian
                        </span>
                        <p class="text-sm text-gray-600 mt-0.5 leading-relaxed">
                            {{ $kasus['solusi'] }}
                        </p>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- NAVIGASI ANTAR AHLI WARIS --}}
        @php
            $allAhliWaris = \App\Models\EdukasiAhliWaris::orderBy('urutan')->get();
            $currentIndex = $allAhliWaris->search(fn($a) => $a->id === $ahliWaris->id);
            $prev = $currentIndex > 0 ? $allAhliWaris[$currentIndex - 1] : null;
            $next = $currentIndex < $allAhliWaris->count() - 1 ? $allAhliWaris[$currentIndex + 1] : null;
        @endphp
        <div class="flex justify-between items-center gap-4 pb-6">
            @if($prev)
            <a href="{{ route('materi.ahli-waris', $prev->slug) }}"
               class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:border-blue-300 hover:text-blue-600 transition-all shadow-sm capitalize">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ $prev->nama_ahli_waris }}
            </a>
            @else
            <div></div>
            @endif

            <a href="{{ route('materi.index') }}#ahliwaris"
               class="text-xs font-bold text-gray-400 hover:text-blue-600 transition-colors">
                Semua Ahli Waris
            </a>

            @if($next)
            <a href="{{ route('materi.ahli-waris', $next->slug) }}"
               class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:border-blue-300 hover:text-blue-600 transition-all shadow-sm capitalize">
                {{ $next->nama_ahli_waris }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @else
            <div></div>
            @endif
        </div>

    </div>
</div>
@endsection