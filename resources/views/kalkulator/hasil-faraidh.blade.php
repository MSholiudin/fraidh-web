@extends('layouts.app')

@section('title', 'Hasil Faraidh')

@section('content')
<div class="py-10 bg-gray-50 min-h-screen" x-data="{}">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Sistem Pakar Faraidh</p>
                    <h1 class="text-2xl font-black text-gray-900">Hasil Perhitungan Faraidh</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500">
                        <span>Pewaris: <strong class="text-gray-800">{{ $namaMayit }}</strong></span>
                        <span class="text-gray-300">|</span>
                        <span>Harta Bersih: <strong class="text-emerald-600">Rp {{ number_format($hartaBersih, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL RINGKASAN --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm">Tabel Distribusi Ahli Waris</h2>
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
                        @foreach($hasil as $item)
                            @if($item['hubungan'] === 'catatan') @continue @endif

                            @if(!Str::contains(strtolower($item['hubungan']), ['baitul maal', 'radd']))
                                {{-- Jika Ahli Waris Manusia (Anak, Istri, dll), tampilkan per orang --}}
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
                                {{-- Jika Baitul Maal / Radd, tampilkan 1 baris saja tanpa angka jumlah --}}
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
                                @php
                                    $totalTerbagi = array_sum(array_map(
                                        fn($item) => $item['hubungan'] !== 'catatan' ? $item['nominal'] : 0,
                                        $hasil
                                    ));
                                @endphp
                                Rp {{ number_format($totalTerbagi, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- CATATAN KHUSUS --}}
        @foreach($hasil as $item)
            @if($item['hubungan'] === 'catatan')
            <div class="bg-amber-50 rounded-2xl border border-amber-200 shadow-sm p-5 flex gap-4">
                <div class="shrink-0 w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black text-amber-700 mb-1">{{ $item['bagian'] }}</p>
                    <p class="text-xs text-amber-600 leading-relaxed">{{ $item['catatan'] }}</p>
                </div>
            </div>
            @endif
        @endforeach

        {{-- DETAIL PERHITUNGAN FARAIDH --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="w-1 h-5 bg-indigo-500 rounded-full"></span>
                <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm">
                    Detail Perhitungan Faraidh
                </h2>
            </div>

            @php
                $items        = $detailPerhitungan['items']         ?? [];
                $asalMasalah  = $detailPerhitungan['asal_masalah']  ?? null;
                $tashih       = $detailPerhitungan['tashih']        ?? null;
                $aul          = $detailPerhitungan['aul']           ?? null;
                $aulTashih    = $detailPerhitungan['aul_tashih']    ?? null;
                $isAkdariyah  = $detailPerhitungan['is_akdariyah']  ?? false;
                $isGharrawain = $detailPerhitungan['is_gharrawain'] ?? false;
                $isMuqosamah  = $detailPerhitungan['is_muqosamah']  ?? false;

                $adaTashih    = !is_null($tashih) || !is_null($aulTashih);
                $adaSahamAwal = $adaTashih || collect($items)->contains(fn($i) => ($i['jumlah'] ?? 1) > 1);

                $isAshobahLabel = fn($b) => str_contains(strtolower($b ?? ''), 'ashobah')
                                        || str_contains(strtolower($b ?? ''), 'musytarakah');

                // Precompute blok ashobah
                $ashobahItems       = array_values(array_filter($items, fn($i) => $isAshobahLabel($i['bagian'])));
                $ashobahTotalBaris  = array_sum(array_column($ashobahItems, 'jumlah'));
                $ashobahSahamAwal   = $ashobahItems[0]['saham_awal'] ?? null;
                $ashobahBlockRendered = false;

                // Tashih untuk header: aul_tashih (Akdariyah) atau tashih biasa
                $tashihHeader = $aulTashih ?? $tashih;

                $fmt = fn($val) => is_null($val) ? '-'
                    : (fmod(round((float)$val, 6), 1) == 0
                        ? number_format((float)$val, 0)
                        : rtrim(number_format((float)$val, 2, '.', ''), '0'));
            @endphp

            {{-- ===================== HEADER ANGKA ===================== --}}
           <div class="p-6 border-b bg-gray-50">
                <div class="flex flex-wrap items-end gap-4 mb-3">

                    <div class="text-center">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Asal Masalah</p>
                        <p class="text-4xl font-black text-gray-700">{{ $asalMasalah ?? '-' }}</p>
                    </div>

                    @if($aul)
                    <div class="w-px h-10 bg-gray-200 mb-1"></div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">'Aul</p>
                        <p class="text-4xl font-black text-rose-500">{{ $aul }}</p>
                    </div>
                    @endif

                    @if($tashihHeader)
                    <div class="w-px h-10 bg-gray-200 mb-1"></div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">{{ $aulTashih ? 'Aul Tashih' : 'Tashih' }}</p>
                        <p class="text-4xl font-black text-emerald-600">{{ $tashihHeader }}</p>
                    </div>
                    @endif

                </div>
            </div>

            {{-- ===================== TABEL ===================== --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Ahli Waris</th>
                            <th class="px-4 py-3 text-center">Bagian</th>
                            @if($adaSahamAwal)
                            <th class="px-4 py-3 text-center">Saham Awal</th>
                            @endif
                            @if($adaTashih)
                            <th class="px-4 py-3 text-center">Saham Setelah Tashih</th>
                            @endif
                            <th class="px-4 py-3 text-center">Saham / Orang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                        @foreach($items as $item)
                            @php $isAsh = $isAshobahLabel($item['bagian']); @endphp

                            @for($i = 1; $i <= $item['jumlah']; $i++)
                            <tr class="{{ $isAsh ? 'bg-indigo-50/30' : '' }} hover:bg-gray-50/50">

                                {{-- Nama --}}
                                <td class="px-4 py-3 font-semibold">
                                    {{ $item['hubungan'] }}
                                    @if($item['jumlah'] > 1) {{ $i }} @endif
                                </td>

                                @if($isAsh)
                                    {{-- Ashobah: Bagian & Saham Awal rowspan seluruh blok --}}
                                    @if(!$ashobahBlockRendered && $i == 1)
                                        @php $ashobahBlockRendered = true; @endphp
                                        <td rowspan="{{ $ashobahTotalBaris }}"
                                            class="px-4 py-3 text-center align-middle font-semibold text-indigo-700 bg-indigo-50/50">
                                            Ashobah
                                        </td>
                                        @if($adaSahamAwal)
                                        <td rowspan="{{ $ashobahTotalBaris }}"
                                            class="px-4 py-3 text-center align-middle font-semibold bg-indigo-50/50">
                                            {{ $fmt($ashobahSahamAwal) }}
                                        </td>
                                        @endif
                                        @if($adaTashih)
                                        <td rowspan="{{ $ashobahTotalBaris }}"
                                            class="px-4 py-3 text-center align-middle font-semibold bg-indigo-50/50">
                                            {{ $fmt(array_sum(array_column($ashobahItems, 'saham_akhir'))) }}
                                        </td>
                                        @endif
                                    @endif
                                    <td class="px-4 py-3 text-center font-bold text-blue-600">
                                        {{ $fmt($item['saham_per_orang']) }}
                                    </td>

                                @else
                                    {{-- Furudh biasa --}}
                                    @if($i == 1)
                                        <td rowspan="{{ $item['jumlah'] }}"
                                            class="px-4 py-3 text-center align-middle">
                                            {{ $item['bagian'] }}
                                        </td>
                                        @if($adaSahamAwal)
                                        <td rowspan="{{ $item['jumlah'] }}"
                                            class="px-4 py-3 text-center align-middle">
                                            {{ $fmt($item['saham_awal']) }}
                                        </td>
                                        @endif
                                        @if($adaTashih)
                                        <td rowspan="{{ $item['jumlah'] }}"
                                            class="px-4 py-3 text-center align-middle">
                                            {{ $fmt($item['saham_akhir']) }}
                                        </td>
                                        @endif
                                    @endif
                                    <td class="px-4 py-3 text-center font-bold text-blue-600">
                                        {{ $fmt($item['saham_per_orang']) }}
                                    </td>

                                @endif

                            </tr>
                            @endfor
                        @endforeach

                    </tbody>
                </table>
            </div>

            {{-- ===================== CATATAN KASUS KHUSUS ===================== --}}

            @if($isGharrawain)
            <div class="mx-6 my-4 p-4 bg-amber-50 border border-amber-200 rounded-xl flex gap-3">
                <div class="shrink-0 text-amber-500 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-amber-700 mb-1">Kasus Gharrawain</p>
                    <p class="text-xs text-amber-600 leading-relaxed">
                        Setelah suami/istri mendapat bagiannya, sisa harta dibagi antara ibu dan bapak —
                        ibu mendapat <strong>1/3 dari sisa</strong>, dan bapak mendapat sisanya sebagai <strong>ashobah</strong>.
                        Kasus ini berlaku ketika pewaris hanya meninggalkan suami/istri, ibu, dan bapak tanpa anak maupun saudara.
                    </p>
                </div>
            </div>
            @endif

            @if($isAkdariyah)
            <div class="mx-6 my-4 p-4 bg-purple-50 border border-purple-200 rounded-xl flex gap-3">
                <div class="shrink-0 text-purple-500 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-purple-700 mb-1">Kasus Akdariyah</p>
                    <p class="text-xs text-purple-600 leading-relaxed">
                        Bagian kakek dan saudari perempuan digabungkan terlebih dahulu setelah 'aul,
                        kemudian dibagi dengan rasio <strong>2:1</strong> —
                        kakek mendapat <strong>2/3</strong> dan saudari mendapat <strong>1/3</strong> dari bagian gabungan tersebut.
                    </p>
                </div>
            </div>
            @endif

            @if($isMuqosamah)
            <div class="mx-6 my-4 p-4 bg-blue-50 border border-blue-200 rounded-xl flex gap-3">
                <div class="shrink-0 text-blue-500 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-blue-700 mb-1">Kasus Muqosamah</p>
                    <p class="text-xs text-blue-600 leading-relaxed">
                        Kakek mendapat bagian terbesar dari tiga pilihan: <strong>muqosamah</strong> (berbagi kepala dengan saudara),
                        <strong>1/3 harta</strong>, atau <strong>1/6 harta</strong>.
                        Pilihan yang paling menguntungkan kakek yang diterapkan.
                    </p>
                </div>
            </div>
            @endif

        </div>
        
        {{-- PELAJARI AHLI WARIS --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm">
                    Pelajari Ahli Waris
                </h2>
            </div>

            <div class="p-6">

                @php
                    $sudahDitampilkan = [];
                @endphp

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">

                    @foreach($hasil as $item)

                        @if($item['hubungan'] === 'catatan')
                            @continue
                        @endif

                        @if(in_array($item['hubungan'], $sudahDitampilkan))
                            @continue
                        @endif

                        @php
                            $sudahDitampilkan[] = $item['hubungan'];

                            $edukasiData = \App\Models\EdukasiAhliWaris::cariByHubungan($item['hubungan']);
                        @endphp

                        @if($edukasiData)

                        <div class="border border-gray-100 rounded-xl p-4 hover:border-blue-200 hover:bg-blue-50/30 transition">

                            <h3 class="font-black text-gray-800 capitalize mb-1">
                                {{ $item['hubungan'] }}
                            </h3>

                            <p class="text-xs text-gray-500 mb-4">
                                Pelajari dasar hukum, bagian waris, serta kondisi hijab ahli waris ini.
                            </p>

                            <a href="{{ route('materi.ahli-waris', $edukasiData->slug) }}"
                            class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700">

                                Lihat Materi

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7" />

                                </svg>

                            </a>

                        </div>

                        @endif

                    @endforeach

                </div>

            </div>

        </div>

        {{-- TOMBOL NAVIGASI --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pb-6">
            <a href="{{ route('kalkulator.index') }}"
               class="flex items-center gap-2 text-gray-400 hover:text-gray-700 font-bold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Hitung Ulang
            </a>
            <div class="flex flex-wrap gap-3 justify-end">
                {{-- Simpan tanpa Islah --}}
                @auth
                    <button type="button" 
                            onclick="prosesSimpan(this)"
                            class="px-5 py-2.5 bg-emerald-500 text-white font-black rounded-xl text-sm hover:bg-emerald-600 transition-all shadow-sm shadow-emerald-200 active:scale-95">
                        <span class="btn-text">Simpan Riwayat</span>
                    </button>
                @endauth

                {{-- Lanjut ke Islah --}}
                <form action="{{ route('kalkulator.fuzzy') }}" method="GET">
                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white font-black rounded-xl text-sm hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95">
                        Lanjut ke Islah Ekonomi →
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function prosesSimpan(button) {
    const textSpan = button.querySelector('.btn-text');
    const originalText = textSpan.innerText;

    // Beri efek loading sederhana
    button.disabled = true;
    textSpan.innerText = 'Menyimpan...';

    fetch("{{ route('riwayat.simpan') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "X-Requested-With": "XMLHttpRequest",
            "Accept": "application/json"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Memunculkan alert browser sesuai keinginanmu
            alert(data.message); 
        } else {
            alert("Gagal: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Terjadi kesalahan koneksi atau server.");
    })
    .finally(() => {
        // Kembalikan tombol ke kondisi semula
        button.disabled = false;
        textSpan.innerText = originalText;
    });
}
</script>

@endsection