@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Hasil Perhitungan Faraidh</h2>
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <p class="text-gray-600">Pewaris: <span class="font-semibold">{{ $namaMayit }}</span></p>
                            <p class="text-gray-600">Harta Bersih: <span class="font-semibold">Rp {{ number_format($hartaBersih, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Sesuai Syariat Islam
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-5 bg-white rounded-xl shadow-sm border border-gray-100 p-8 overflow-x-auto">
                    <h3 class="text-center text-gray-400 text-sm font-semibold mb-5 uppercase tracking-widest">Visualisasi Hubungan Ahli Waris</h3>
                    
                    <div class="tree">
                        <ul>
                            @php
                                $hasilData = collect($hasil);
                                $ahliWarisAktif = $hasilData->filter(fn($item) => $item['nominal'] > 0);
                                $levelAtas = $ahliWarisAktif->filter(fn($item) => in_array($item['hubungan'], ['Bapak', 'Ibu', 'Kakek', 'Nenek (Pihak Bapak)', 'Nenek (Pihak Ibu)']));
                                $levelSejajar = $ahliWarisAktif->filter(fn($item) => in_array($item['hubungan'], ['Suami', 'Istri', 'Saudara Laki-laki Sekandung', 'Saudara Perempuan Sekandung', 'Saudara Laki-laki Sebapak', 'Saudara Perempuan Sebapak', 'Saudara Seibu']));
                                $levelBawah = $ahliWarisAktif->filter(fn($item) => str_contains($item['hubungan'], 'Anak') || str_contains($item['hubungan'], 'Cucu'));
                                
                                // Helper untuk gender almarhum
                                $isIstri = $levelSejajar->where('hubungan', 'Istri')->isNotEmpty();
                                $iconAlmarhum = $isIstri ? 'icon_pria.png' : 'icon_wanita.png';
                            @endphp

                            {{-- LEVEL ATAS --}}
                            @if($levelAtas->isNotEmpty())
                            <li>
                                <div class="flex gap-10 justify-center mb-5">
                                    @foreach($levelAtas as $at)
                                        <div class="node-container">
                                            <img src="{{ asset('images/' . (in_array($at['hubungan'], ['Ibu', 'Nenek (Pihak Bapak)', 'Nenek (Pihak Ibu)']) ? 'icon_wanita.png' : 'icon_pria.png')) }}" 
                                                class="w-12 h-12 mx-auto mb-1 transition-transform hover:scale-110 cursor-help" alt="icon">
                                            
                                            {{-- Label Teks Langsung --}}
                                            <div class="text-[10px] font-bold text-gray-700 uppercase tracking-tighter">
                                                {{ $at['hubungan'] }}
                                            </div>

                                            {{-- Tooltip Detail --}}
                                            <div class="custom-tooltip">
                                                <strong>{{ $at['hubungan'] }}</strong><br>
                                                Bagian: {{ $at['bagian'] }}<br>
                                                Rp {{ number_format($at['nominal'], 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- LEVEL TENGAH (Mayit & Pasangan) --}}
                            <ul>
                                <li>
                                    <div class="flex gap-12 items-start justify-center"> {{-- Menggunakan items-start agar sejajar atas --}}
                                        {{-- Box Almarhum --}}
                                        <div class="node-container opacity-60 grayscale">
                                            <img src="{{ asset('images/'.$iconAlmarhum) }}" class="w-14 h-14 mx-auto mb-1 border-2 border-slate-400 rounded-full p-1" alt="Almarhum">
                                            <div class="font-bold text-[11px] text-slate-800 leading-none">{{ $namaMayit }}</div>
                                            <div class="text-[8px] uppercase font-bold text-red-600">(Almarhum)</div>
                                        </div>

                                        {{-- Pasangan/Saudara --}}
                                        @foreach($levelSejajar as $sj)
                                            @for($i = 0; $i < $sj['jumlah']; $i++)
                                            <div class="node-container">
                                                <img src="{{ asset('images/' . (in_array($sj['hubungan'], ['Istri', 'Saudara Perempuan Sekandung', 'Saudara Perempuan Sebapak']) ? 'icon_wanita.png' : 'icon_pria.png')) }}" 
                                                    class="w-12 h-12 mx-auto mb-1 hover:scale-110 transition-transform cursor-help" alt="icon">
                                                
                                                {{-- Label Teks Langsung --}}
                                                <div class="text-[10px] font-bold text-gray-700 leading-tight text-center">
                                                    {{ $sj['hubungan'] }} {{ $sj['jumlah'] > 1 ? ($i+1) : '' }}
                                                </div>

                                                {{-- Tooltip Detail --}}
                                                <div class="custom-tooltip">
                                                    <strong>{{ $sj['hubungan'] }}</strong><br>
                                                    Bagian: {{ $sj['bagian'] }}<br>
                                                    Rp {{ number_format($sj['nominal'] / $sj['jumlah'], 0, ',', '.') }}
                                                </div>
                                            </div>
                                            @endfor
                                        @endforeach
                                    </div>

                                        {{-- LEVEL BAWAH (Anak/Cucu) --}}
                                        @if($levelBawah->isNotEmpty())
                                        <ul>
                                            <li class="pt-8">
                                                <div class="flex flex-wrap justify-center gap-10"> {{-- Gap besar agar teks tidak tabrakan --}}
                                                    @foreach($levelBawah as $bw)
                                                        <div class="flex flex-col items-center">
                                                            {{-- Container Icon Berjejer --}}
                                                            <div class="flex flex-wrap justify-center gap-4 mb-3"> 
                                                                @for ($i = 0; $i < $bw['jumlah']; $i++)
                                                                <div class="node-container">
                                                                    <img src="{{ asset('images/' . (str_contains($bw['hubungan'], 'Perempuan') ? 'icon_wanita.png' : 'icon_pria.png')) }}" 
                                                                        class="w-12 h-12 rounded-full border-2 border-white shadow-md hover:scale-110 transition-all cursor-help" 
                                                                        alt="icon">
                                                                    
                                                                    {{-- Teks Nama/Hubungan di bawah masing-masing icon --}}
                                                                    <div class="mt-1 text-[10px] font-medium text-gray-700 whitespace-nowrap">
                                                                        {{ $bw['hubungan'] }} ({{ $i+1 }})
                                                                    </div>

                                                                    {{-- Tooltip Detail saat Hover --}}
                                                                    <div class="custom-tooltip">
                                                                        <strong>{{ $bw['hubungan'] }}</strong><br>
                                                                        Bagian: {{ $bw['bagian'] }}<br>
                                                                        Hak: Rp {{ number_format($bw['nominal'] / $bw['jumlah'], 0, ',', '.') }}
                                                                    </div>
                                                                </div>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </li>
                                        </ul>
                                        @endif
                                    </li>
                                </ul>

                            @if($levelAtas->isNotEmpty())
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <!-- Hasil Detail -->
                <div class="mb-8">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">Detail Pembagian</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($hasil as $item)
                        <div class="hasil-card bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-gray-800">{{ $item['hubungan'] }}</h4>
                                <span class="bagian-badge bg-bagian-{{ str_replace('/', '-', $item['bagian']) }}">
                                    {{ $item['bagian'] }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm text-gray-600">Jumlah: {{ $item['jumlah'] }} orang</p>
                                <p class="text-sm text-gray-600">Per Orang: Rp {{ number_format($item['nominal'] / $item['jumlah'], 0, ',', '.') }}</p>
                                <div class="pt-2 border-t border-gray-200">
                                    <p class="text-lg font-bold text-blue-600">
                                        Total: Rp {{ number_format($item['nominal'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Ringkasan -->
                <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-blue-800">Ringkasan Pembagian</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-blue-200">
                            <thead>
                                <tr class="bg-blue-100">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Ahli Waris</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Bagian</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Nominal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Per Orang</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-blue-100">
                                @foreach($hasil as $item)
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['hubungan'] }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item['bagian'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $item['jumlah'] }} orang</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">Rp {{ number_format($item['nominal'] / $item['jumlah'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-50 font-bold">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">TOTAL</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">-</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ collect($hasil)->sum('jumlah') }} orang</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600">Rp {{ number_format($hartaBersih, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <div>
                        <a href="{{ route('kalkulator.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Input
                        </a>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form action="{{ route('kalkulator.fuzzy') }}" method="GET">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-md hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Lanjutkan ke Islah Sosial
                            </button>
                        </form>
                        
                        <button onclick="window.print()"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Hasil
                        </button>
                    </div>
                </div>
                
                <!-- Catatan -->
                <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="font-bold text-yellow-800 mb-1">Catatan Penting</h4>
                            <p class="text-sm text-yellow-700">
                                Hasil ini adalah pembagian sesuai hukum waris Islam (faraidh) murni. 
                                Untuk rekomendasi pembagian dengan pertimbangan kondisi sosial (usia, penghasilan, aset), 
                                silakan lanjutkan ke tahap Islah Sosial.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection