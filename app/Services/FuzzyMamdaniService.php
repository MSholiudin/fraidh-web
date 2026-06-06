<?php

namespace App\Services;

class FuzzyMamdaniService
{
    // =========================================================
    // KONSTANTA FUNGSI KEANGGOTAAN
    // =========================================================

    // Penghasilan (Rupiah) - Referensi: UMK Jember 2025 (Rp 3.012.197)
    private const PENGHASILAN = [
        'rendah' => [0, 0, 2_500_000, 3_000_000],
        'sedang' => [2_500_000, 4_500_000, 6_000_000],
        'tinggi' => [5_500_000, 6_000_000, 15_000_000, 15_000_000],
    ];

    // Usia (Tahun) - Referensi: BPS usia produktif 15-64 tahun
    private const USIA = [
        'muda'   => [0, 0, 17, 25],
        'dewasa' => [24, 40, 55],
        'tua'    => [50, 60, 100, 100],
    ];

    // Aset (Rupiah) - Referensi: Kepmenpupr, LPS, World Bank
    private const ASET = [
        'sedikit' => [0, 0, 200_000_000, 250_000_000],
        'sedang'  => [200_000_000, 850_000_000, 1_500_000_000],
        'banyak'  => [1_200_000_000, 1_500_000_000, 3_000_000_000, 3_000_000_000],
    ];

    // Output bobot kebutuhan (0-1)
    private const OUTPUT = [
        'sangat_kecil' => [0.0, 0.1, 0.3],
        'kecil'        => [0.2, 0.3, 0.5],
        'menengah'     => [0.4, 0.5, 0.7],
        'besar'        => [0.6, 0.8, 0.9],
        'sangat_besar' => [0.8, 1.0, 1.0, 1.0],
    ];

    // =========================================================
    // 27 RULE FUZZY MAMDANI
    // Format: [penghasilan, usia, aset, output]
    // Logika: semakin rendah penghasilan, semakin tidak produktif usia,
    // dan semakin sedikit aset → bobot kebutuhan makin besar
    // =========================================================

    private const RULES = [
        // RF01-RF09: Penghasilan Rendah
        ['rendah', 'muda',   'sedikit', 'besar'],
        ['rendah', 'muda',   'sedang',  'menengah'],
        ['rendah', 'muda',   'banyak',  'kecil'],
        ['rendah', 'dewasa', 'sedikit', 'sangat_besar'],
        ['rendah', 'dewasa', 'sedang',  'besar'],
        ['rendah', 'dewasa', 'banyak',  'menengah'],
        ['rendah', 'tua',    'sedikit', 'sangat_besar'],
        ['rendah', 'tua',    'sedang',  'sangat_besar'],
        ['rendah', 'tua',    'banyak',  'besar'],

        // RF10-RF18: Penghasilan Sedang
        ['sedang', 'muda',   'sedikit', 'menengah'],
        ['sedang', 'muda',   'sedang',  'kecil'],
        ['sedang', 'muda',   'banyak',  'sangat_kecil'],
        ['sedang', 'dewasa', 'sedikit', 'besar'],
        ['sedang', 'dewasa', 'sedang',  'menengah'],
        ['sedang', 'dewasa', 'banyak',  'kecil'],
        ['sedang', 'tua',    'sedikit', 'sangat_besar'],
        ['sedang', 'tua',    'sedang',  'besar'],
        ['sedang', 'tua',    'banyak',  'menengah'],

        // RF19-RF27: Penghasilan Tinggi
        ['tinggi', 'muda',   'sedikit', 'kecil'],
        ['tinggi', 'muda',   'sedang',  'sangat_kecil'],
        ['tinggi', 'muda',   'banyak',  'sangat_kecil'],
        ['tinggi', 'dewasa', 'sedikit', 'menengah'],
        ['tinggi', 'dewasa', 'sedang',  'kecil'],
        ['tinggi', 'dewasa', 'banyak',  'sangat_kecil'],
        ['tinggi', 'tua',    'sedikit', 'besar'],
        ['tinggi', 'tua',    'sedang',  'menengah'],
        ['tinggi', 'tua',    'banyak',  'kecil'],
    ];

    // =========================================================
    // PUBLIC: HITUNG ISLAH (ENTRY POINT)
    // =========================================================

    public static function calculate_islah($harta_bersih, $ahli_waris_data)
    {
        // 1. Hitung skor fuzzy setiap ahli waris
        $hasil = array_map(function ($data) {
            return [
                'hubungan'   => $data['hubungan'],
                'faraidh'    => $data['faraidh'] ?? 0,
                'penghasilan' => $data['penghasilan'],
                'usia'       => $data['usia'],
                'aset'       => $data['aset'],
                'skor_fuzzy' => self::hitungSkorFuzzy(
                    $data['penghasilan'],
                    $data['usia'],
                    $data['aset']
                ),
            ];
        }, $ahli_waris_data);

        // 2. Distribusikan harta berdasarkan proporsi skor fuzzy
        $total_bobot = array_sum(array_column($hasil, 'skor_fuzzy'));

        foreach ($hasil as &$item) {
            if ($total_bobot > 0) {
                $item['islah']      = ($item['skor_fuzzy'] / $total_bobot) * $harta_bersih;
                $item['persentase'] = ($item['skor_fuzzy'] / $total_bobot) * 100;
            } else {
                $item['islah']      = $harta_bersih / count($hasil);
                $item['persentase'] = 100 / count($hasil);
            }
        }

        return [
            'hasil_islah' => $hasil,
            'total_bobot' => $total_bobot,
            'total_islah' => array_sum(array_column($hasil, 'islah')),
        ];
    }

    // =========================================================
    // PRIVATE: ENGINE FUZZY MAMDANI
    // =========================================================

    private static function hitungSkorFuzzy($penghasilan, $usia, $aset)
    {
        $mu_p = self::fuzzifikasiPenghasilan($penghasilan);
        $mu_u = self::fuzzifikasiUsia($usia);
        $mu_a = self::fuzzifikasiAset($aset);

        $agregasi = self::inferensi($mu_p, $mu_u, $mu_a);
        return self::defuzzifikasi($agregasi);
    }

    // =========================================================
    // TAHAP 1: FUZZIFIKASI
    // =========================================================

    private static function fuzzifikasiPenghasilan($nilai)
    {
        return [
            'rendah' => self::trapmf($nilai, self::PENGHASILAN['rendah']),
            'sedang' => self::trimf($nilai,  self::PENGHASILAN['sedang']),
            'tinggi' => self::trapmf($nilai, self::PENGHASILAN['tinggi']),
        ];
    }

    private static function fuzzifikasiUsia($nilai)
    {
        return [
            'muda'   => self::trapmf($nilai, self::USIA['muda']),
            'dewasa' => self::trimf($nilai,  self::USIA['dewasa']),
            'tua'    => self::trapmf($nilai, self::USIA['tua']),
        ];
    }

    private static function fuzzifikasiAset($nilai)
    {
        return [
            'sedikit' => self::trapmf($nilai, self::ASET['sedikit']),
            'sedang'  => self::trimf($nilai,  self::ASET['sedang']),
            'banyak'  => self::trapmf($nilai, self::ASET['banyak']),
        ];
    }

    // =========================================================
    // TAHAP 2 & 3: INFERENSI + KOMPOSISI (MAX)
    // =========================================================

    private static function inferensi($mu_p, $mu_u, $mu_a)
    {
        $agregasi = [
            'sangat_kecil' => 0,
            'kecil'        => 0,
            'menengah'     => 0,
            'besar'        => 0,
            'sangat_besar' => 0,
        ];

        foreach (self::RULES as $rule) {
            [$p, $u, $a, $output] = $rule;

            // Implikasi MIN
            $alpha = min($mu_p[$p], $mu_u[$u], $mu_a[$a]);

            // Komposisi MAX
            $agregasi[$output] = max($agregasi[$output], $alpha);
        }

        return $agregasi;
    }

    // =========================================================
    // TAHAP 4: DEFUZZIFIKASI (CENTROID)
    // =========================================================

    private static function defuzzifikasi($agregasi)
    {
        $numerator   = 0;
        $denominator = 0;
        $step        = 0.005; // resolusi scan (lebih kecil = lebih akurat)

        for ($z = 0; $z <= 1.0; $z += $step) {
            $mu = self::hitungMuGabungan($z, $agregasi);
            $numerator   += $z * $mu;
            $denominator += $mu;
        }

        return $denominator > 0 ? ($numerator / $denominator) : 0.5;
    }

    private static function hitungMuGabungan($z, $agregasi)
    {
        return max(
            min($agregasi['sangat_kecil'], self::trimf($z,  self::OUTPUT['sangat_kecil'])),
            min($agregasi['kecil'],        self::trimf($z,  self::OUTPUT['kecil'])),
            min($agregasi['menengah'],     self::trimf($z,  self::OUTPUT['menengah'])),
            min($agregasi['besar'],        self::trimf($z,  self::OUTPUT['besar'])),
            min($agregasi['sangat_besar'], self::trapmf($z, self::OUTPUT['sangat_besar'])),
        );
    }

    // =========================================================
    // FUNGSI KEANGGOTAAN
    // =========================================================

    private static function trimf($x, $params)
    {
        [$a, $b, $c] = $params;
        if ($x < $a || $x > $c) return 0.0;
        if ($x == $b) return 1.0;
        if ($b > $a && $x < $b) return ($x - $a) / ($b - $a);
        if ($c > $b && $x > $b) return ($c - $x) / ($c - $b);
        return 1.0;
    }

    private static function trapmf($x, $params)
    {
        [$a, $b, $c, $d] = $params;
        
        // FIX: gunakan < dan > bukan <= dan >=
        // agar nilai tepat di batas (x=0 saat a=0) tetap dihitung
        if ($x < $a || $x > $d) return 0.0;
        if ($x >= $b && $x <= $c) return 1.0;
        if ($b > $a && $x < $b) return ($x - $a) / ($b - $a);
        if ($d > $c && $x > $c) return ($d - $x) / ($d - $c);
        return 1.0;
    }
}