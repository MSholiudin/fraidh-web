<?php

namespace App\Services;

class FuzzyMamdaniService
{
    /**
     * ENGINE FUZZY MAMDANI (DENGAN BIAS GENDER)
     */
    public static function engine_fuzzy_final($penghasilan_val, $usia_val, $tanggungan_val, $hubungan)
    {
        // 1. Fuzzyfikasi
        $p_rendah = self::trapmf($penghasilan_val, [0, 0, 1500000, 2500001]);
        $p_sedang = self::trimf($penghasilan_val, [2000000, 3750000, 5000001]);
        $p_tinggi = self::trapmf($penghasilan_val, [4500000, 6000000, 15000000, 15000000]);
        
        $u_muda = self::trapmf($usia_val, [0, 0, 17, 25]);
        $u_dewasa = self::trimf($usia_val, [24, 40, 55]);
        $u_tua = self::trapmf($usia_val, [50, 60, 100, 100]);
        
        $t_tidak_ada = self::trapmf($tanggungan_val, [0, 0, 0, 1]);
        $t_sedikit = self::trimf($tanggungan_val, [0, 1.5, 3]);
        $t_banyak = self::trapmf($tanggungan_val, [2, 4, 15, 15]);
        
        // 2. Rules dengan output langsung berupa centroid
        $rules = [];
        
        // Rule 1-27 dengan centroid value langsung
        $rules[] = ['w' => min($p_rendah, $u_muda, $t_tidak_ada), 'c' => 0.533]; // menengah
        $rules[] = ['w' => min($p_rendah, $u_muda, $t_sedikit), 'c' => 0.767];   // besar
        $rules[] = ['w' => min($p_rendah, $u_muda, $t_banyak), 'c' => 0.933];    // sangat_besar
        
        $rules[] = ['w' => min($p_rendah, $u_dewasa, $t_tidak_ada), 'c' => 0.333]; // kecil
        $rules[] = ['w' => min($p_rendah, $u_dewasa, $t_sedikit), 'c' => 0.533];   // menengah
        $rules[] = ['w' => min($p_rendah, $u_dewasa, $t_banyak), 'c' => 0.767];    // besar
        
        $rules[] = ['w' => min($p_rendah, $u_tua, $t_tidak_ada), 'c' => 0.767];    // besar
        $rules[] = ['w' => min($p_rendah, $u_tua, $t_sedikit), 'c' => 0.933];      // sangat_besar
        $rules[] = ['w' => min($p_rendah, $u_tua, $t_banyak), 'c' => 0.933];       // sangat_besar
        
        $rules[] = ['w' => min($p_sedang, $u_muda, $t_tidak_ada), 'c' => 0.333];   // kecil
        $rules[] = ['w' => min($p_sedang, $u_muda, $t_sedikit), 'c' => 0.533];     // menengah
        $rules[] = ['w' => min($p_sedang, $u_muda, $t_banyak), 'c' => 0.767];      // besar
        
        $rules[] = ['w' => min($p_sedang, $u_dewasa, $t_tidak_ada), 'c' => 0.133]; // sangat_kecil
        $rules[] = ['w' => min($p_sedang, $u_dewasa, $t_sedikit), 'c' => 0.333];   // kecil
        $rules[] = ['w' => min($p_sedang, $u_dewasa, $t_banyak), 'c' => 0.533];    // menengah
        
        $rules[] = ['w' => min($p_sedang, $u_tua, $t_tidak_ada), 'c' => 0.533];    // menengah
        $rules[] = ['w' => min($p_sedang, $u_tua, $t_sedikit), 'c' => 0.767];      // besar
        $rules[] = ['w' => min($p_sedang, $u_tua, $t_banyak), 'c' => 0.933];       // sangat_besar
        
        $rules[] = ['w' => min($p_tinggi, $u_muda, $t_tidak_ada), 'c' => 0.133];   // sangat_kecil
        $rules[] = ['w' => min($p_tinggi, $u_muda, $t_sedikit), 'c' => 0.333];     // kecil
        $rules[] = ['w' => min($p_tinggi, $u_muda, $t_banyak), 'c' => 0.533];      // menengah
        
        $rules[] = ['w' => min($p_tinggi, $u_dewasa, $t_tidak_ada), 'c' => 0.133]; // sangat_kecil
        $rules[] = ['w' => min($p_tinggi, $u_dewasa, $t_sedikit), 'c' => 0.133];   // sangat_kecil
        $rules[] = ['w' => min($p_tinggi, $u_dewasa, $t_banyak), 'c' => 0.333];    // kecil
        
        $rules[] = ['w' => min($p_tinggi, $u_tua, $t_tidak_ada), 'c' => 0.333];    // kecil
        $rules[] = ['w' => min($p_tinggi, $u_tua, $t_sedikit), 'c' => 0.533];      // menengah
        $rules[] = ['w' => min($p_tinggi, $u_tua, $t_banyak), 'c' => 0.767];       // besar
        
        // 3. Defuzzifikasi
        $total_weighted = 0;
        $total_weight = 0;
        
        foreach ($rules as $rule) {
            $total_weighted += $rule['c'] * $rule['w'];
            $total_weight += $rule['w'];
        }
        
        $out = ($total_weight > 0) ? ($total_weighted / $total_weight) : 0.5;
        
        // 4. Proteksi Sosial
        if (strtolower($hubungan) == 'anak perempuan') {
            $out = min(1.0, $out + 0.05);
        }
        
        return max(0, min(1, $out));
    }
    
    // ============== HELPER FUNCTIONS ==============
    
    /**
     * Triangular Membership Function
     */
    private static function trimf($x, $params)
    {
        list($a, $b, $c) = $params;
        
        if ($x <= $a || $x >= $c) {
            return 0;
        } elseif ($x == $b) {
            return 1;
        } elseif ($x < $b) {
            return ($x - $a) / ($b - $a);
        } else {
            return ($c - $x) / ($c - $b);
        }
    }
    
    /**
     * Trapezoidal Membership Function
     */
    private static function trapmf($x, $params)
    {
        list($a, $b, $c, $d) = $params;
        
        if ($x <= $a || $x >= $d) {
            return 0;
        } elseif ($x >= $b && $x <= $c) {
            return 1;
        } elseif ($x < $b) {
            return ($x - $a) / ($b - $a);
        } else {
            return ($d - $x) / ($d - $c);
        }
    }
    
    /**
     * Output membership functions (BOBOT)
     * sangat_kecil: [0, 0.1, 0.3]
     * kecil: [0.2, 0.3, 0.5]
     * menengah: [0.4, 0.5, 0.7]
     * besar: [0.6, 0.8, 0.9]
     * sangat_besar: [0.8, 1, 1]
     */
    private static function trimf_output($type)
    {
        $params = [
            'sangat_kecil' => [0.0, 0.1, 0.3],
            'kecil' => [0.2, 0.3, 0.5],
            'menengah' => [0.4, 0.5, 0.7],
            'besar' => [0.6, 0.8, 0.9],
            'sangat_besar' => [0.8, 1.0, 1.0]
        ];
        
        return $params[$type] ?? [0.4, 0.5, 0.7]; // default menengah
    }
    
    /**
     * Get centroid for output type
     */
    private static function get_centroid_for_output($output_type)
    {
        $centroids = [
            'sangat_kecil' => 0.133,  // (0 + 0.1 + 0.3) / 3
            'kecil' => 0.333,         // (0.2 + 0.3 + 0.5) / 3
            'menengah' => 0.533,      // (0.4 + 0.5 + 0.7) / 3
            'besar' => 0.767,         // (0.6 + 0.8 + 0.9) / 3
            'sangat_besar' => 0.933   // (0.8 + 1 + 1) / 3
        ];
        
        return $centroids[$output_type] ?? 0.5;
    }
    
    /**
     * Get weight for output type
     */
    private static function get_weight_for_output($output_type)
    {
        // Weight based on output type importance
        $weights = [
            'sangat_kecil' => 0.1,
            'kecil' => 0.3,
            'menengah' => 0.5,
            'besar' => 0.7,
            'sangat_besar' => 0.9
        ];
        
        return $weights[$output_type] ?? 0.5;
    }
    
    /**
     * Calculate Islah distribution
     */
    public static function calculate_islah($harta_bersih, $ahli_waris_data)
    {
        $total_bobot = 0;
        $list_fm = [];
        
        // Hitung skor fuzzy untuk setiap ahli waris
        foreach ($ahli_waris_data as $data) {
            $skor = self::engine_fuzzy_final(
                $data['penghasilan'],
                $data['usia'],
                $data['tanggungan'],
                $data['hubungan']
            );
            
            $total_bobot += $skor;
            
            $list_fm[] = [
                'hubungan' => $data['hubungan'],
                'faraidh' => $data['faraidh'] ?? 0,
                'label' => $data['label'] ?? '',
                'skor_fuzzy' => $skor,
                'penghasilan' => $data['penghasilan'],
                'usia' => $data['usia'],
                'tanggungan' => $data['tanggungan']
            ];
        }
        
        // Hitung distribusi Islah
        foreach ($list_fm as &$item) {
            if ($total_bobot > 0) {
                $item['islah'] = ($item['skor_fuzzy'] / $total_bobot) * $harta_bersih;
                $item['persentase'] = ($item['skor_fuzzy'] / $total_bobot) * 100;
            } else {
                $item['islah'] = $item['faraidh'];
                $item['persentase'] = 0;
            }
        }
        
        return [
            'hasil_islah' => $list_fm,
            'total_bobot' => $total_bobot,
            'total_faraidh' => array_sum(array_column($list_fm, 'faraidh')),
            'total_islah' => array_sum(array_column($list_fm, 'islah'))
        ];
    }
    
    /**
     * Simple fallback method jika fuzzy error
     */
    public static function calculate_bobot_simple($penghasilan_val, $usia_val, $tanggungan_val, $hubungan)
    {
        $bobot = 0.5;
        
        // Logika sederhana berdasarkan rules
        if ($penghasilan_val <= 2500000) {
            if ($usia_val <= 25) {
                $bobot += 0.2;
            } elseif ($usia_val >= 60) {
                $bobot += 0.25;
            } else {
                $bobot += 0.15;
            }
        } elseif ($penghasilan_val <= 5000000) {
            $bobot += 0.1;
        } else {
            $bobot -= 0.1;
        }
        
        if ($tanggungan_val >= 3) {
            $bobot += 0.15;
        } elseif ($tanggungan_val >= 1) {
            $bobot += 0.05;
        }
        
        // PROTEKSI SOSIAL
        if (strtolower($hubungan) == 'anak perempuan') {
            $bobot = min(1.0, $bobot + 0.05);
        }
        
        return max(0, min(1, $bobot));
    }
}