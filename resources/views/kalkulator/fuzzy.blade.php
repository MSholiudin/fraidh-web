@extends('layouts.app')

@section('title', 'Kalkulator Waris (Ekonomi)')

@section('content')
<div class="py-10 bg-gray-50 min-h-screen" x-data="fuzzyForm()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ================================================== --}}
        {{-- HEADER --}}
        {{-- ================================================== --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-xs font-bold text-purple-500 uppercase tracking-widest mb-1">Fuzzy Mamdani</p>
            <h1 class="text-2xl font-black text-gray-900">Data Kondisi Ekonomi Ahli Waris</h1>
            <p class="text-sm text-gray-500 mt-1">Lengkapi data ekonomi untuk perhitungan rekomendasi Islah</p>
        </div>

        {{-- ================================================== --}}
        {{-- INFO BOX --}}
        {{-- ================================================== --}}
        <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4 flex gap-3 items-start">
			<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
			</svg>
			<div>
				<h4 class="font-semibold text-yellow-800 text-sm mb-1">Mengenal Islah Ekonomi</h4>
				<p class="text-yellow-700 text-xs leading-relaxed">
					Sistem menggunakan logika <strong>Fuzzy Mamdani</strong> untuk memberikan rekomendasi
                    pembagian berdasarkan tingkat ekonomi Ahli Waris. Variabel yang digunakan meliputi usia, penghasilan, dan aset yang dimiliki.
                    Fitur ini hanya bersifat <strong>rekomendasi</strong>, 
                    adapun pembagian final tetap berdasarkan kesepakatan bersama semua ahli waris.
				</p>
			</div>
		</div>

        {{-- ================================================== --}}
        {{-- FORM --}}
        {{-- ================================================== --}}
        <form action="{{ route('kalkulator.hitung-fuzzy') }}" method="POST" @submit="prepareSubmit()">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                    <ul class="text-xs text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                @php $index = 0; @endphp

                @foreach($ahliWaris as $item)
                @php
                    $namaHubungan = $item['hubungan'] ?? 'Ahli Waris';
                    $jumlahOrang  = $item['jumlah'] ?? 0;
                    $colorMap = [
                        ['border' => 'border-blue-400',   'bg' => 'bg-blue-50/40',   'badge' => 'bg-blue-100 text-blue-700'],
                        ['border' => 'border-emerald-400','bg' => 'bg-emerald-50/40','badge' => 'bg-emerald-100 text-emerald-700'],
                        ['border' => 'border-purple-400', 'bg' => 'bg-purple-50/40', 'badge' => 'bg-purple-100 text-purple-700'],
                        ['border' => 'border-amber-400',  'bg' => 'bg-amber-50/40',  'badge' => 'bg-amber-100 text-amber-700'],
                        ['border' => 'border-rose-400',   'bg' => 'bg-rose-50/40',   'badge' => 'bg-rose-100 text-rose-700'],
                    ];
                    $color = $colorMap[$loop->index % 5];
                @endphp

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                    {{-- Header Kelompok --}}
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3 border-l-4 {{ $color['border'] }}">
                        <span class="px-2 py-1 {{ $color['badge'] }} text-xs font-black rounded-lg">
                            {{ $loop->iteration }}
                        </span>
                        <h3 class="font-black text-gray-800 uppercase tracking-tight text-sm capitalize">
                            {{ $namaHubungan }}
                        </h3>
                        <span class="text-xs text-gray-400 font-medium">({{ $jumlahOrang }} orang)</span>
                    </div>

                    {{-- Input per orang --}}
                    <div class="p-6 space-y-8">
                        @for($i = 1; $i <= $jumlahOrang; $i++)
                        <div class="{{ $i > 1 ? 'pt-8 border-t border-dashed border-gray-200' : '' }}">

                            @if($jumlahOrang > 1)
                            <div class="mb-4">
                                <span class="bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg">
                                    Orang ke-{{ $i }}
                                </span>
                            </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                {{-- PENGHASILAN --}}
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">
                                        Penghasilan / Bulan
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold border-r border-gray-200 pr-3 pointer-events-none">Rp</span>
                                        <input type="text"
                                               x-ref="penghasilan_display_{{ $index }}"
                                               @input="formatCurrency($event.target, 'penghasilan_{{ $index }}')"
                                               required
                                               placeholder="0"
                                               class="w-full pl-14 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                                        <input type="hidden"
                                               name="data_sosial[{{ $index }}][penghasilan]"
                                               x-ref="penghasilan_{{ $index }}"
                                               value="0">
                                    </div>
                                    <div class="flex justify-between text-[9px] font-bold uppercase">
                                        <span class="text-rose-400">Rendah &lt; 3jt</span>
                                        <span class="text-yellow-500">Sedang 3 – 6 jt</span>
                                        <span class="text-emerald-400">Tinggi &gt; 6jt</span>
                                    </div>
                                </div>

                                {{-- USIA --}}
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">
                                        Usia (Tahun)
                                    </label>
                                    <input type="number"
                                           name="data_sosial[{{ $index }}][usia]"
                                           required min="0" max="120"
                                           placeholder="Contoh: 35"
                                           @keydown="if(!/[0-9]/.test($event.key) && !['Backspace','Tab','ArrowLeft','ArrowRight','Delete'].includes($event.key)) $event.preventDefault()"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                                    <div class="flex justify-between text-[9px] font-bold uppercase text-gray-400">
                                        <span>Muda &lt; 25</span>
                                        <span class="text-gray-500">Dewasa 25 – 55</span>
                                        <span>Tua &gt; 55</span>
                                    </div>
                                </div>

                                {{-- ASET --}}
                                <div class="space-y-2">
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest">
                                        Total Aset Pribadi
                                    </label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold border-r border-gray-200 pr-3 pointer-events-none">Rp</span>
                                        <input type="text"
                                               x-ref="aset_display_{{ $index }}"
                                               @input="formatCurrency($event.target, 'aset_{{ $index }}')"
                                               required
                                               placeholder="0"
                                               class="w-full pl-14 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                                        <input type="hidden"
                                               name="data_sosial[{{ $index }}][aset]"
                                               x-ref="aset_{{ $index }}"
                                               value="0">
                                    </div>
                                    <div class="flex justify-between text-[9px] font-bold uppercase">
                                        <span class="text-blue-400">Sedikit &lt; 250jt</span>
                                        <span class="text-yellow-500">Sedang 250jt – 1.5M</span>
                                        <span class="text-rose-400">Banyak &gt; 1.2M</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @php $index++; @endphp
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>

            {{-- TOMBOL --}}
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 pb-6">
                <a href="{{ route('kalkulator.index') }}"
                   class="flex items-center gap-2 text-gray-400 hover:text-gray-700 font-bold text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Hasil Faraidh
                </a>
                <div class="flex gap-3">
                    <button type="reset"
                            class="px-6 py-3 bg-white border border-gray-200 text-gray-500 font-bold rounded-xl text-sm hover:bg-gray-50 transition-all">
                        Reset
                    </button>
                    <button type="submit"
                            class="px-8 py-3 bg-purple-600 text-white font-black rounded-xl hover:bg-purple-700 shadow-lg shadow-purple-200 transition-all active:scale-95 text-sm">
                        Hitung Rekomendasi Islah →
                    </button>
                </div>
            </div>

        </form>

        {{-- ================================================== --}}
        {{-- INFO CARDS VARIABEL --}}
        {{-- ================================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pb-6">
            @foreach([
                ['title' => 'Penghasilan', 'color' => 'blue', 'image' => 'penghasilan.png',
                 'desc'  => 'Prioritas diberikan kepada ahli waris dengan penghasilan rendah. Zona overlap Rendah–Sedang di Rp 3 jt – 6 jt. Zona Tinggi mulai dari Rp 6 jt.'],
                ['title' => 'Faktor Usia', 'color' => 'emerald', 'image' => 'umur.png',
                 'desc'  => 'Usia Muda (< 20 tahun) dan Tua (> 55 tahun) mendapat bobot kebutuhan lebih tinggi. Usia Dewasa produktif antara 20 – 60 tahun (referensi BPS).'],
                ['title' => 'Aset Pribadi', 'color' => 'rose', 'image' => 'aset.png',
                 'desc'  => 'Aset Sedikit di bawah Rp 250 juta. Aset Sedang antara Rp 250 juta – 1,5 miliar. Aset Banyak di atas Rp 1,2 miliar (referensi World Bank, LPS).'],
            ] as $info)
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <div class="w-10 h-10 bg-{{ $info['color'] }}-50 rounded-xl flex items-center justify-center mb-3 overflow-hidden">
                    <img src="{{ asset('images/' . $info['image']) }}" class="w-6 h-6 object-contain">
                </div>
                <h4 class="font-black text-gray-800 mb-1 text-sm">{{ $info['title'] }}</h4>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $info['desc'] }}</p>
            </div>
            @endforeach
        </div>

    </div>
</div>

<script>
function fuzzyForm() {
    return {
        formatCurrency(displayInput, refName) {
            const raw = displayInput.value.replace(/\D/g, '');
            displayInput.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            if (this.$refs[refName] !== undefined) {
                this.$refs[refName].value = raw || '0';
            }
        },
        prepareSubmit() {
            document.querySelectorAll('input[type="hidden"]').forEach(hidden => {
                if (!hidden.value) hidden.value = '0';
            });
        }
    }
}
</script>
@endsection