<?php

namespace App\Services;

class FuzzyMamdaniService
{
    // =========================================================
    // KONSTANTA FUNGSI KEANGGOTAAN
    // =========================================================

    // Penghasilan (Rupiah) - Referensi: UMK Jember 2026 (Rp 3.000.000)
    // Overlap: Rendah-Sedang di 2.5jt-3.5jt | Sedang-Tinggi di 5.5jt-6.5jt
    private const PENGHASILAN = [
        'rendah' => [0, 0, 2_500_000, 3_500_000],           // trapmf
        'sedang' => [2_500_000, 4_500_000, 6_500_000],       // trimf
        'tinggi' => [5_500_000, 7_000_000, 15_000_000, 15_000_000], // trapmf
    ];

    // Usia (Tahun) - Referensi: BPS usia produktif
    // Overlap: Muda-Dewasa di 20-25 | Dewasa-Tua di 50-60
    private const USIA = [
        'muda'   => [0, 0, 20, 25],      // trapmf
        'dewasa' => [20, 40, 60],         // trimf
        'tua'    => [50, 60, 100, 100],   // trapmf
    ];

    // Aset (Rupiah) - Referensi: Kepmenpupr, LPS, World Bank
    // Overlap: Sedikit-Sedang di 250jt-500jt | Sedang-Banyak di 1.2M-1.5M
    private const ASET = [
        'sedikit' => [0, 0, 250_000_000, 500_000_000],                          // trapmf
        'sedang'  => [250_000_000, 1_000_000_000, 1_500_000_000],               // trimf
        'banyak'  => [1_200_000_000, 1_500_000_000, 3_000_000_000, 3_000_000_000], // trapmf
    ];

    // Output bobot kebutuhan (0-1)
    // Centroid: sangat_kecil=0.1 | kecil=0.3 | menengah=0.5 | besar=0.7 | sangat_besar=0.9
    private const OUTPUT = [
        'sangat_kecil' => ['type' => 'trimf',  'params' => [0.0, 0.1, 0.2]],
        'kecil'        => ['type' => 'trimf',  'params' => [0.1, 0.3, 0.5]],
        'menengah'     => ['type' => 'trimf',  'params' => [0.3, 0.5, 0.7]],
        'besar'        => ['type' => 'trimf',  'params' => [0.5, 0.7, 0.9]],
        'sangat_besar' => ['type' => 'trapmf', 'params' => [0.8, 0.9, 1.0, 1.0]],
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

    /**
     * Hitung distribusi islah berdasarkan bobot fuzzy.
     *
     * @param  float  $harta_ahli_waris  Harta yang menjadi hak ahli waris
     *                                   (sudah dikurangi bagian Radd ke Baitul Maal)
     * @param  array  $ahli_waris_data   Data tiap ahli waris
     * @return array
     */
    public static function hitungIslah(float $harta_ahli_waris, array $ahli_waris_data): array
    {
        // 1. Hitung skor fuzzy setiap ahli waris
        $hasil = array_map(function ($data) {
            return [
                'hubungan'    => $data['hubungan'],
                'faraidh'     => $data['faraidh'] ?? 0,
                'penghasilan' => $data['penghasilan'],
                'usia'        => $data['usia'],
                'aset'        => $data['aset'],
                'skor_fuzzy'  => self::hitungSkorFuzzy(
                    $data['penghasilan'],
                    $data['usia'],
                    $data['aset']
                ),
            ];
        }, $ahli_waris_data);

        // 2. Normalisasi bobot dan distribusi islah
        // Menggunakan skor_fuzzy presisi penuh (belum dibulatkan)
        // agar total islah tepat sama dengan harta_ahli_waris
        $total_bobot = array_sum(array_column($hasil, 'skor_fuzzy'));

        foreach ($hasil as &$item) {
            if ($total_bobot > 0) {
                $item['bobot_normal'] = $item['skor_fuzzy'] / $total_bobot;
                $item['islah']        = ($item['skor_fuzzy'] / $total_bobot) * $harta_ahli_waris;
                $item['persentase']   = $item['bobot_normal'] * 100;
            } else {
                // Fallback: bagi rata jika semua skor 0
                $n                    = count($hasil);
                $item['bobot_normal'] = 1 / $n;
                $item['islah']        = $harta_ahli_waris / $n;
                $item['persentase']   = 100 / $n;
            }
        }
        unset($item);

        return [
            'hasil_islah'       => $hasil,
            'total_bobot'       => $total_bobot,
            'harta_ahli_waris'  => $harta_ahli_waris,
            'total_islah'       => array_sum(array_column($hasil, 'islah')),
        ];
    }

    // =========================================================
    // PRIVATE: ENGINE FUZZY MAMDANI
    // =========================================================

    private static function hitungSkorFuzzy(
        float $penghasilan,
        float $usia,
        float $aset
    ): float {
        $mu_p = self::fuzzifikasiPenghasilan($penghasilan);
        $mu_u = self::fuzzifikasiUsia($usia);
        $mu_a = self::fuzzifikasiAset($aset);

        $agregasi = self::inferensi($mu_p, $mu_u, $mu_a);

        return self::defuzzifikasi($agregasi);
    }

    // =========================================================
    // TAHAP 1: FUZZIFIKASI
    // =========================================================

    private static function fuzzifikasiPenghasilan(float $nilai): array
    {
        return [
            'rendah' => self::trapmf($nilai, self::PENGHASILAN['rendah']),
            'sedang' => self::trimf($nilai,  self::PENGHASILAN['sedang']),
            'tinggi' => self::trapmf($nilai, self::PENGHASILAN['tinggi']),
        ];
    }

    private static function fuzzifikasiUsia(float $nilai): array
    {
        return [
            'muda'   => self::trapmf($nilai, self::USIA['muda']),
            'dewasa' => self::trimf($nilai,  self::USIA['dewasa']),
            'tua'    => self::trapmf($nilai, self::USIA['tua']),
        ];
    }

    private static function fuzzifikasiAset(float $nilai): array
    {
        return [
            'sedikit' => self::trapmf($nilai, self::ASET['sedikit']),
            'sedang'  => self::trimf($nilai,  self::ASET['sedang']),
            'banyak'  => self::trapmf($nilai, self::ASET['banyak']),
        ];
    }

    // =========================================================
    // TAHAP 2 & 3: INFERENSI (MIN) + KOMPOSISI (MAX)
    // =========================================================

    private static function inferensi(
        array $mu_p,
        array $mu_u,
        array $mu_a
    ): array {
        $agregasi = [
            'sangat_kecil' => 0.0,
            'kecil'        => 0.0,
            'menengah'     => 0.0,
            'besar'        => 0.0,
            'sangat_besar' => 0.0,
        ];

        foreach (self::RULES as $rule) {
            [$p, $u, $a, $output] = $rule;

            // Implikasi MIN
            $alpha = min($mu_p[$p], $mu_u[$u], $mu_a[$a]);

            // Komposisi MAX
            if ($alpha > $agregasi[$output]) {
                $agregasi[$output] = $alpha;
            }
        }

        return $agregasi;
    }

    // =========================================================
    // TAHAP 4: DEFUZZIFIKASI (CENTER OF AREA)
    // =========================================================

    private static function defuzzifikasi(array $agregasi): float
    {
        $numerator   = 0.0;
        $denominator = 0.0;
        $step        = 0.001; // resolusi scan

        for ($z = 0.0; $z <= 1.0; $z += $step) {
            $mu           = self::hitungMuGabungan($z, $agregasi);
            $numerator   += $z * $mu;
            $denominator += $mu;
        }

        return $denominator > 0 ? ($numerator / $denominator) : 0.0;
    }

    private static function hitungMuGabungan(float $z, array $agregasi): float
    {
        $mu_values = [];

        foreach (self::OUTPUT as $label => $def) {
            $mu_mentah = $def['type'] === 'trapmf'
                ? self::trapmf($z, $def['params'])
                : self::trimf($z, $def['params']);

            $mu_values[] = min($agregasi[$label], $mu_mentah);
        }

        return max($mu_values);
    }

    // =========================================================
    // FUNGSI KEANGGOTAAN DASAR
    // =========================================================

    /**
     * Trapezoid membership function.
     * Plateau antara b dan c, naik dari a ke b, turun dari c ke d.
     * Menggunakan < dan > (bukan <= >=) agar nilai tepat di batas
     * tetap dihitung dengan benar (misal x=0 saat a=b=0 → 1.0)
     */
    private static function trapmf(float $x, array $params): float
    {
        [$a, $b, $c, $d] = $params;

        if ($x < $a || $x > $d) return 0.0;
        if ($x >= $b && $x <= $c) return 1.0;
        if ($b > $a && $x < $b) return ($x - $a) / ($b - $a);
        if ($d > $c && $x > $c) return ($d - $x) / ($d - $c);

        return 1.0;
    }

    /**
     * Triangle membership function.
     * Puncak di b, naik dari a ke b, turun dari b ke c.
     */
    private static function trimf(float $x, array $params): float
    {
        [$a, $b, $c] = $params;

        if ($x <= $a || $x >= $c) return 0.0;
        if ($x == $b) return 1.0;
        if ($b > $a && $x < $b) return ($x - $a) / ($b - $a);
        if ($c > $b && $x > $b) return ($c - $x) / ($c - $b);

        return 1.0;
    }
}