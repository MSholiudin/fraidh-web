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
                <div class="flex gap-3">
                    <form action="{{ route('kalkulator.fuzzy') }}" method="GET">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-all shadow-sm shadow-blue-200">
                            Islah Ekonomi →
                        </button>
                    </form>
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
                $adaTashih =
                    !empty($detailPerhitungan['tashih']) &&
                    $detailPerhitungan['tashih'] != $detailPerhitungan['asal_masalah'];
            @endphp

            {{-- Header Angka --}}
            <div class="p-6 border-b bg-gray-50">
                <div class="grid grid-cols-2 gap-4 max-w-md">

                    <div>
                        <p class="text-sm text-gray-500">Asal Masalah</p>
                        <p class="text-4xl font-bold text-blue-600">
                            {{ $detailPerhitungan['asal_masalah'] }}
                        </p>
                    </div>

                    @if($adaTashih)
                        <div>
                            <p class="text-sm text-gray-500">Tashih</p>
                            <p class="text-4xl font-bold text-emerald-600">
                                {{ $detailPerhitungan['tashih'] }}
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>

                            <th class="px-4 py-3 text-left">
                                Ahli Waris
                            </th>

                            <th class="px-4 py-3 text-center">
                                Bagian
                            </th>

                            @if($adaTashih)

                                <th class="px-4 py-3 text-center">
                                    Saham Awal
                                </th>

                                <th class="px-4 py-3 text-center">
                                    Saham Setelah Tashih
                                </th>

                            @else

                                <th class="px-4 py-3 text-center">
                                    Saham
                                </th>

                            @endif

                            <th class="px-4 py-3 text-center">
                                Saham / Orang
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @foreach($detailPerhitungan['items'] as $item)

                            @for($i = 1; $i <= $item['jumlah']; $i++)

                                <tr>

                                    {{-- Nama ahli waris --}}
                                    <td class="px-4 py-3 font-semibold">

                                        {{ $item['hubungan'] }}

                                        @if($item['jumlah'] > 1)
                                            {{ $i }}
                                        @endif

                                    </td>

                                    @if($i == 1)

                                        {{-- Bagian --}}
                                        <td rowspan="{{ $item['jumlah'] }}"
                                            class="px-4 py-3 text-center align-middle">

                                            {{ $item['bagian'] }}

                                        </td>

                                        @if($adaTashih)

                                            {{-- Saham Awal --}}
                                            <td rowspan="{{ $item['jumlah'] }}"
                                                class="px-4 py-3 text-center align-middle">

                                                {{ $item['saham_awal'] ?? '-' }}

                                            </td>

                                            {{-- Saham Setelah Tashih --}}
                                            <td rowspan="{{ $item['jumlah'] }}"
                                                class="px-4 py-3 text-center align-middle">

                                                {{ $item['saham_akhir'] ?? '-' }}

                                            </td>

                                        @else

                                            {{-- Tidak ada tashih --}}
                                            <td rowspan="{{ $item['jumlah'] }}"
                                                class="px-4 py-3 text-center align-middle">

                                                {{ $item['saham_awal'] ?? '-' }}

                                            </td>

                                        @endif

                                    @endif

                                    {{-- Per orang --}}
                                    <td class="px-4 py-3 text-center font-bold text-blue-600">

                                        {{ $item['saham_per_orang'] ?? '-' }}

                                    </td>

                                </tr>

                            @endfor

                        @endforeach

                    </tbody>

                </table>
            </div>
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