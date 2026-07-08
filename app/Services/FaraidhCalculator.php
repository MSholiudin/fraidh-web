<?php

namespace App\Services;

use Exception;

class FaraidhCalculator
{
    public static function calculate($harta_bersih, $daftar_ahli_waris)
    {
        return self::engine_faraidh_full($harta_bersih, $daftar_ahli_waris);
    }

    private static function engine_faraidh_full($harta_bersih, $daftar_aw)
    {
        $flags = self::buildFlags($daftar_aw);

        if ($flags['is_akdariyah']) {
            return self::selesaikanAkdariyah($harta_bersih, $daftar_aw);
        }

        [$hasil_faraidh, $pilihan_kakek] = self::hitungBagianTetap($harta_bersih, $daftar_aw, $flags);
        $hasil_faraidh = self::hitungAshobah($harta_bersih, $hasil_faraidh, $flags, $pilihan_kakek);
        $hasil_faraidh = self::hitungAshobahMaalGhoiri($harta_bersih, $hasil_faraidh);
        $hasil_faraidh = self::hitungRadd($harta_bersih, $hasil_faraidh);
        $hasil_faraidh = self::hitungAul($harta_bersih, $hasil_faraidh, $flags);

        $c = $flags['count'];
        $nenek_bapak_aktif = !$flags['has_ibu'] && !$flags['has_bapak'] && $c['nenek_bapak'] > 0;
        $nenek_ibu_aktif   = !$flags['has_ibu'] && $c['nenek_ibu'] > 0;

        if ($nenek_bapak_aktif && $nenek_ibu_aktif) {
            $hasil_faraidh[] = [
                'hubungan' => 'catatan',
                'label'    => 'Perlu Konsultasi',
                'nominal'  => 0,
                'catatan'  => 'Sistem mengasumsikan kedua nenek memiliki kedudukan yang sejajar sehingga berbagi 1/6 secara rata. Namun jika tingkat kedekatan keduanya berbeda, salah satu nenek bisa terhijab. Mohon konsultasikan dengan ahli faraidh.',
            ];
        }

        return $hasil_faraidh;
    }

    // =========================================================
    // TAHAP 0: BUILD FLAGS
    // =========================================================

    private static function buildFlags($daftar_aw)
    {
        $aw_names = array_map(fn($a) => strtolower($a['hubungan']), $daftar_aw);

        $count = [
            'anak_lk'      => self::countOccurrences($aw_names, 'anak laki-laki'),
            'anak_pr'      => self::countOccurrences($aw_names, 'anak perempuan'),
            'istri'        => self::countOccurrences($aw_names, 'istri'),
            'cucu_lk'      => self::countOccurrences($aw_names, 'cucu laki-laki'),
            'cucu_pr'      => self::countOccurrences($aw_names, 'cucu perempuan'),
            'sdr_kand_lk'  => self::countOccurrences($aw_names, 'saudara laki-laki sekandung'),
            'sdr_kand_pr'  => self::countOccurrences($aw_names, 'saudara perempuan sekandung'),
            'sdr_sebpk_lk' => self::countOccurrences($aw_names, 'saudara laki-laki sebapak'),
            'sdr_sebpk_pr' => self::countOccurrences($aw_names, 'saudara perempuan sebapak'),
            'sdr_seibu'    => self::countOccurrences($aw_names, 'saudara seibu'),
            'nenek_bapak'  => self::countOccurrences($aw_names, 'nenek pihak bapak'),
            'nenek_ibu'    => self::countOccurrences($aw_names, 'nenek pihak ibu'),
            'nenek'        => self::countOccurrences($aw_names, 'nenek pihak bapak')
                            + self::countOccurrences($aw_names, 'nenek pihak ibu'),
        ];

        $has_suami = in_array('suami', $aw_names);
        $has_ibu   = in_array('ibu', $aw_names);
        $has_kakek = in_array('kakek', $aw_names);
        $has_bapak = in_array('bapak', $aw_names);

        // --- Musytarakah ---
        $is_musytarakah_candidate = (
            $has_suami &&
            $has_ibu &&
            $count['sdr_seibu'] >= 2 &&
            $count['sdr_kand_lk'] > 0 &&
            $count['anak_lk'] === 0 && $count['anak_pr'] === 0 &&
            $count['cucu_lk'] === 0 && $count['cucu_pr'] === 0 &&
            !$has_bapak && !$has_kakek
        );

        $sisa_untuk_ashobah_musytarakah = false;
        if ($is_musytarakah_candidate) {
            $total_tanpa_sdr_kand = 0.5 + 1/6 + 1/3;
            $sisa_untuk_ashobah_musytarakah = ($total_tanpa_sdr_kand >= 1.0 - 0.001);
        }

        // --- Akdariyah ---
        // Syarat: suami + ibu + kakek + tepat 1 saudari pr (kandung ATAU sebapak), tanpa anak/cucu
        $is_akdariyah = false;
        if ($has_suami && $has_ibu && $has_kakek && !$has_bapak
            && $count['anak_lk'] === 0 && $count['anak_pr'] === 0
            && $count['cucu_lk'] === 0 && $count['cucu_pr'] === 0
        ) {
            $total_saudara_lk = $count['sdr_kand_lk'] + $count['sdr_sebpk_lk'];
            $total_saudari_pr = $count['sdr_kand_pr'] + $count['sdr_sebpk_pr'];
            if ($total_saudara_lk === 0 && $total_saudari_pr === 1) {
                $is_akdariyah = true;
            }
        }

        return [
            'count'          => $count,
            'aw_names'       => $aw_names,
            'has_anak'       => ($count['anak_lk'] > 0) || ($count['anak_pr'] > 0),
            'has_anak_lk'    => $count['anak_lk'] > 0,
            'has_anak_pr'    => $count['anak_pr'] > 0,
            'has_cucu'       => ($count['cucu_lk'] > 0) || ($count['cucu_pr'] > 0),
            'has_cucu_lk'    => $count['cucu_lk'] > 0,
            'has_cucu_pr'    => $count['cucu_pr'] > 0,
            'has_bapak'      => $has_bapak,
            'has_ibu'        => $has_ibu,
            'has_kakek'      => $has_kakek,
            'has_suami'      => $has_suami,
            'has_istri'      => in_array('istri', $aw_names),
            'has_sdr_kand'   => ($count['sdr_kand_lk'] > 0) || ($count['sdr_kand_pr'] > 0),
            'has_sdr_sebpk'  => ($count['sdr_sebpk_lk'] > 0) || ($count['sdr_sebpk_pr'] > 0),
            'has_sdr_seibu'  => $count['sdr_seibu'] > 0,
            'is_musytarakah' => $is_musytarakah_candidate && $sisa_untuk_ashobah_musytarakah,
            'is_akdariyah'   => $is_akdariyah,
        ];
    }

    // =========================================================
    // TAHAP 1: BAGIAN TETAP (ASHABUL FURUD)
    // =========================================================

    private static function hitungBagianTetap($total_harta, $daftar_aw, $flags)
    {
        $hasil_faraidh = [];
        $data_sementara = [];
        $pilihan_kakek = null;

        foreach ($daftar_aw as $aw) {
            $hub = strtolower($aw['hubungan']);
            [$porsi, $label, $pilihan_kakek] = self::tentukanPorsi($hub, $total_harta, $flags, $pilihan_kakek);

            if (!isset($data_sementara[$hub])) {
                $data_sementara[$hub] = ['porsi_awal' => $porsi, 'label_awal' => $label, 'count' => 1];
            } else {
                $data_sementara[$hub]['count']++;
            }
        }

        foreach ($data_sementara as $hub => $data) {
            $count = $data['count'];
            $porsi = $data['porsi_awal'];
            $label = $data['label_awal'];

            if ($porsi > 0) {
                if (strpos($label, 'dibagi') !== false) {
                    $nominal_per_orang = $total_harta * $porsi;
                } else {
                    $nominal_per_orang = ($total_harta * $porsi) / $count;
                }
            } else {
                $nominal_per_orang = 0;
            }

            for ($i = 0; $i < $count; $i++) {
                $hasil_faraidh[] = ['hubungan' => $hub, 'label' => $label, 'nominal' => $nominal_per_orang];
            }
        }

        return [$hasil_faraidh, $pilihan_kakek];
    }

    // =========================================================
    // HELPER: TENTUKAN PORSI PER AHLI WARIS
    // =========================================================

    private static function tentukanPorsi($hub, $total_harta, $flags, $pilihan_kakek)
    {
        $f = $flags;
        $c = $flags['count'];
        $porsi = 0.0;
        $label = "";

        switch (true) {

            case $hub === 'suami':
                $porsi = ($f['has_anak'] || $f['has_cucu']) ? 0.25 : 0.5;
                $label = ($f['has_anak'] || $f['has_cucu']) ? "1/4" : "1/2";
                break;

            case $hub === 'istri':
                if ($f['has_anak'] || $f['has_cucu']) {
                    $porsi = 0.125 / $c['istri'];
                    $label = $c['istri'] > 1 ? "1/8 (dibagi {$c['istri']})" : "1/8";
                } else {
                    $porsi = 0.25 / $c['istri'];
                    $label = $c['istri'] > 1 ? "1/4 (dibagi {$c['istri']})" : "1/4";
                }
                break;

            case $hub === 'ibu':
                $total_saudara = $c['sdr_kand_lk'] + $c['sdr_kand_pr']
                               + $c['sdr_sebpk_lk'] + $c['sdr_sebpk_pr']
                               + $c['sdr_seibu'];
                if ($f['has_anak'] || $f['has_cucu'] || $total_saudara >= 2) {
                    $porsi = 1/6; $label = "1/6";
                } elseif ($f['has_bapak'] && ($f['has_suami'] || $f['has_istri'])) {
                    $bagian_pasangan = $f['has_suami'] ? 0.5 : 0.25;
                    $porsi = (1 - $bagian_pasangan) / 3;
                    $label = "1/3 sisa (Gharawain)";
                } else {
                    $porsi = 1/3; $label = "1/3";
                }
                break;

            case $hub === 'bapak':
                if ($f['has_anak_lk'] || $f['has_cucu_lk']) {
                    $porsi = 1/6;
                    $label = "1/6";
                } elseif ($f['has_anak'] || $f['has_cucu']) {
                    $porsi = 1/6;
                    $label = "1/6 + Ashobah Binafsihi";
                } else {
                    $porsi = 0;
                    $label = "Ashobah Binafsihi";
                }
                break;

            case $hub === 'anak laki-laki':
                $label = $f['has_anak_pr'] ? "Ashobah Bil Ghoiri" : "Ashobah Binafsihi";
                break;

            case $hub === 'anak perempuan':
                if ($f['has_anak_lk']) {
                    $label = "Ashobah Bil Ghoiri";
                } elseif ($c['anak_pr'] === 1) {
                    $porsi = 0.5; $label = "1/2";
                } else {
                    $porsi = (2/3) / $c['anak_pr'];
                    $label = "2/3 (dibagi {$c['anak_pr']})";
                }
                break;

            case $hub === 'cucu laki-laki':
                if ($f['has_anak_lk']) {
                    $label = "Terhijab";
                } elseif ($f['has_anak']) {
                    $label = "Ashobah";
                } else {
                    $label = "Ashobah Binafsihi";
                }
                break;

            case $hub === 'cucu perempuan':
                [$porsi, $label] = self::tentukanPorsiCucuPerempuan($c, $f);
                break;

            case $hub === 'kakek':
                [$porsi, $label, $pilihan_kakek] = self::tentukanPorsiKakek($total_harta, $c, $f, $pilihan_kakek);
                break;

            case $hub === 'nenek pihak bapak':
                if ($f['has_ibu'] || $f['has_bapak']) {
                    $label = "Terhijab";
                } else {
                    $total_nenek_aktif = $c['nenek_bapak'] + $c['nenek_ibu'];
                    $porsi = (1/6) / max($total_nenek_aktif, 1);
                    $label = $total_nenek_aktif > 1 ? "1/6 (dibagi {$total_nenek_aktif})" : "1/6";
                }
                break;

            case $hub === 'nenek pihak ibu':
                if ($f['has_ibu']) {
                    $label = "Terhijab";
                } else {
                    $total_nenek_aktif = $c['nenek_ibu'] + ($f['has_bapak'] ? 0 : $c['nenek_bapak']);
                    $porsi = (1/6) / max($total_nenek_aktif, 1);
                    $label = $total_nenek_aktif > 1 ? "1/6 (dibagi {$total_nenek_aktif})" : "1/6";
                }
                break;

            case $hub === 'saudara seibu':
                if ($f['has_anak'] || $f['has_cucu'] || $f['has_bapak'] || $f['has_kakek']) {
                    $label = "Terhijab";
                } elseif ($f['is_musytarakah']) {
                    $total_peserta = $c['sdr_seibu'] + $c['sdr_kand_lk'] + $c['sdr_kand_pr'];
                    $porsi = (1/3) / $total_peserta;
                    $label = "Musytarakah (1/3 dibagi {$total_peserta})";
                } elseif ($c['sdr_seibu'] === 1) {
                    $porsi = 1/6; $label = "1/6";
                } else {
                    $porsi = (1/3) / $c['sdr_seibu'];
                    $label = "1/3 (dibagi {$c['sdr_seibu']})";
                }
                break;

            case $hub === 'saudara laki-laki sekandung':
                if ($f['has_anak_lk'] || $f['has_cucu_lk'] || $f['has_bapak']) {
                    $label = "Terhijab";
                } elseif ($f['has_kakek']) {
                    $label = "Ashobah (bersama Kakek)";
                } elseif ($f['is_musytarakah']) {
                    $total_peserta = $c['sdr_seibu'] + $c['sdr_kand_lk'] + $c['sdr_kand_pr'];
                    $porsi = (1/3) / $total_peserta;
                    $label = "Musytarakah (1/3 dibagi {$total_peserta})";
                } else {
                    $label = "Ashobah Binafsihi";
                }
                break;

            case $hub === 'saudara perempuan sekandung':
                if ($f['has_anak_lk'] || $f['has_cucu_lk'] || $f['has_bapak']) {
                    $label = "Terhijab";
                } elseif ($f['has_kakek']) {
                    $label = "Ashobah (bersama Kakek)";
                } elseif ($f['is_musytarakah']) {
                    $total_peserta = $c['sdr_seibu'] + $c['sdr_kand_lk'] + $c['sdr_kand_pr'];
                    $porsi = (1/3) / $total_peserta;
                    $label = "Musytarakah (1/3 dibagi {$total_peserta})";
                } else {
                    [$porsi, $label] = self::tentukanPorsiSdrPrKandung($c, $f);
                }
                break;

            case $hub === 'saudara laki-laki sebapak':
                if ($f['has_anak_lk'] || $f['has_cucu_lk'] || $f['has_bapak'] || $c['sdr_kand_lk'] > 0) {
                    $label = "Terhijab";
                } elseif ($f['has_kakek']) {
                    // Saudara sebapak ikut muaddah bersama kakek, hasilnya dihandle di ashobah
                    $label = "Ashobah (bersama Kakek)";
                } elseif ($c['sdr_kand_pr'] > 0 && ($f['has_anak_pr'] || $f['has_cucu_pr'])) {
                    $label = "Terhijab";
                } else {
                    $label = $c['sdr_sebpk_pr'] > 0 ? "Ashobah Bil Ghoiri" : "Ashobah Binafsihi";
                }
                break;

            case $hub === 'saudara perempuan sebapak':
                [$porsi, $label] = self::tentukanPorsiSdrPrSebapak($c, $f);
                break;
        }

        return [$porsi, $label, $pilihan_kakek];
    }

    // =========================================================
    // HELPER: PORSI CUCU PEREMPUAN
    // =========================================================

    private static function tentukanPorsiCucuPerempuan($c, $f)
    {
        if ($f['has_anak_lk']) {
            return [0.0, "Terhijab"];
        } elseif ($f['has_anak']) {
            if ($c['anak_pr'] === 1) {
                return [1/6, "1/6 (Takmilah)"];
            } elseif ($c['anak_pr'] >= 2) {
                return [0.0, $f['has_cucu_lk'] ? "Ashobah Bil Ghoiri" : "Terhijab"];
            }
        } else {
            if ($f['has_cucu_lk']) {
                return [0.0, "Ashobah Bil Ghoiri"];
            } elseif ($c['cucu_pr'] === 1) {
                return [0.5, "1/2"];
            } else {
                return [(2/3) / $c['cucu_pr'], "2/3 (dibagi {$c['cucu_pr']})"];
            }
        }
        return [0.0, ""];
    }

    // =========================================================
    // HELPER: PORSI KAKEK
    // =========================================================

    private static function tentukanPorsiKakek($total_harta, $c, $f, $pilihan_kakek)
    {
        // 1. Mahjub oleh Bapak
        if ($f['has_bapak']) {
            return [0.0, "Terhijab", $pilihan_kakek];
        }

        // 2. Akdariyah (dihandle terpisah)
        if ($f['is_akdariyah']) {
            return [0.0, "Akdariyah (dihandle terpisah)", $pilihan_kakek];
        }

        // 3. Ada Anak Laki-laki atau Cucu Laki-laki → 1/6 tetap
        if ($f['has_anak_lk'] || $f['has_cucu_lk']) {
            return [1/6, "1/6", $pilihan_kakek];
        }

        // 4. Ada Anak Perempuan (tanpa anak laki-laki) → 1/6 tetap
        if ($f['has_anak_pr'] && !$f['has_anak_lk']) {
            return [1/6, "1/6", $pilihan_kakek];
        }

        $ada_saudara = ($c['sdr_kand_lk'] + $c['sdr_kand_pr'] +
                        $c['sdr_sebpk_lk'] + $c['sdr_sebpk_pr']) > 0;
        // Catatan: sdr_seibu tidak dihitung (mahjub oleh kakek)

        // 5. Kakek tanpa saudara (asobah murni)
        if (!$ada_saudara) {
            return [1/6, "1/6 + Ashobah Binafsihi", $pilihan_kakek];
        }

        // 6. Kakek bersama saudara: tentukan apakah ada ahli waris lain
        $ada_ahli_waris_lain = (
            $f['has_ibu'] || $f['has_suami'] || $f['has_istri'] ||
            $f['has_cucu_pr'] || $c['sdr_seibu'] > 0
        );

        if (!$ada_ahli_waris_lain) {
            // KAKEK + SAUDARA SAJA (tanpa ahli waris lain)
            $total_bagian_saudara = self::hitungTotalKepalaMuaddah($c);

            if ($total_bagian_saudara < 4) {
                $total_kepala = 2 + $total_bagian_saudara;
                $porsi = 2 / $total_kepala;
                return [$porsi, "Muqosamah (terpilih)", 'muqosamah_solo'];
            } elseif ($total_bagian_saudara == 4) {
                return [1/3, "1/3 (sama dengan Muqosamah)", 'sepertiga_solo'];
            } else {
                return [1/3, "1/3 (terpilih)", 'sepertiga_solo'];
            }
        }

        // 7. Kakek + saudara + ahli waris lain
        return [1/6, "1/6 + Ashobah (sementara)", 'with_others'];
    }

    private static function hitungTotalKepalaMuaddah($c)
    {
        $kepala_kand  = ($c['sdr_kand_lk'] * 2) + $c['sdr_kand_pr'];
        $kepala_sebpk = ($c['sdr_sebpk_lk'] * 2) + $c['sdr_sebpk_pr'];
        $ada_sebpk    = ($c['sdr_sebpk_lk'] + $c['sdr_sebpk_pr']) > 0;

        // Jika tidak ada saudara sebapak, hanya hitung kandung
        if (!$ada_sebpk) {
            return $kepala_kand;
        }

        // Jika saudara kandung sudah >= 4 bagian, tidak perlu muaddah
        if ($kepala_kand >= 4) {
            return $kepala_kand;
        }

        // Muaddah berlaku: tambahkan sebapak (sampai batas 4 atau habis)
        // Namun dalam praktiknya kita hitung semua yang ada
        return $kepala_kand + $kepala_sebpk;
    }

    // =========================================================
    // HELPER: PORSI SAUDARA PEREMPUAN KANDUNG
    // =========================================================

    private static function tentukanPorsiSdrPrKandung($c, $f)
    {
        if ($f['has_anak_lk'] || $f['has_cucu_lk'] || $f['has_bapak']) {
            return [0.0, "Terhijab"];
        }
        if ($f['has_kakek']) {
            return [0.0, "Ashobah (bersama Kakek)"];
        }
        if ($f['has_anak_pr'] || $f['has_cucu_pr']) {
            if ($c['anak_pr'] >= 2) {
                return [0.0, "Terhijab"];
            }
            return [0.0, "Ashobah Ma'al Ghoiri"];
        }
        if (!$f['has_anak'] && !$f['has_cucu']) {
            if ($c['sdr_kand_lk'] > 0) {
                return [0.0, "Ashobah Bil Ghoiri"];
            } elseif ($c['sdr_kand_pr'] === 1) {
                return [0.5, "1/2"];
            } else {
                return [(2/3) / $c['sdr_kand_pr'], "2/3 (dibagi {$c['sdr_kand_pr']})"];
            }
        }
        return [0.0, ""];
    }

    // =========================================================
    // HELPER: PORSI SAUDARA PEREMPUAN SEBAPAK
    // =========================================================

    private static function tentukanPorsiSdrPrSebapak($c, $f)
    {
        if ($f['has_anak_lk'] || $f['has_cucu_lk'] || $f['has_bapak'] || $c['sdr_kand_lk'] > 0) {
            return [0.0, "Terhijab"];
        }
        if ($f['has_kakek']) {
            return [0.0, "Ashobah (bersama Kakek)"];
        }
        if ($c['sdr_kand_pr'] > 0 && ($f['has_anak_pr'] || $f['has_cucu_pr'])) {
            return [0.0, "Terhijab"];
        }
        if ($f['has_anak_pr'] || $f['has_cucu_pr']) {
            return [0.0, $c['anak_pr'] >= 2 ? "Terhijab" : "Ashobah Ma'al Ghoiri"];
        }
        if ($c['sdr_sebpk_lk'] > 0) {
            return [0.0, "Ashobah Bil Ghoiri"];
        }
        if ($c['sdr_sebpk_pr'] === 1) {
            if ($c['sdr_kand_pr'] === 1) {
                return [1/6, "1/6 (Takmilah)"];
            } elseif ($c['sdr_kand_pr'] >= 2) {
                return [0.0, "Terhijab"];
            }
            return [0.5, "1/2"];
        }
        if ($c['sdr_sebpk_pr'] >= 2) {
            if ($c['sdr_kand_pr'] === 1) {
                return [(2/3 - 0.5) / $c['sdr_sebpk_pr'], "1/6 (Takmilah, dibagi {$c['sdr_sebpk_pr']})"];
            } elseif ($c['sdr_kand_pr'] >= 2) {
                return [0.0, "Terhijab"];
            }
            return [(2/3) / $c['sdr_sebpk_pr'], "2/3 (dibagi {$c['sdr_sebpk_pr']})"];
        }
        return [0.0, "Ashobah Binafsihi"];
    }

    // =========================================================
    // TAHAP 2: ASHOBAH
    // =========================================================

    private static function hitungAshobah($total_harta, $hasil_faraidh, $flags, $pilihan_kakek)
    {
        $f = $flags;
        $c = $flags['count'];
        $total_bagian_tetap = array_sum(array_column($hasil_faraidh, 'nominal'));
        $sisa = $total_harta - $total_bagian_tetap;

        // === KAKEK (semua kasus) ===
        if ($pilihan_kakek) {
            return self::hitungAshobahKakek($total_harta, $hasil_faraidh, $c, $f, $pilihan_kakek);
        }

        // === ANAK LAKI-LAKI ===
        if ($f['has_anak_lk'] && $sisa > 0) {
            return self::bagikanAshobahBilGhoiriAtauBinafsihi(
                $hasil_faraidh, $sisa, 'anak laki-laki', 'anak perempuan', $c['anak_lk'], $c['anak_pr']
            );
        }

        // === CUCU LAKI-LAKI (jika tidak ada anak lk) ===
        if (!$f['has_anak_lk'] && $f['has_cucu_lk'] && $sisa > 0) {
            return self::bagikanAshobahBilGhoiriAtauBinafsihi(
                $hasil_faraidh, $sisa, 'cucu laki-laki', 'cucu perempuan', $c['cucu_lk'], $c['cucu_pr']
            );
        }

        // === BAPAK ===
        if ($f['has_bapak'] && $sisa > 0) {
            foreach ($hasil_faraidh as &$res) {
                if ($res['hubungan'] === 'bapak') {
                    $res['nominal'] += $sisa;
                    $res['label'] = ($f['has_anak'] || $f['has_cucu'])
                        ? "1/6 + Ashobah Binafsihi"
                        : "Ashobah Binafsihi";
                    break;
                }
            }
            return $hasil_faraidh;
        }

        // === KAKEK ASOBAH MURNI (tanpa saudara) ===
        if ($f['has_kakek'] && !$f['has_bapak'] && !$f['has_sdr_kand'] && !$f['has_sdr_sebpk'] && $sisa > 0) {
            foreach ($hasil_faraidh as &$res) {
                if ($res['hubungan'] === 'kakek') {
                    $res['nominal'] += $sisa;
                    $res['label']    = "1/6 + Ashobah";
                    break;
                }
            }
            return $hasil_faraidh;
        }

        // === SAUDARA KANDUNG ===
        if ($f['has_sdr_kand'] && !$f['has_kakek']
            && !($f['has_anak_lk'] || $f['has_cucu_lk'] || $f['has_bapak'])
            && $sisa > 0) {
            if ($c['sdr_kand_lk'] > 0) {
                return self::bagikanAshobahBilGhoiriAtauBinafsihi(
                    $hasil_faraidh, $sisa,
                    'saudara laki-laki sekandung', 'saudara perempuan sekandung',
                    $c['sdr_kand_lk'], $c['sdr_kand_pr']
                );
            }
        }

        // === SAUDARA SEBAPAK ===
        $sdr_kand_pr_ashobah_maal = false;
        foreach ($hasil_faraidh as $r) {
            if ($r['hubungan'] === 'saudara perempuan sekandung' && $r['label'] === "Ashobah Ma'al Ghoiri") {
                $sdr_kand_pr_ashobah_maal = true;
                break;
            }
        }

        if ($f['has_sdr_sebpk'] && !$f['has_kakek']
            && !($f['has_anak_lk'] || $f['has_cucu_lk'] || $f['has_bapak'])
            && $c['sdr_kand_lk'] === 0 && !$sdr_kand_pr_ashobah_maal
            && $sisa > 0) {
            if ($c['sdr_sebpk_lk'] > 0) {
                return self::bagikanAshobahBilGhoiriAtauBinafsihi(
                    $hasil_faraidh, $sisa,
                    'saudara laki-laki sebapak', 'saudara perempuan sebapak',
                    $c['sdr_sebpk_lk'], $c['sdr_sebpk_pr']
                );
            }
        }

        return $hasil_faraidh;
    }

    // =========================================================
    // HELPER: ASHOBAH KAKEK (semua kasus kakek + saudara)
    // =========================================================

    private static function hitungAshobahKakek($total_harta, $hasil_faraidh, $c, $f, $pilihan_kakek)
    {
        if ($pilihan_kakek === 'muqosamah_solo' || $pilihan_kakek === 'sepertiga_solo') {
            $bagian_kakek = 0;
            foreach ($hasil_faraidh as $r) {
                if ($r['hubungan'] === 'kakek') {
                    $bagian_kakek = $r['nominal'];
                    break;
                }
            }
            $sisa_untuk_sdr = $total_harta - $bagian_kakek;
            return self::bagikanSisaSetelahKakek($hasil_faraidh, $sisa_untuk_sdr, $total_harta, $c, true);
        }

        if ($pilihan_kakek === 'with_others') {
            $nominal_furud = 0;
            foreach ($hasil_faraidh as $r) {
                if ($r['hubungan'] === 'kakek') continue;
                if (self::isSaudara($r['hubungan'])) continue;
                if ($r['label'] === 'Terhijab') continue;
                if ($r['nominal'] > 0) $nominal_furud += $r['nominal'];
            }

            $sisa_setelah_furud = $total_harta - $nominal_furud;

            // Hitung total kepala saudara untuk muqosamah (dengan muaddah)
            $total_kepala_muaddah = self::hitungTotalKepalaMuaddah($c);

            // Opsi 1: Muqosamah
            $opsi_muqosamah = ($total_kepala_muaddah > 0)
                ? $sisa_setelah_furud * (2 / (2 + $total_kepala_muaddah))
                : $sisa_setelah_furud;

            // Opsi 2: 1/3 dari sisa
            $opsi_sepertiga_sisa = $sisa_setelah_furud / 3;

            // Opsi 3: 1/6 dari total harta (minimal)
            $opsi_seperenam = $total_harta / 6;

            $bagian_kakek = max($opsi_muqosamah, $opsi_sepertiga_sisa, $opsi_seperenam);

            // Tentukan label
            $sama_muqosamah    = abs($bagian_kakek - $opsi_muqosamah) < 0.01;
            $sama_sepertiga    = abs($bagian_kakek - $opsi_sepertiga_sisa) < 0.01;
            $lebih_dari_seperenam = $bagian_kakek > $opsi_seperenam + 0.01;

            if ($sama_muqosamah && $sama_sepertiga && $lebih_dari_seperenam) {
                $label_kakek = "Muqosamah = 1/3 sisa (terpilih)";
            } elseif ($sama_muqosamah && $lebih_dari_seperenam) {
                $label_kakek = "Muqosamah (terpilih)";
            } elseif ($sama_sepertiga && $lebih_dari_seperenam) {
                $label_kakek = "1/3 sisa (terpilih)";
            } else {
                $label_kakek = "1/6 (minimal)";
            }

            // Terapkan bagian kakek
            foreach ($hasil_faraidh as &$r) {
                if ($r['hubungan'] === 'kakek') {
                    $r['nominal'] = $bagian_kakek;
                    $r['label']   = $label_kakek;
                    break;
                }
            }
            unset($r);

            $sisa_untuk_sdr = $sisa_setelah_furud - $bagian_kakek;

            if ($sisa_untuk_sdr > 0.01) {
                $hasil_faraidh = self::bagikanSisaSetelahKakek(
                    $hasil_faraidh, $sisa_untuk_sdr, $total_harta, $c, false
                );
            }

            return $hasil_faraidh;
        }

        return $hasil_faraidh;
    }

    // =========================================================
    // HELPER: BAGIKAN SISA SETELAH KAKEK DAPAT BAGIANNYA
    // =========================================================

    private static function bagikanSisaSetelahKakek($hasil_faraidh, $sisa_untuk_sdr, $total_harta, $c, $is_solo)
    {
        if ($sisa_untuk_sdr <= 0.01) {
            // Tandai semua saudara sebagai terhijab jika sisa habis
            foreach ($hasil_faraidh as &$r) {
                if (self::isSaudara($r['hubungan']) && $r['nominal'] == 0) {
                    $r['label'] = "Terhijab (sisa habis)";
                }
            }
            return $hasil_faraidh;
        }

        // ============================================================
        // KASUS KHUSUS: 1 saudari kandung + ada saudara/i sebapak
        // Saudara sebapak sudah ikut muqosamah (muaddah), tapi setelah
        // kakek dapat bagian, saudari kandung dicek vs batas 1/2 harta
        // ============================================================
        $kasus_satu_sdri_kand_plus_sebpak = (
            $c['sdr_kand_lk'] === 0 &&
            $c['sdr_kand_pr'] === 1 &&
            ($c['sdr_sebpk_lk'] + $c['sdr_sebpk_pr']) > 0
        );

        if ($kasus_satu_sdri_kand_plus_sebpak) {
            $batas_setengah = $total_harta * 0.5;

            if ($sisa_untuk_sdr <= $batas_setengah + 0.01) {
                // Sisa ≤ 1/2 total harta → seluruh sisa untuk saudari kandung
                foreach ($hasil_faraidh as &$r) {
                    if ($r['hubungan'] === 'saudara perempuan sekandung') {
                        $r['nominal'] += $sisa_untuk_sdr;
                        $r['label']    = "Ashobah (seluruh sisa)";
                    }
                    if (self::isSaudara($r['hubungan']) && str_contains($r['hubungan'], 'sebapak')) {
                        $r['nominal'] = 0;
                        $r['label']   = "Ashobah habis (diambil saudari kandung)";
                    }
                }
            } else {
                // Sisa > 1/2 total harta → saudari kandung ambil 1/2, lebih untuk sebapak
                $bagian_sdri_kand  = $batas_setengah;
                $lebih_untuk_sebpk = $sisa_untuk_sdr - $bagian_sdri_kand;
                $total_kepala_sebpk = ($c['sdr_sebpk_lk'] * 2) + $c['sdr_sebpk_pr'];
                $nilai_per_kepala   = $total_kepala_sebpk > 0 ? $lebih_untuk_sebpk / $total_kepala_sebpk : 0;

                foreach ($hasil_faraidh as &$r) {
                    if ($r['hubungan'] === 'saudara perempuan sekandung') {
                        $r['nominal'] += $bagian_sdri_kand;
                        $r['label']    = "1/2 dari total harta";
                    } elseif (self::isSaudaraLaki($r['hubungan']) && str_contains($r['hubungan'], 'sebapak')) {
                        $r['nominal'] += $nilai_per_kepala * 2;
                        $r['label']    = "Ashobah Bil Ghoiri (sisa kakek)";
                    } elseif (self::isSaudaraPerempuan($r['hubungan']) && str_contains($r['hubungan'], 'sebapak')) {
                        $r['nominal'] += $nilai_per_kepala * 1;
                        $r['label']    = "Ashobah Bil Ghoiri (sisa kakek)";
                    }
                }
            }
            unset($r);
            return $hasil_faraidh;
        }

        // Tentukan siapa yang berhak menerima sisa
        $ada_sdr_kand_lk = $c['sdr_kand_lk'] > 0;
        $ada_sdr_kand_pr = $c['sdr_kand_pr'] > 0;

        $daftar_berhak = [];
        $total_kepala  = 0;

        foreach ($hasil_faraidh as $idx => $r) {
            if ($r['label'] === 'Terhijab' || str_contains($r['label'], 'Terhijab')) continue;
            if (!self::isSaudara($r['hubungan'])) continue;

            $is_kandung = str_contains($r['hubungan'], 'sekandung');
            $is_sebapak = str_contains($r['hubungan'], 'sebapak');

            // Jika ada sdr kandung (lk atau pr), sdr sebapak mahjub
            if ($is_sebapak && ($ada_sdr_kand_lk || $ada_sdr_kand_pr)) {
                // Kecuali kasus khusus 1 sdri kandung + sebapak sudah dihandle di atas
                continue;
            }

            $bagian = self::isSaudaraLaki($r['hubungan']) ? 2 : 1;
            $total_kepala += $bagian;
            $daftar_berhak[] = ['idx' => $idx, 'bagian' => $bagian];
        }

        if ($total_kepala > 0) {
            $nilai_per_kepala = $sisa_untuk_sdr / $total_kepala;
            foreach ($daftar_berhak as $data) {
                $hasil_faraidh[$data['idx']]['nominal'] += $nilai_per_kepala * $data['bagian'];
                $hasil_faraidh[$data['idx']]['label']    = "Ashobah (sisa dari kakek)";
            }
        }

        return $hasil_faraidh;
    }

    // =========================================================
    // HELPER: BAGIKAN ASHOBAH BIL GHOIRI ATAU BINAFSIHI
    // =========================================================

    private static function bagikanAshobahBilGhoiriAtauBinafsihi(
        $hasil_faraidh, $sisa, $hub_lk, $hub_pr, $count_lk, $count_pr
    ) {
        if ($count_lk > 0 && $count_pr > 0) {
            $total_kepala     = ($count_lk * 2) + $count_pr;
            $nilai_per_kepala = $total_kepala > 0 ? $sisa / $total_kepala : 0;
            foreach ($hasil_faraidh as &$res) {
                if ($res['hubungan'] === $hub_lk) {
                    $res['nominal'] = $nilai_per_kepala * 2;
                    $res['label']   = "Ashobah Bil Ghoiri (2 bagian)";
                } elseif ($res['hubungan'] === $hub_pr) {
                    $res['nominal'] = $nilai_per_kepala * 1;
                    $res['label']   = "Ashobah Bil Ghoiri (1 bagian)";
                }
            }
        } elseif ($count_lk > 0) {
            $nilai_per_orang = $sisa / $count_lk;
            foreach ($hasil_faraidh as &$res) {
                if ($res['hubungan'] === $hub_lk) {
                    $res['nominal'] = $nilai_per_orang;
                    $res['label']   = "Ashobah Binafsihi";
                }
            }
        }
        return $hasil_faraidh;
    }

    // =========================================================
    // TAHAP 2.5: ASHOBAH MA'AL GHOIRI
    // =========================================================

    private static function hitungAshobahMaalGhoiri($total_harta, $hasil_faraidh)
    {
        $sisa = $total_harta - array_sum(array_column($hasil_faraidh, 'nominal'));
        if ($sisa <= 0.01) return $hasil_faraidh;

        $indeks = [];
        foreach ($hasil_faraidh as $idx => $r) {
            if ($r['label'] === "Ashobah Ma'al Ghoiri") {
                $indeks[] = $idx;
            }
        }

        if (count($indeks) > 0) {
            $bagian_per_orang = $sisa / count($indeks);
            foreach ($indeks as $idx) {
                $hasil_faraidh[$idx]['nominal'] += $bagian_per_orang;
            }
        }

        return $hasil_faraidh;
    }

    // =========================================================
    // TAHAP 3: RADD
    // =========================================================

    private static function hitungRadd($total_harta, $hasil_faraidh)
    {
        $total_sudah_dibagi = array_sum(array_column($hasil_faraidh, 'nominal'));
        $sisa_harta = round($total_harta - $total_sudah_dibagi, 2);

        if ($sisa_harta <= 0) return $hasil_faraidh;

        $ada_ashobah = false;
        foreach ($hasil_faraidh as $r) {
            if (strpos($r['label'], 'Ashobah') !== false && $r['nominal'] > 0) {
                $ada_ashobah = true;
                break;
            }
        }

        if ($ada_ashobah) return $hasil_faraidh;

        $hasil_faraidh[] = [
            'hubungan' => 'Baitul Maal / Sabilillah',
            'label'    => 'Sisa (Radd)',
            'nominal'  => $sisa_harta,
        ];

        return $hasil_faraidh;
    }

    // =========================================================
    // TAHAP 4: 'AUL
    // =========================================================

    private static function hitungAul($total_harta, $hasil_faraidh, $flags)
    {
        $bagian_pecahan = [];
        foreach ($hasil_faraidh as $r) {
            if ($r['nominal'] <= 0) continue;
            if (strpos($r['label'], 'Ashobah') !== false) continue;
            if (strpos($r['label'], 'Terhijab') !== false) continue;

            [$pembilang, $penyebut] = self::labelKePecahan($r['label']);
            if ($pembilang > 0) {
                $bagian_pecahan[] = [$pembilang, $penyebut];
            }
        }

        if (empty($bagian_pecahan)) return $hasil_faraidh;

        $kpk = 1;
        foreach ($bagian_pecahan as $p) {
            $kpk = self::kpk($kpk, $p[1]);
        }

        $total_pembilang = 0;
        foreach ($bagian_pecahan as $p) {
            $total_pembilang += $p[0] * ($kpk / $p[1]);
        }

        if ($total_pembilang <= $kpk) return $hasil_faraidh;

        foreach ($hasil_faraidh as &$r) {
            if ($r['nominal'] <= 0) continue;
            if (strpos($r['label'], 'Ashobah') !== false) continue;
            if (strpos($r['label'], 'Terhijab') !== false) continue;

            [$pembilang, $penyebut] = self::labelKePecahan($r['label']);
            if ($pembilang > 0) {
                $faktor           = $kpk / $penyebut;
                $bagian_aul       = ($pembilang * $faktor) / $total_pembilang;
                $r['nominal']     = $total_harta * $bagian_aul;
                if (strpos($r['label'], "'Aul") === false) {
                    $r['label'] = str_replace(" + Radd", "", $r['label']) . " ('Aul)";
                }
            }
        }

        return $hasil_faraidh;
    }

    // =========================================================
    // AKDARIYAH
    // =========================================================

    private static function selesaikanAkdariyah($harta_bersih, $daftar_aw)
    {
        // Langkah:
        // 1. Susun bagian awal: suami=1/2, ibu=1/3, kakek=1/6, saudari=1/2
        // 2. Total = 9/6 → aul dengan penyebut 9
        //    suami=3/9, ibu=2/9, kakek=1/9, saudari=3/9
        // 3. Gabung kakek(1/9) + saudari(3/9) = 4/9, bagi 3 → kakek=8/27, saudari=4/27

        $hasil_faraidh = [];
        foreach ($daftar_aw as $aw) {
            $hubungan = strtolower($aw['hubungan']);
            switch ($hubungan) {
                case 'suami':
                    $nominal = $harta_bersih * (3 / 9);
                    $label   = "1/2 (Aul Akdariyah)";
                    break;
                case 'ibu':
                    $nominal = $harta_bersih * (2 / 9);
                    $label   = "1/3 (Aul Akdariyah)";
                    break;
                case 'kakek':
                    $nominal = $harta_bersih * (8 / 27);
                    $label   = "1/6 (Aul Akdariyah)";
                    break;
                case 'saudara perempuan sekandung':
                case 'saudara perempuan sebapak':
                    $nominal = $harta_bersih * (4 / 27);
                    $label   = "1/2 (Aul Akdariyah)";
                    break;
                default:
                    $nominal = 0;
                    $label   = "Terhijab (Akdariyah)";
                    break;
            }

            $hasil_faraidh[] = [
                'hubungan' => $aw['hubungan'],
                'label'    => $label,
                'nominal'  => $nominal,
            ];
        }

        return $hasil_faraidh;
    }

    // =========================================================
    // HELPER FUNCTIONS
    // =========================================================

    private static function isSaudara($hub)
    {
        return str_contains($hub, 'saudara');
    }

    private static function isSaudaraLaki($hub)
    {
        return str_contains($hub, 'saudara laki-laki') &&
               (str_contains($hub, 'sekandung') || str_contains($hub, 'sebapak'));
    }

    private static function isSaudaraPerempuan($hub)
    {
        return str_contains($hub, 'saudara perempuan') &&
               (str_contains($hub, 'sekandung') || str_contains($hub, 'sebapak'));
    }

    private static function countOccurrences($array, $value)
    {
        return count(array_filter($array, fn($item) => $item === $value));
    }

    private static function labelKePecahan($label)
    {
        if (str_contains($label, "1/2"))  return [1, 2];
        if (str_contains($label, "1/4")) {
            if (str_contains($label, "dibagi")) {
                preg_match('/dibagi\s*(\d+)/', $label, $m);
                $n = isset($m[1]) ? (int)$m[1] : 1;
                return [1, 4 * $n];
            }
            return [1, 4];
        }
        if (str_contains($label, "1/8")) {
            if (str_contains($label, "dibagi")) {
                preg_match('/dibagi\s*(\d+)/', $label, $m);
                $n = isset($m[1]) ? (int)$m[1] : 1;
                return [1, 8 * $n];
            }
            return [1, 8];
        }
        if (str_contains($label, "2/3")) {
            if (str_contains($label, "dibagi")) {
                preg_match('/dibagi\s*(\d+)/', $label, $m);
                $n = isset($m[1]) ? (int)$m[1] : 1;
                return [2, 3 * $n];
            }
            return [2, 3];
        }
        if (str_contains($label, "1/3")) {
            if (str_contains($label, "dibagi")) {
                preg_match('/dibagi\s*(\d+)/', $label, $m);
                $n = isset($m[1]) ? (int)$m[1] : 1;
                return [1, 3 * $n];
            }
            return [1, 3];
        }
        if (str_contains($label, "1/6")) {
            if (str_contains($label, "dibagi")) {
                preg_match('/dibagi\s*(\d+)/', $label, $m);
                $n = isset($m[1]) ? (int)$m[1] : 1;
                return [1, 6 * $n];
            }
            return [1, 6];
        }
        return [0, 1];
    }

    private static function gcd($a, $b)
    {
        while ($b != 0) { [$a, $b] = [$b, $a % $b]; }
        return $a;
    }

    private static function kpk($a, $b)
    {
        return ($a * $b) / self::gcd($a, $b);
    }
}