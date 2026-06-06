@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Islah')

@section('content')
<div class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold text-purple-500 uppercase tracking-widest mb-1">Rekomendasi Islah</p>
                    <h1 class="text-2xl font-black text-gray-900">Hasil Akhir Pembagian</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500">
                        <span>Pewaris: <strong class="text-gray-800">{{ $namaMayit }}</strong></span>
                        <span class="text-gray-300">|</span>
                        <span>Harta Bersih: <strong class="text-emerald-600">Rp {{ number_format($hartaBersih, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('kalkulator.fuzzy') }}"
                       class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all">
                        ← Ubah Data
                    </a>
                </div>
            </div>
        </div>

        {{-- TABEL HASIL UTAMA --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="w-1 h-5 bg-purple-500 rounded-full"></span>
                <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm">Komparasi Pembagian Per Ahli Waris</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-black text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-3 text-left">Ahli Waris</th>
                            <th class="px-6 py-3 text-center">Bobot Kebutuhan</th>
                            <th class="px-6 py-3 text-right">Faraidh Murni</th>
                            <th class="px-6 py-3 text-right">Hasil Islah</th>
                            <th class="px-6 py-3 text-right">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        {{-- KUNCI PERUBAHAN: Gunakan $islah_detail, bukan $islah --}}
                        @php 
                            $counterNama = []; 
                        @endphp

                        @foreach($islah_detail as $item)
                        @php
                            // Ambil label hubungan yang rapi
                            $hubunganKey = str_replace(' ', '_', strtolower($item['hubungan']));
                            $namaHubungan = $ahliWarisList[$hubunganKey] ?? ucwords($item['hubungan']);
                            
                            // Logika penomoran (Anak Laki-laki 1, Anak Laki-laki 2, dst)
                            $counterNama[$namaHubungan] = ($counterNama[$namaHubungan] ?? 0) + 1;
                            $namaTampilan = $namaHubungan . ' ' . $counterNama[$namaHubungan];

                            $selisih = $item['islah'] - $item['faraidh'];
                            $pctSelisih = $item['faraidh'] > 0 ? ($selisih / $item['faraidh']) * 100 : 0;
                            $bobot = $item['skor_fuzzy'] ?? 0;
                        @endphp
                        <tr class="hover:bg-purple-50/20 transition-colors">
                            {{-- Nama Ahli Waris Individu --}}
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-900">{{ $namaTampilan }}</span>
                            </td>

                            {{-- Bobot Kebutuhan --}}
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-lg text-xs font-black tabular-nums
                                    @if($bobot >= 0.75) bg-emerald-100 text-emerald-700
                                    @elseif($bobot >= 0.5) bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-500 @endif">
                                    {{ number_format($bobot, 2) }}
                                </span>
                            </td>

                            {{-- Faraidh Murni (Per Orang) --}}
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-gray-700 tabular-nums">
                                    Rp {{ number_format($item['faraidh'], 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Hasil Islah (Per Orang) --}}
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-purple-700 tabular-nums">
                                    Rp {{ number_format($item['islah'], 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Selisih --}}
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
                            <td colspan="2" class="px-6 py-3 text-xs font-black text-gray-400 uppercase">Total</td>
                            <td class="px-6 py-3 text-right font-black text-gray-700 tabular-nums text-sm">
                                Rp {{ number_format($hartaBersih, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right font-black text-purple-700 tabular-nums text-sm">
                                Rp {{ number_format($total_islah, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- PENJELASAN BOBOT PER AHLI WARIS --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm">Mengapa Bobot Ini?</h2>
                <span class="text-xs text-gray-400 font-medium">— Penjelasan per ahli waris</span>
            </div>

            <div class="divide-y divide-gray-50">
                @foreach($islah_detail as $item)
                @php
                    $bobot     = $item['skor_fuzzy'] ?? 0;
                    $pHasil    = $item['penghasilan'] ?? 0;
                    $usia      = $item['usia'] ?? 0;
                    $aset      = $item['aset'] ?? 0;

                    // Label linguistik penghasilan
                    if ($pHasil < 3000000)       $labelP = ['Rendah', 'text-rose-600',    'bg-rose-50'];
                    elseif ($pHasil <= 6000000)  $labelP = ['Sedang', 'text-amber-600',   'bg-amber-50'];
                    else                         $labelP = ['Tinggi', 'text-emerald-600', 'bg-emerald-50'];

                    // Label linguistik usia
                    if ($usia < 25)              $labelU = ['Muda',   'text-blue-600',   'bg-blue-50'];
                    elseif ($usia <= 55)         $labelU = ['Dewasa', 'text-indigo-600', 'bg-indigo-50'];
                    else                         $labelU = ['Tua',    'text-purple-600', 'bg-purple-50'];

                    // Label linguistik aset
                    if ($aset < 250000000)       $labelA = ['Sedikit', 'text-rose-600',    'bg-rose-50'];
                    elseif ($aset <= 1200000000) $labelA = ['Sedang',  'text-amber-600',   'bg-amber-50'];
                    else                         $labelA = ['Banyak',  'text-emerald-600', 'bg-emerald-50'];

                    // Penjelasan naratif
                    $naratif = "Berdasarkan profil ekonomi, ";
                    if ($bobot >= 0.8) {
                        $naratif .= "ahli waris ini dinilai memiliki kebutuhan yang sangat tinggi terhadap harta warisan.";
                    } elseif ($bobot >= 0.6) {
                        $naratif .= "ahli waris ini dinilai memiliki kebutuhan yang cukup besar terhadap harta warisan.";
                    } elseif ($bobot >= 0.4) {
                        $naratif .= "ahli waris ini dinilai berada dalam kondisi ekonomi yang cukup memadai.";
                    } else {
                        $naratif .= "ahli waris ini dinilai sudah dalam kondisi ekonomi yang baik.";
                    }
                @endphp
                <div class="px-6 py-5">
                    <div class="flex flex-col md:flex-row md:items-start gap-4">

                        {{-- Nama & Bobot --}}
                        <div class="md:w-48 shrink-0">
                            <p class="font-black text-gray-900 mb-1">{{ ucwords($item['hubungan']) }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400">Bobot:</span>
                                <span class="text-sm font-black
                                    @if($bobot >= 0.75) text-emerald-600
                                    @elseif($bobot >= 0.5) text-blue-600
                                    @else text-gray-500 @endif">
                                    {{ number_format($bobot, 3) }}
                                </span>
                            </div>
                        </div>

                        {{-- Tag Variabel --}}
                        <div class="flex flex-wrap gap-2 md:flex-1">
                            {{-- Penghasilan --}}
                            <div class="flex items-center gap-1.5 px-3 py-1.5 {{ $labelP[2] }} rounded-xl">
                                <span class="text-xs font-bold text-gray-500">Penghasilan:</span>
                                <span class="text-xs font-black {{ $labelP[1] }}">{{ $labelP[0] }}</span>
                                <span class="text-xs text-gray-400">(Rp {{ number_format($pHasil, 0, ',', '.') }})</span>
                            </div>

                            {{-- Usia --}}
                            <div class="flex items-center gap-1.5 px-3 py-1.5 {{ $labelU[2] }} rounded-xl">
                                <span class="text-xs font-bold text-gray-500">Usia:</span>
                                <span class="text-xs font-black {{ $labelU[1] }}">{{ $labelU[0] }}</span>
                                <span class="text-xs text-gray-400">({{ $usia }} thn)</span>
                            </div>

                            {{-- Aset --}}
                            <div class="flex items-center gap-1.5 px-3 py-1.5 {{ $labelA[2] }} rounded-xl">
                                <span class="text-xs font-bold text-gray-500">Aset:</span>
                                <span class="text-xs font-black {{ $labelA[1] }}">{{ $labelA[0] }}</span>
                                <span class="text-xs text-gray-400">(Rp {{ number_format($aset, 0, ',', '.') }})</span>
                            </div>

                            {{-- Naratif --}}
                            <p class="w-full text-xs text-gray-500 mt-1 leading-relaxed">
                                {{ $naratif }}
                            </p>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- PANDUAN MUSYAWARAH + DISCLAIMER --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Panduan --}}
            <div class="bg-gray-900 rounded-2xl p-6 text-white">
                <h3 class="font-black text-sm uppercase tracking-widest mb-4 text-gray-400">Panduan Musyawarah</h3>
                <div class="space-y-3">
                    <div class="flex gap-3 p-3 bg-gray-800 rounded-xl">
                        <span class="text-emerald-400 font-black text-lg leading-none">1</span>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Faraidh adalah <strong class="text-white">kewajiban syariat</strong> yang harus diselesaikan terlebih dahulu sebelum musyawarah.
                        </p>
                    </div>
                    <div class="flex gap-3 p-3 bg-gray-800 rounded-xl">
                        <span class="text-emerald-400 font-black text-lg leading-none">2</span>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Islah hanya bisa dilakukan jika <strong class="text-white">semua ahli waris setuju</strong> secara sukarela tanpa paksaan.
                        </p>
                    </div>
                    <div class="flex gap-3 p-3 bg-gray-800 rounded-xl">
                        <span class="text-emerald-400 font-black text-lg leading-none">3</span>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Ahli waris dengan bobot <strong class="text-white">lebih tinggi</strong> adalah yang paling membutuhkan dukungan ekonomi saat ini.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Disclaimer --}}
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6">
                <div class="flex gap-3 mb-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h3 class="font-black text-amber-900 text-sm uppercase tracking-widest">Pernyataan Penting</h3>
                </div>
                <p class="text-xs text-amber-800 leading-relaxed">
                    Hasil ini adalah <strong>rekomendasi teknis</strong> berbasis logika Fuzzy Mamdani dan bukan keputusan syar'i. Keputusan final tetap mengacu pada hukum Faraidh dan kesepakatan seluruh ahli waris.
                </p>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pb-6">
            <a href="{{ route('kalkulator.index') }}"
               class="px-6 py-3 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Hitung Ulang
            </a>

            @auth
            <form action="{{ route('riwayat.simpan') }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-6 py-3 bg-purple-600 text-white rounded-xl font-bold text-sm hover:bg-purple-700 transition-all shadow-sm shadow-purple-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan ke Riwayat
                </button>
            </form>
            @endauth
        </div>

    </div>
</div>
@endsection