@extends('layouts.app')

@section('title', 'Kalkulator Waris')

@section('content')
<div class="py-10 bg-gray-50 min-h-screen" x-data="{
    jenisKelamin: 'L',
    formatCurrency(el) {
        const raw = el.value.replace(/\D/g, '');
        el.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    },
    clearDots() {
        document.querySelectorAll('.input-currency').forEach(input => {
            input.value = input.value.replace(/\./g, '');
        });
    }
}">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Sistem Pakar Faraidh</p>
            <h1 class="text-2xl font-black text-gray-900">Kalkulator Pembagian Waris</h1>
            <p class="text-sm text-gray-500 mt-1">Hitung pembagian waris sesuai syariat Islam secara otomatis</p>
        </div>

        <form action="{{ route('kalkulator.hitung') }}" method="POST" @submit="clearDots()">
            @csrf
            {{-- BAGIAN 1: DATA PEWARIS --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-black flex items-center justify-center">1</span>
                    <h2 class="font-black text-gray-800 text-sm uppercase tracking-tight">Data Pewaris</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                                Nama Pewaris <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_mayit" required
                                   placeholder="Nama almarhum/almarhumah"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                                Jenis Kelamin <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_kelamin_mayit" x-model="jenisKelamin" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition bg-white">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: DATA HARTA --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-black flex items-center justify-center">2</span>
                    <h2 class="font-black text-gray-800 text-sm uppercase tracking-tight">Data Harta Warisan</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        @foreach([
                            'total_harta'  => ['label' => 'Total Harta', 'placeholder' => '120.000.000', 'required' => true],
                            'hutang'       => ['label' => 'Hutang',       'placeholder' => '0',           'required' => false],
                            'wasiat'       => ['label' => 'Wasiat',       'placeholder' => '0',           'required' => false],
                            'haji_amanat'  => ['label' => 'Biaya Haji',   'placeholder' => '0',           'required' => false],
                        ] as $name => $field)
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                                {{ $field['label'] }} (Rp)
                                @if($field['required']) <span class="text-rose-500">*</span> @endif
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm font-bold pointer-events-none border-r border-gray-200 pr-3">
                                    Rp
                                </span>
                                <input type="text"
                                       name="{{ $name }}"
                                       @if($field['required']) required @endif
                                       @input="formatCurrency($event.target)"
                                       value="{{ $name === 'total_harta' ? '' : '0' }}"
                                       placeholder="{{ $field['placeholder'] }}"
                                       class="input-currency w-full pl-14 pr-4 py-3 border border-gray-200 rounded-xl text-sm font-bold text-gray-800 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                            </div>
                        </div>
                        @endforeach

                    </div>

                    {{-- Info pengurangan --}}
                    <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-xs text-blue-700 leading-relaxed">
                            <strong>Catatan:</strong> Harta bersih = Total Harta − Biaya Haji − Hutang − Wasiat.
                            Pastikan semua harta sudah diuangkan dan disepakati sebelum diinput.
                        </p>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 3: DATA AHLI WARIS --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-black flex items-center justify-center">3</span>
                    <h2 class="font-black text-gray-800 text-sm uppercase tracking-tight">Data Ahli Waris</h2>
                    <span class="text-xs text-gray-400 font-medium">— Pilih dan tentukan jumlah</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($kelompokAhliWaris as $kelompok => $anggota)
                        <div class="bg-gray-50 rounded-2xl border border-gray-100 p-4">

                            {{-- Header Kelompok --}}
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                                {{ $kelompok }}
                            </h3>

                            {{-- Daftar Ahli Waris --}}
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($anggota as $key => $value)
                                <div x-data="{
                                         checked: false,
                                         get isDisabled() {
                                             return ('{{ $key }}' === 'suami' && jenisKelamin === 'L')
                                                 || ('{{ $key }}' === 'istri'  && jenisKelamin === 'P')
                                         }
                                     }"
                                     x-effect="if (isDisabled) checked = false"
                                     :class="isDisabled
                                         ? 'opacity-40 cursor-not-allowed bg-gray-100 border-gray-200'
                                         : checked
                                             ? 'bg-blue-50 border-blue-400 ring-2 ring-blue-100'
                                             : 'bg-white border-gray-200 hover:border-blue-300'"
                                     class="flex items-center justify-between px-4 py-3 rounded-xl border transition-all duration-200 cursor-pointer"
                                     @click="if (!isDisabled) checked = !checked">

                                    {{-- Checkbox + Label --}}
                                    <label class="flex items-center gap-3 cursor-pointer flex-grow select-none" @click.stop>
                                        <input type="checkbox"
                                               x-model="checked"
                                               :disabled="isDisabled"
                                               name="ahli_waris_terpilih[]"
                                               value="{{ $key }}"
                                               class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                                        <span class="text-sm font-bold"
                                              :class="checked ? 'text-blue-700' : 'text-gray-700'">
                                            {{ $value }}
                                        </span>
                                    </label>

                                    {{-- Input Jumlah --}}
                                    <div x-show="checked"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 scale-90"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="flex items-center gap-2 ml-3"
                                         @click.stop>
                                        <div class="w-px h-4 bg-gray-300"></div>
                                        <input type="number"
                                               name="ahli_waris[{{ $key }}][jumlah]"
                                               min="1"
                                               :max="{{ $key === 'istri' ? 4 : ($key === 'suami' ? 1 : 999) }}"
      										   value="1"
                                               :required="checked"
                                               :disabled="!checked"
                                               class="w-12 text-center text-sm font-black text-blue-900 bg-white rounded-lg border border-blue-200 focus:border-blue-500 focus:ring-0 outline-none py-1">
                                        <span class="text-[10px] font-bold text-gray-400">org</span>
                                    </div>

                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="flex justify-end pb-6 py-8">
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 tracking-wider text-sm">
                    Hitung Pembagian Faraidh →
                </button>
            </div>

        </form>
    </div>
</div>
@endsection