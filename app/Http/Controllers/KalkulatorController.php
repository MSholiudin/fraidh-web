<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FaraidhCalculator;
use App\Services\FaraidhDetailService;
use App\Services\FuzzyMamdaniService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class KalkulatorController extends Controller
{
    private $ahliWarisList = [
        'suami' => 'Suami',
        'istri' => 'Istri',
        'bapak' => 'Bapak',
        'ibu' => 'Ibu',
        'anak_laki' => 'Anak Laki-laki',
        'anak_perempuan' => 'Anak Perempuan',
        'cucu_laki' => 'Cucu Laki-laki',
        'cucu_perempuan' => 'Cucu Perempuan',
        'kakek' => 'Kakek',
        'nenek_bapak' => 'Nenek (Pihak Bapak)',
        'nenek_ibu' => 'Nenek (Pihak Ibu)',
        'saudara_laki_kandung' => 'Saudara Laki-laki Sekandung',
        'saudara_perempuan_kandung' => 'Saudara Perempuan Sekandung',
        'saudara_laki_sebapak' => 'Saudara Laki-laki Sebapak',
        'saudara_perempuan_sebapak' => 'Saudara Perempuan Sebapak',
        'saudara_seibu' => 'Saudara Seibu',
    ];
    
    public function index()
    {
        // Kelompokkan untuk tampilan checkbox
        $kelompokAhliWaris = [
            'Pasangan' => [
                'suami' => 'Suami',
                'istri' => 'Istri'
            ],
            'Orang Tua' => [
                'bapak' => 'Bapak',
                'ibu' => 'Ibu'
            ],
            'Anak Kandung' => [
                'anak_laki' => 'Anak Laki-laki',
                'anak_perempuan' => 'Anak Perempuan'
            ],
            'Cucu' => [
                'cucu_laki' => 'Cucu Laki-laki',
                'cucu_perempuan' => 'Cucu Perempuan'
            ],
            'Kakek/Nenek' => [
                'kakek' => 'Kakek',
                'nenek_bapak' => 'Nenek (Pihak Bapak)',
                'nenek_ibu' => 'Nenek (Pihak Ibu)'
            ],
            'Saudara' => [
                'saudara_laki_kandung' => 'Saudara Laki-laki Sekandung',
                'saudara_perempuan_kandung' => 'Saudara Perempuan Sekandung',
                'saudara_laki_sebapak' => 'Saudara Laki-laki Sebapak',
                'saudara_perempuan_sebapak' => 'Saudara Perempuan Sebapak',
                'saudara_seibu' => 'Saudara Seibu'
            ]
        ];
        
        return view('kalkulator.index', [
            'ahliWarisList' => $this->ahliWarisList,
            'kelompokAhliWaris' => $kelompokAhliWaris
        ]);
    }
    
    public function hitung(Request $request)
    {
        //dd($request->all());
        $validated = $request->validate([
            'nama_mayit' => 'required|string|max:100',
            'jenis_kelamin_mayit' => 'required|in:L,P',
            'total_harta' => 'required|numeric|min:0',
            'hutang' => 'nullable|numeric|min:0',
            'wasiat' => 'nullable|numeric|min:0',
            'haji_amanat' => 'nullable|numeric|min:0',
            'ahli_waris_terpilih' => 'required|array|min:1',
            'ahli_waris_terpilih.*' => 'string|in:' . implode(',', array_keys($this->ahliWarisList)),
            'ahli_waris' => 'required|array',
        ]);
        
        // Validasi tambahan untuk input jumlah
        foreach ($validated['ahli_waris_terpilih'] as $key) {
            $request->validate([
                "ahli_waris.{$key}.jumlah" => 'required|integer|min:1'
            ]);
        }
        
        // Hitung harta bersih
        $hartaBersih = $validated['total_harta'];
        $hartaBersih -= $validated['hutang'] ?? 0;
        $hartaBersih -= $validated['wasiat'] ?? 0;
        $hartaBersih -= $validated['haji_amanat'] ?? 0;
        
        if ($hartaBersih <= 0) {
            return back()->with('error', 'Harta bersih tidak valid setelah dikurangi hutang/wasiat/haji!');
        }
        
        // Format data ahli waris untuk engine
        $daftarAhliWaris = [];
        foreach ($validated['ahli_waris_terpilih'] as $key) {
            $jumlah = $request->input("ahli_waris.{$key}.jumlah", 1);
            
            // Ubah kode menjadi format readable untuk engine
            $hubunganFormatted = $this->formatHubunganUntukEngine($key);
            
            for ($i = 0; $i < $jumlah; $i++) {
                $daftarAhliWaris[] = [
                    'hubungan' => $hubunganFormatted,
                    'penghasilan' => 0,
                    'usia' => 0,
                    'aset' => 0
                ];
            }
        }
        
        // Jalankan engine faraidh
        $hasilFaraidh = FaraidhCalculator::calculate($hartaBersih, $daftarAhliWaris);
        
        // Simpan ke session
        Session::put('faraidh_data', [
            'harta_bersih' => $hartaBersih,
            'ahli_waris' => $daftarAhliWaris,
            'nama_mayit' => $validated['nama_mayit'],
            'total_harta' => $validated['total_harta'],
            'hutang' => $validated['hutang'] ?? 0,
            'wasiat' => $validated['wasiat'] ?? 0,
            'haji_amanat' => $validated['haji_amanat'] ?? 0,
            'hasil_faraidh_raw' => $hasilFaraidh
        ]);
        
        // Format hasil untuk ditampilkan
        $hasilTampilan = [];
        $counter = 1;
        $groupedResults = [];

        // Group by hubungan dan label
        foreach ($hasilFaraidh as $item) {

            if ($item['hubungan'] === 'catatan') {
                continue;
            }
            $key = $item['hubungan'] . '|' . $item['label'];
            
            if (!isset($groupedResults[$key])) {
                $groupedResults[$key] = [
                    'count' => 0,
                    'total_nominal' => 0,
                    'hubungan' => $item['hubungan'],
                    'label' => $item['label']
                ];
            }
            
            $groupedResults[$key]['count']++;
            $groupedResults[$key]['total_nominal'] += $item['nominal'];
        }

        // Convert ke format tampilan
        foreach ($groupedResults as $group) {
            $hubunganKey = $this->formatHubunganDariEngine($group['hubungan']);
            $hubunganLabel = $this->ahliWarisList[$hubunganKey] ?? ucwords(str_replace('_', ' ', $hubunganKey));
            
            $hasilTampilan[] = [
                'id' => $counter++,
                'hubungan' => $hubunganLabel,
                'jumlah' => $group['count'],
                'bagian' => $group['label'],
                'nominal' => $group['total_nominal']
            ];
        }

        foreach ($hasilFaraidh as $item) {
            if ($item['hubungan'] === 'catatan') {
                $hasilTampilan[] = [
                    'id'       => $counter++,
                    'hubungan' => 'catatan',
                    'jumlah'   => 1,
                    'bagian'   => $item['label'],
                    'nominal'  => 0,
                    'catatan'  => $item['catatan'],
                ];
            }
        }
        
        Session::put('hasil_faraidh', $hasilTampilan);
        
        // Simpan data kelompok untuk kebutuhan fuzzy nanti agar tidak rancu
        $kelompokFuzzy = [];
        foreach ($hasilTampilan as $item) {
            if ($item['hubungan'] === 'catatan') continue;
            $kelompokFuzzy[$item['hubungan']] = [
                'jumlah' => $item['jumlah'],
                'bagian' => $item['bagian']
            ];
        }
        Session::put('kelompok_fuzzy', $kelompokFuzzy);

        $detailService = new FaraidhDetailService();
        $detailPerhitungan = $detailService->generate($hasilTampilan);

        return view('kalkulator.hasil-faraidh', [
            'hasil' => $hasilTampilan,
            'detailPerhitungan' => $detailPerhitungan,
            'hartaBersih' => $hartaBersih,
            'namaMayit' => $validated['nama_mayit']
        ]);
    }
    
    /**
     * Format hubungan untuk engine (dari kode ke format readable)
     */
    private function formatHubunganUntukEngine($key)
    {
        $mapping = [
            'suami' => 'suami',
            'istri' => 'istri',
            'bapak' => 'bapak',
            'ibu' => 'ibu',
            'anak_laki' => 'anak laki-laki',
            'anak_perempuan' => 'anak perempuan',
            'cucu_laki' => 'cucu laki-laki',
            'cucu_perempuan' => 'cucu perempuan',
            'kakek' => 'kakek',
            'nenek_bapak' => 'nenek pihak bapak',
            'nenek_ibu' => 'nenek pihak ibu',
            'saudara_laki_kandung' => 'saudara laki-laki sekandung',
            'saudara_perempuan_kandung' => 'saudara perempuan sekandung',
            'saudara_laki_sebapak' => 'saudara laki-laki sebapak',
            'saudara_perempuan_sebapak' => 'saudara perempuan sebapak',
            'saudara_seibu' => 'saudara seibu'
        ];
        
        return $mapping[$key] ?? $key;
    }
    
    /**
     * Format hubungan dari engine (dari readable ke kode)
     */
    private function formatHubunganDariEngine($hubungan)
    {
        $mapping = [
            'suami' => 'suami',
            'istri' => 'istri',
            'bapak' => 'bapak',
            'ibu' => 'ibu',
            'anak laki-laki' => 'anak_laki',
            'anak perempuan' => 'anak_perempuan',
            'cucu laki-laki' => 'cucu_laki',
            'cucu perempuan' => 'cucu_perempuan',
            'kakek' => 'kakek',
            'nenek pihak bapak' => 'nenek_bapak',
            'nenek pihak ibu' => 'nenek_ibu',
            'saudara laki-laki sekandung' => 'saudara_laki_kandung',
            'saudara perempuan sekandung' => 'saudara_perempuan_kandung',
            'saudara laki-laki sebapak' => 'saudara_laki_sebapak',
            'saudara perempuan sebapak' => 'saudara_perempuan_sebapak',
            'saudara seibu' => 'saudara_seibu'
        ];
        
        return $mapping[strtolower($hubungan)] ?? str_replace(' ', '_', strtolower($hubungan));
    }
    
    public function fuzzy()
    {
        if (!session('hasil_faraidh')) {
            return redirect()->route('kalkulator.index');
        }
        
        $colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899'];
        
        // Ambil data faraidh dari session
        $ahliWarisKelompok = session('hasil_faraidh', []);

        $ahliWarisKelompok = array_filter($ahliWarisKelompok, function($item) {
            return $item['hubungan'] !== 'catatan'
                && !Str::contains(strtolower($item['hubungan']), ['baitul maal', 'sabilillah']);
        });
        
        $ahliWarisKelompok = array_values($ahliWarisKelompok);
        
        return view('kalkulator.fuzzy', [
            'ahliWaris' => $ahliWarisKelompok,
            'ahliWarisList' => $this->ahliWarisList,
            'colors' => $colors
        ]);
    }
    
    public function hitungFuzzy(Request $request)
    {
        $validated = $request->validate([
            'data_sosial' => 'required|array',
            'data_sosial.*.usia' => 'required|integer|min:0|max:120',
            'data_sosial.*.penghasilan' => 'required|numeric|min:0',
            'data_sosial.*.aset' => 'required|numeric|min:0|max:3000000000',
        ]);
        
        $hasilFaraidh = Session::get('hasil_faraidh');
        $faraidhData = Session::get('faraidh_data');
        
        if (!$hasilFaraidh || !$faraidhData) {
            return redirect()->route('kalkulator.index')->with('error', 'Data Faraidh tidak ditemukan.');
        }

        // 1. Siapkan data untuk fuzzy engine
        $ahliWarisData = [];
        $index = 0;
        
        foreach ($hasilFaraidh as $item) {
            $jumlah = $item['jumlah'];

            for ($i = 0; $i < $jumlah; $i++) {
                $dataSosial = $validated['data_sosial'][$index] ?? [
                    'usia'        => 40,
                    'penghasilan' => 3000000,
                    'aset'        => 0,
                ];

                $hubunganRaw = $this->formatHubunganUntukEngine(
                    $this->formatHubunganDariEngine($item['hubungan'])
                );

                $ahliWarisData[] = [
                    'hubungan'    => $hubunganRaw,
                    'faraidh'     => $item['nominal'] / $jumlah, // per orang
                    'label'       => $item['bagian'] ?? '',
                    'penghasilan' => $dataSosial['penghasilan'],
                    'usia'        => $dataSosial['usia'],
                    'aset'        => $dataSosial['aset'],
                ];

                $index++;
            }
        }
        
        // 2. Hitung distribusi islah menggunakan Fuzzy Mamdani
        $hasilIslah = FuzzyMamdaniService::calculate_islah(
            $faraidhData['harta_bersih'],
            $ahliWarisData
        );
        
        // 3. Group hasil untuk tampilan (Menangani kasus ahli waris berjumlah > 1)
        $hasilIslahGrouped = [];
        $counter = 1;
        
        foreach ($hasilIslah['hasil_islah'] as $item) {
            $hubunganKey = $this->formatHubunganDariEngine($item['hubungan']);
            $hubunganLabel = $this->ahliWarisList[$hubunganKey] ?? ucwords(str_replace('_', ' ', $hubunganKey));
            
            $found = false;
            foreach ($hasilIslahGrouped as &$group) {
                if ($group['hubungan'] === $hubunganLabel) {
                    $group['jumlah'] += 1;
                    $group['faraidh'] += $item['faraidh'];
                    $group['total_islah'] += $item['islah'];
                    $group['akumulasi_skor_fuzzy'] += $item['skor_fuzzy'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $hasilIslahGrouped[] = [
                    'id' => $counter++,
                    'hubungan' => $hubunganLabel,
                    'jumlah' => 1,
                    'faraidh' => $item['faraidh'],
                    'total_islah' => $item['islah'],
                    'akumulasi_skor_fuzzy' => $item['skor_fuzzy'],
                    'bobot' => 0, // Akan diisi di langkah finalisasi
                ];
            }
        }

        // 4. Finalisasi: Hitung rata-rata bobot per kelompok
        foreach ($hasilIslahGrouped as &$group) {
            // Bobot tampilan diambil dari rata-rata skor fuzzy (0 s/d 1)
            $group['bobot'] = $group['akumulasi_skor_fuzzy'] / $group['jumlah'];
            // Opsional: Jika ingin tetap menyimpan skor_fuzzy untuk kompatibilitas view lama
            $group['skor_fuzzy'] = $group['bobot'] * 100; 
        }
        unset($group);
        
        // 5. Simpan ke Session
        Session::put('hasil_islah_detail', $hasilIslah['hasil_islah']);
        Session::put('hasil_islah_grouped', $hasilIslahGrouped);
        Session::put('total_islah', $hasilIslah['total_islah']);
        
        // 6. Return ke View
        return view('kalkulator.hasil-akhir', [
            'faraidh' => $hasilFaraidh,
            'islah' => $hasilIslahGrouped,
            'islah_detail' => $hasilIslah['hasil_islah'],
            'hartaBersih' => $faraidhData['harta_bersih'],
            'namaMayit' => $faraidhData['nama_mayit'],
            'total_faraidh' => array_sum(array_column($hasilFaraidh, 'nominal')),
            'total_islah' => $hasilIslah['total_islah']
        ]);
    }
}