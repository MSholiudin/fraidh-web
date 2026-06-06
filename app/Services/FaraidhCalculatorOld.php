<?php

namespace App\Services;

use Exception;

class FaraidhCalculator
{
    public static function engine_faraidh_full($harta_bersih, $daftar_aw)
    {
        // Ambil nama hubungan semua ahli waris
        $aw_names = array_map(function($a) {
            return strtolower($a['hubungan']);
        }, $daftar_aw);
        
        // Hitung jumlah masing-masing ahli waris
        $count_anak_lk = self::countOccurrences($aw_names, 'anak laki-laki');
        $count_anak_pr = self::countOccurrences($aw_names, 'anak perempuan');
        $count_cucu_lk = self::countOccurrences($aw_names, 'cucu laki-laki');
        $count_cucu_pr = self::countOccurrences($aw_names, 'cucu perempuan');
        $count_sdr_kand_lk = self::countOccurrences($aw_names, 'saudara laki-laki sekandung');
        $count_sdr_kand_pr = self::countOccurrences($aw_names, 'saudara perempuan sekandung');
        $count_sdr_sebpk_lk = self::countOccurrences($aw_names, 'saudara laki-laki sebapak');
        $count_sdr_sebpk_pr = self::countOccurrences($aw_names, 'saudara perempuan sebapak');
        $count_sdr_seibu = self::countOccurrences($aw_names, 'saudara seibu');
        $count_nenek = self::countOccurrences($aw_names, 'nenek pihak bapak') + 
                      self::countOccurrences($aw_names, 'nenek pihak ibu');
        
        // Flags kondisi
        $has_anak = ($count_anak_lk > 0) || ($count_anak_pr > 0);
        $has_anak_lk = $count_anak_lk > 0;
        $has_anak_pr = $count_anak_pr > 0;
        $has_cucu = ($count_cucu_lk > 0) || ($count_cucu_pr > 0);
        $has_cucu_lk = $count_cucu_lk > 0;
        $has_cucu_pr = $count_cucu_pr > 0;
        $has_bapak = in_array('bapak', $aw_names);
        $has_ibu = in_array('ibu', $aw_names);
        $has_kakek = in_array('kakek', $aw_names);
        $has_sdr_kand = ($count_sdr_kand_lk > 0) || ($count_sdr_kand_pr > 0);
        $has_sdr_sebpk = ($count_sdr_sebpk_lk > 0) || ($count_sdr_sebpk_pr > 0);
        $has_sdr_seibu = $count_sdr_seibu > 0;
        $has_suami = in_array('suami', $aw_names);
        $has_istri = in_array('istri', $aw_names);
        
        $total_harta = $harta_bersih;
        $hasil_faraidh = [];
        $data_sementara = [];
        
        // Variabel untuk menyimpan pilihan kakek
        $pilihan_kakek = null;  // 'ashobah', 'sepertiga', 'muqossamah'
        $bagian_kakek_terpilih = 0;
        
        // --- TAHAP 1: ASHABUL FURUD (Bagian Pasti) ---
        foreach ($daftar_aw as $aw) {
            $hub = strtolower($aw['hubungan']);
            $porsi = 0.0;
            $label = "";
            
            // --- SUAMI & ISTRI ---
            if ($hub == 'suami') {
                if ($has_anak || $has_cucu) {
                    $porsi = 0.25;
                    $label = "1/4";  // R05
                } else {
                    $porsi = 0.5;
                    $label = "1/2";   // R04
                }
            } elseif ($hub == 'istri') {
                if ($has_anak || $has_cucu) {
                    $porsi = 0.125;
                    $label = "1/8";  // R07
                } else {
                    $porsi = 0.25;
                    $label = "1/4";   // R06
                }
            }
            
            // --- IBU ---
            elseif ($hub == 'ibu') {
                // Ibu dapat 1/6 jika:
                // 1. Ada anak atau cucu
                // 2. Ada lebih dari 1 saudara seibu
                // 3. Ada saudara kandung (sekandung) atau saudara sebapak
                if ($has_anak || $has_cucu || $count_sdr_seibu > 1 || $has_sdr_kand || $has_sdr_sebpk) {
                    $porsi = 1/6;
                    $label = "1/6";    // IB02
                } else {
                    $porsi = 1/3;
                    $label = "1/3";    // IB01
                }
            }
            
            // --- BAPAK ---
            elseif ($hub == 'bapak') {
                if ($has_anak_lk || $has_cucu_lk) {
                    $porsi = 1/6;
                    $label = "1/6";    // BP01
                } else {
                    // Bagian 1/6 akan diambil nanti, sisa sebagai ashobah
                    $porsi = 1/6;
                    $label = "1/6 + Ashobah";
                }
            }
            
            // --- ANAK PEREMPUAN ---
            elseif ($hub == 'anak perempuan') {
                if (!$has_anak_lk) {  // Tidak ada anak laki-laki
                    if ($count_anak_pr == 1) {
                        $porsi = 0.5;
                        $label = "1/2";  // AN03
                    } elseif ($count_anak_pr >= 2) {
                        $porsi_per_orang = (2/3) / $count_anak_pr;
                        $porsi = $porsi_per_orang;
                        $label = "2/3 (dibagi {$count_anak_pr})";  // AN04
                    }
                } else {
                    // Akan dihitung di tahap ashobah bil ghoiri
                    $label = "Ashobah Bil Ghoiri";
                }
            }
            
            // --- ANAK LAKI-LAKI ---
            elseif ($hub == 'anak laki-laki') {
                // Anak laki-laki murni ashobah (tidak ada bagian tetap)
                if ($has_anak_pr) {  // Jika ada anak perempuan
                    $label = "Ashobah Bil Ghoiri";
                } else {  // Hanya anak laki-laki saja
                    $label = "Ashobah Binafsihi";
                }
            }
            
            // --- CUCU PEREMPUAN ---
            elseif ($hub == 'cucu perempuan') {
                if ($has_anak_lk) {
                    $label = "Terhijab";  // CC01
                } elseif ($has_anak) {  // Ada anak (tapi tidak laki-laki)
                    if ($count_anak_pr == 1) {
                        $porsi = 1/6;
                        $label = "1/6 (Takmilah)";  // CC05
                    } elseif ($count_anak_pr >= 2) {
                        if ($has_cucu_lk) {
                            $label = "Ashobah Bil Ghoiri";  // CC08
                        } else {
                            $label = "Terhijab";  // CC06
                        }
                    }
                } else {  // Tidak ada anak sama sekali
                    if ($count_cucu_pr == 1) {
                        $porsi = 0.5;
                        $label = "1/2";  // CC03
                    } elseif ($count_cucu_pr >= 2) {
                        // FIX: 2/3 dibagi rata ke semua cucu perempuan
                        $porsi_per_orang = (2/3) / $count_cucu_pr;
                        $porsi = $porsi_per_orang;
                        $label = "2/3 (dibagi {$count_cucu_pr})";  // CC04
                    }
                }
            }
            
            // --- CUCU LAKI-LAKI ---
            elseif ($hub == 'cucu laki-laki') {
                if ($has_anak_lk) {
                    $label = "Terhijab";  // CC01
                } elseif ($has_anak) {  // Ada anak perempuan
                    $label = !$has_anak_lk ? "Ashobah" : "Terhijab";
                } else {  // Tidak ada anak sama sekali
                    $label = "Ashobah Binafsihi";  // CC02
                }
            }
            
            // --- KAKEK ---
            elseif ($hub == 'kakek') {
                if ($has_bapak) {
                    $label = "Terhijab";  // KK01
                } else {
                    // Cek apakah hanya kakek dan saudara saja
                    $is_only_kakek_and_siblings = (
                        $has_kakek &&
                        ($has_sdr_kand || $has_sdr_sebpk) &&
                        !$has_anak && !$has_cucu &&
                        !$has_bapak && !$has_ibu &&
                        !$has_suami && !$has_istri
                    );
                    
                    if ($is_only_kakek_and_siblings) {
                        // Rule khusus kakek dengan saudara
                        // Hitung semua opsi untuk kakek
                        
                        // Opsi 1: 1/6 + Ashobah (mendapat semua sisa)
                        $opsi_ashobah = $total_harta;  // Mendapat semua sebagai ashobah
                        
                        // Opsi 2: 1/3 dari pokok harta
                        $opsi_sepertiga = $total_harta * (1/3);
                        
                        // Opsi 3: Muqossamah (kakek sebagai bapak, saudara sebagai anak)
                        $total_saudara_lk = $count_sdr_kand_lk + $count_sdr_sebpk_lk;
                        $total_saudara_pr = $count_sdr_kand_pr + $count_sdr_sebpk_pr;
                        $total_kepala_saudara = $total_saudara_lk * 2 + $total_saudara_pr;
                        
                        if ($total_kepala_saudara > 0) {
                            $opsi_muqossamah = $total_harta * (1 / (1 + $total_kepala_saudara));
                        } else {
                            $opsi_muqossamah = $total_harta;
                        }
                        
                        // Pilih yang paling menguntungkan untuk kakek
                        if ($opsi_ashobah >= $opsi_sepertiga && $opsi_ashobah >= $opsi_muqossamah) {
                            $porsi = 1/6;
                            $label = "1/6 + Ashobah";
                            $pilihan_kakek = 'ashobah';
                            $bagian_kakek_terpilih = $opsi_ashobah;
                        } elseif ($opsi_sepertiga >= $opsi_muqossamah) {
                            $porsi = 1/3;
                            $label = "1/3 pokok";
                            $pilihan_kakek = 'sepertiga';
                            $bagian_kakek_terpilih = $opsi_sepertiga;
                        } else {
                            $bagian_kakek_muq = 1 / (1 + $total_kepala_saudara);
                            $porsi = $bagian_kakek_muq;
                            $label = "Muqossamah (1:{$total_kepala_saudara})";
                            $pilihan_kakek = 'muqossamah';
                            $bagian_kakek_terpilih = $opsi_muqossamah;
                        }
                    } elseif ($has_anak_lk || $has_cucu_lk) {
                        $porsi = 1/6;
                        $label = "1/6";  // KK02
                    } else {
                        $porsi = 1/6;
                        $label = "1/6 + Ashobah";  // KK03
                    }
                }
            }
            
            // --- NENEK ---
            elseif ($hub == 'nenek pihak bapak') {
                if ($has_ibu || $has_bapak) {
                    $label = "Terhijab";  // NE01 (implisit)
                } else {
                    $porsi_per_orang = ($count_nenek > 0) ? (1/6) / $count_nenek : 1/6;
                    $porsi = $porsi_per_orang;
                    $label = "1/6";  // NE02
                }
            } elseif ($hub == 'nenek pihak ibu') {
                if ($has_ibu) {
                    $label = "Terhijab";  // NE02
                } else {
                    $porsi_per_orang = ($count_nenek > 0) ? (1/6) / $count_nenek : 1/6;
                    $porsi = $porsi_per_orang;
                    $label = "1/6";  // NE02
                }
            }
            
            // --- SAUDARA SEIBU ---
            elseif ($hub == 'saudara seibu') {
                if ($has_anak || $has_cucu || $has_bapak || $has_kakek) {
                    $label = "Terhijab";  // SB01
                } else {
                    if ($count_sdr_seibu == 1) {
                        $porsi = 1/6;
                        $label = "1/6";  // SB02
                    } elseif ($count_sdr_seibu >= 2) {
                        $porsi_per_orang = (1/3) / $count_sdr_seibu;
                        $porsi = $porsi_per_orang;
                        $label = "1/3 (dibagi {$count_sdr_seibu})";  // SB03
                    }
                }
            }
            
            // --- SAUDARA PEREMPUAN KANDUNG ---
            elseif ($hub == 'saudara perempuan sekandung') {
                if ($has_anak_lk || $has_cucu_lk || $has_bapak) {
                    $label = "Terhijab";
                } elseif ($has_anak_pr || $has_cucu_pr) {
                    // Ada anak/cucu perempuan tapi tidak ada laki → Ashobah Ma'al Ghoiri
                    $label = "Ashobah Ma'al Ghoiri";
                } elseif (!$has_anak && !$has_cucu) {
                    if ($count_sdr_kand_pr == 1 && $count_sdr_kand_lk == 0) {
                        $porsi = 0.5;
                        $label = "1/2";
                    } elseif ($count_sdr_kand_pr >= 2 && $count_sdr_kand_lk == 0) {
                        $porsi_per_orang = (2/3) / $count_sdr_kand_pr;
                        $porsi = $porsi_per_orang;
                        $label = "2/3 (dibagi {$count_sdr_kand_pr})";
                    } elseif ($count_sdr_kand_lk > 0) {
                        // Ada saudara laki kandung → Ashobah Bil Ghoiri (ditangani di Tahap 2)
                        $label = "Ashobah Bil Ghoiri";
                    }
                }
            }
            
            // --- SAUDARA LAKI-LAKI KANDUNG ---
            elseif ($hub == 'saudara laki-laki sekandung') {
                if ($has_anak_lk || $has_cucu_lk || $has_bapak) {
                    $label = "Terhijab";  // SK01
                } else {
                    $label = "Ashobah Binafsihi";  // SK05
                }
            }
            
            // --- SAUDARA SEBAPAK ---
            elseif (in_array($hub, ['saudara perempuan sebapak', 'saudara laki-laki sebapak'])) {
                if ($has_anak_lk || $has_cucu_lk || $has_bapak || $count_sdr_kand_lk > 0) {
                    $label = "Terhijab";
                } elseif ($has_kakek && !$has_bapak && !$has_anak && !$has_cucu) {
                    $label = "Terhijab";
                } elseif ($has_anak_pr || $has_cucu_pr) {
                    // Ada anak/cucu perempuan tapi tidak ada laki → Ashobah Ma'al Ghoiri
                    // Hanya berlaku untuk saudara perempuan sebapak
                    if ($hub == 'saudara perempuan sebapak') {
                        $label = "Ashobah Ma'al Ghoiri";
                    } else {
                        // Saudara laki-laki sebapak tetap terhijab oleh anak perempuan
                        $label = "Terhijab";
                    }
                } else {
                    if ($hub == 'saudara perempuan sebapak') {
                        if ($count_sdr_sebpk_pr == 1 && $count_sdr_sebpk_lk == 0) {
                            if ($count_sdr_kand_pr == 1) {
                                $porsi = 1/6;
                                $label = "1/6 (Takmilah)";
                            } elseif ($count_sdr_kand_pr >= 2) {
                                $label = "Terhijab";
                            } else {
                                $porsi = 0.5;
                                $label = "1/2";
                            }
                        } elseif ($count_sdr_sebpk_pr >= 2 && $count_sdr_sebpk_lk == 0) {
                            if ($count_sdr_kand_pr == 1) {
                                $porsi_per_orang = (2/3 - 0.5) / $count_sdr_sebpk_pr;
                                $porsi = $porsi_per_orang;
                                $label = "1/6 (Takmilah, dibagi {$count_sdr_sebpk_pr})";
                            } elseif ($count_sdr_kand_pr >= 2) {
                                $label = "Terhijab";
                            } else {
                                $porsi_per_orang = (2/3) / $count_sdr_sebpk_pr;
                                $porsi = $porsi_per_orang;
                                $label = "2/3 (dibagi {$count_sdr_sebpk_pr})";
                            }
                        } elseif ($count_sdr_sebpk_lk > 0) {
                            $label = "Ashobah Bil Ghoiri";
                        } else {
                            $label = "Ashobah";
                        }
                    } elseif ($hub == 'saudara laki-laki sebapak') {
                        $label = "Ashobah";
                    }
                }
            }
            
            // Simpan data sementara
            if (!isset($data_sementara[$hub])) {
                $data_sementara[$hub] = [
                    "porsi_awal" => $porsi,
                    "label_awal" => $label,
                    "count" => 1
                ];
            } else {
                $data_sementara[$hub]["count"]++;
            }
        }
        
        // Konversi data sementara ke hasil_faraidh
        foreach ($data_sementara as $hub => $data) {
            $count = $data["count"];
            $porsi_per_orang = $data["porsi_awal"];
            $label = $data["label_awal"];
            
            if ($porsi_per_orang > 0) {  // Ada bagian tetap
                if (strpos($label, "dibagi") !== false) {  // Sudah porsi per orang
                    $nominal_per_orang = $total_harta * $porsi_per_orang;
                } else {  // Porsi total untuk semua orang dengan hubungan ini
                    $nominal_total = $total_harta * $porsi_per_orang * $count;
                    $nominal_per_orang = ($count > 0) ? $nominal_total / $count : 0;
                }
            } else {  // Tidak ada bagian tetap
                $nominal_per_orang = 0;
            }
            
            for ($i = 0; $i < $count; $i++) {
                $hasil_faraidh[] = [
                    "hubungan" => $hub,
                    "label" => $label,
                    "nominal" => $nominal_per_orang
                ];
            }
        }
        
        // --- TAHAP 2: ASHOBAH ---
        // Hitung total bagian tetap yang sudah dibagikan
        $total_bagian_tetap = array_sum(array_column($hasil_faraidh, 'nominal'));
        $sisa_untuk_ashobah = $total_harta - $total_bagian_tetap;
        
        if ($pilihan_kakek == 'ashobah') {
            // Kakek dapat semua, saudara tidak dapat
            foreach ($hasil_faraidh as &$r) {
                if ($r['hubungan'] == 'kakek') {
                    $r['nominal'] = $total_harta;
                    $r['label'] = "1/6 + Ashobah (terpilih)";
                } elseif (in_array($r['hubungan'], ['saudara laki-laki sekandung', 'saudara perempuan sekandung',
                          'saudara laki-laki sebapak', 'saudara perempuan sebapak'])) {
                    $r['nominal'] = 0;
                    if ($r['label'] != "Terhijab") {
                        $r['label'] = "Terhijab oleh kakek";
                    }
                }
            }
            // Set sisa untuk ashobah = 0 karena sudah habis
            $sisa_untuk_ashobah = 0;
        } elseif ($pilihan_kakek == 'sepertiga') {
            // Kakek dapat 1/3, sisa untuk saudara (ashobah)
            $bagian_kakek = $total_harta * (1/3);
            $sisa_untuk_saudara = $total_harta - $bagian_kakek;
            
            foreach ($hasil_faraidh as &$r) {
                if ($r['hubungan'] == 'kakek') {
                    $r['nominal'] = $bagian_kakek;
                    $r['label'] = "1/3 pokok (terpilih)";
                }
            }
            
            // Bagikan sisa ke saudara
            $total_saudara_lk = $count_sdr_kand_lk + $count_sdr_sebpk_lk;
            $total_saudara_pr = $count_sdr_kand_pr + $count_sdr_sebpk_pr;
            
            if ($total_saudara_lk > 0) {
                if ($total_saudara_pr > 0) {
                    // Ashobah bil ghoiri
                    $total_kepala = $total_saudara_lk * 2 + $total_saudara_pr;
                    $nilai_per_kepala = ($total_kepala > 0) ? $sisa_untuk_saudara / $total_kepala : 0;
                    
                    foreach ($hasil_faraidh as &$r) {
                        if (strpos($r['hubungan'], 'saudara laki-laki') !== false && 
                            (strpos($r['hubungan'], 'sekandung') !== false || strpos($r['hubungan'], 'sebapak') !== false)) {
                            $r['nominal'] += $nilai_per_kepala * 2;
                            $r['label'] = "Ashobah Bil Ghoiri (2 bagian)";
                        } elseif (strpos($r['hubungan'], 'saudara perempuan') !== false && 
                                 (strpos($r['hubungan'], 'sekandung') !== false || strpos($r['hubungan'], 'sebapak') !== false)) {
                            $r['nominal'] += $nilai_per_kepala * 1;
                            $r['label'] = "Ashobah Bil Ghoiri (1 bagian)";
                        }
                    }
                } else {
                    // Ashobah binafsihi untuk saudara laki-laki
                    $nilai_per_orang = ($total_saudara_lk > 0) ? $sisa_untuk_saudara / $total_saudara_lk : 0;
                    foreach ($hasil_faraidh as &$r) {
                        if (strpos($r['hubungan'], 'saudara laki-laki') !== false && 
                            (strpos($r['hubungan'], 'sekandung') !== false || strpos($r['hubungan'], 'sebapak') !== false)) {
                            $r['nominal'] += $nilai_per_orang;
                            $r['label'] = "Ashobah Binafsihi";
                        }
                    }
                }
            } elseif ($total_saudara_pr > 0) {
                // Hanya saudara perempuan
                $nilai_per_orang = ($total_saudara_pr > 0) ? $sisa_untuk_saudara / $total_saudara_pr : 0;
                foreach ($hasil_faraidh as &$r) {
                    if (strpos($r['hubungan'], 'saudara perempuan') !== false && 
                        (strpos($r['hubungan'], 'sekandung') !== false || strpos($r['hubungan'], 'sebapak') !== false)) {
                        $r['nominal'] += $nilai_per_orang;
                        $r['label'] = "Ashobah";
                    }
                }
            }
            // Set sisa untuk ashobah = 0 karena sudah habis
            $sisa_untuk_ashobah = 0;
        } elseif ($pilihan_kakek == 'muqossamah') {
            // Muqossamah: kakek 1 bagian, saudara sebagai anak
            $total_saudara_lk = $count_sdr_kand_lk + $count_sdr_sebpk_lk;
            $total_saudara_pr = $count_sdr_kand_pr + $count_sdr_sebpk_pr;
            $total_kepala_saudara = $total_saudara_lk * 2 + $total_saudara_pr;
            
            if ($total_kepala_saudara > 0) {
                $bagian_kakek = $total_harta * (1 / (1 + $total_kepala_saudara));
                $sisa_untuk_saudara = $total_harta - $bagian_kakek;
                
                foreach ($hasil_faraidh as &$r) {
                    if ($r['hubungan'] == 'kakek') {
                        $r['nominal'] = $bagian_kakek;
                        $r['label'] = "Muqossamah (1:{$total_kepala_saudara}, terpilih)";
                    }
                }
                
                // Bagikan ke saudara berdasarkan kepala
                $nilai_per_kepala = ($total_kepala_saudara > 0) ? $sisa_untuk_saudara / $total_kepala_saudara : 0;
                
                foreach ($hasil_faraidh as &$r) {
                    if (strpos($r['hubungan'], 'saudara laki-laki') !== false && 
                        (strpos($r['hubungan'], 'sekandung') !== false || strpos($r['hubungan'], 'sebapak') !== false)) {
                        $r['nominal'] += $nilai_per_kepala * 2;
                        $r['label'] = "Muqossamah (2 bagian, 1:{$total_kepala_saudara})";
                    } elseif (strpos($r['hubungan'], 'saudara perempuan') !== false && 
                             (strpos($r['hubungan'], 'sekandung') !== false || strpos($r['hubungan'], 'sebapak') !== false)) {
                        $r['nominal'] += $nilai_per_kepala * 1;
                        $r['label'] = "Muqossamah (1 bagian, 1:{$total_kepala_saudara})";
                    }
                }
            } else {
                // Tidak ada saudara, kakek dapat semua
                foreach ($hasil_faraidh as &$r) {
                    if ($r['hubungan'] == 'kakek') {
                        $r['nominal'] = $total_harta;
                        $r['label'] = "1/6 + Ashobah";
                    }
                }
            }
            // Set sisa untuk ashobah = 0 karena sudah habis
            $sisa_untuk_ashobah = 0;
        }
        
        // Prioritas ashobah berdasarkan hirarki:
        // 1. Anak laki-laki (dengan anak perempuan bil ghoiri)
        // 2. Cucu laki-laki (jika tidak ada anak)
        // 3. Bapak (jika tidak ada anak/cucu laki)
        // 4. Kakek (jika tidak ada bapak)
        // 5. Saudara kandung
        // 6. Saudara sebapak
        
        // 1. ANAK LAKI-LAKI & PEREMPUAN (Ashobah Bil Ghoiri atau Binafsihi)
        if ($has_anak_lk && $sisa_untuk_ashobah > 0) {
            if ($has_anak_pr) {  // Ada anak perempuan (Ashobah Bil Ghoiri)
                $total_kepala = ($count_anak_lk * 2) + $count_anak_pr;
                $nilai_per_kepala = ($total_kepala > 0) ? $sisa_untuk_ashobah / $total_kepala : 0;
                
                // Update anak laki-laki
                foreach ($hasil_faraidh as &$res) {
                    if ($res['hubungan'] == 'anak laki-laki') {
                        $res['nominal'] += $nilai_per_kepala * 2;
                        $res['label'] = "Ashobah Bil Ghoiri (2 bagian)";
                    }
                }
                
                // Update anak perempuan
                foreach ($hasil_faraidh as &$res) {
                    if ($res['hubungan'] == 'anak perempuan') {
                        $res['nominal'] += $nilai_per_kepala * 1;
                        $res['label'] = "Ashobah Bil Ghoiri (1 bagian)";
                    }
                }
            } else {  // Hanya anak laki-laki saja (Ashobah Binafsihi)
                $nilai_per_orang = ($count_anak_lk > 0) ? $sisa_untuk_ashobah / $count_anak_lk : 0;
                
                // Update semua anak laki-laki
                foreach ($hasil_faraidh as &$res) {
                    if ($res['hubungan'] == 'anak laki-laki') {
                        $res['nominal'] += $nilai_per_orang;
                        $res['label'] = "Ashobah Binafsihi";
                    }
                }
            }
            $sisa_untuk_ashobah = 0;
        }
        
        // 2. CUCU LAKI-LAKI & PEREMPUAN (jika tidak ada anak)
        elseif (!$has_anak && $has_cucu_lk && $sisa_untuk_ashobah > 0) {
            if ($has_cucu_pr) {  // Ada cucu perempuan
                $total_kepala = ($count_cucu_lk * 2) + $count_cucu_pr;
                $nilai_per_kepala = ($total_kepala > 0) ? $sisa_untuk_ashobah / $total_kepala : 0;
                
                foreach ($hasil_faraidh as &$res) {
                    if ($res['hubungan'] == 'cucu laki-laki') {
                        $res['nominal'] += $nilai_per_kepala * 2;
                        $res['label'] = "Ashobah Bil Ghoiri (2 bagian)";
                    } elseif ($res['hubungan'] == 'cucu perempuan') {
                        $res['nominal'] += $nilai_per_kepala * 1;
                        $res['label'] = "Ashobah Bil Ghoiri (1 bagian)";
                    }
                }
            } else {  // Hanya cucu laki-laki saja
                $nilai_per_orang = ($count_cucu_lk > 0) ? $sisa_untuk_ashobah / $count_cucu_lk : 0;
                
                foreach ($hasil_faraidh as &$res) {
                    if ($res['hubungan'] == 'cucu laki-laki') {
                        $res['nominal'] += $nilai_per_orang;
                        $res['label'] = "Ashobah Binafsihi";
                    }
                }
            }
            $sisa_untuk_ashobah = 0;
        }
        
        // 3. BAPAK (1/6 + Ashobah)
        elseif ($has_bapak && $sisa_untuk_ashobah > 0) {
            // 1/6 bapak sudah dihitung di Tahap 1
            foreach ($hasil_faraidh as &$res) {
                if ($res['hubungan'] == 'bapak') {
                    $res['nominal'] += $sisa_untuk_ashobah;
                    $res['label'] = "1/6 + Ashobah";
                    break;
                }
            }
            $sisa_untuk_ashobah = 0;
        }
        
        // 4. KAKEK (1/6 + Ashobah jika tidak ada bapak)
        elseif ($has_kakek && !$has_bapak && $sisa_untuk_ashobah > 0) {
            foreach ($hasil_faraidh as &$res) {
                if ($res['hubungan'] == 'kakek') {
                    $res['nominal'] += $sisa_untuk_ashobah;
                    $res['label'] = "1/6 + Ashobah";
                    break;
                }
            }
            $sisa_untuk_ashobah = 0;
        }
        
        // 5. SAUDARA KANDUNG (Ashobah)
        elseif ($has_sdr_kand && !($has_anak_lk || $has_cucu_lk || $has_bapak) && $sisa_untuk_ashobah > 0) {
            if ($count_sdr_kand_lk > 0) {  // Ada saudara laki-laki kandung
                if ($count_sdr_kand_pr > 0) {  // Ada saudara perempuan kandung (Ashobah Bil Ghoiri)
                    $total_kepala = ($count_sdr_kand_lk * 2) + $count_sdr_kand_pr;
                    $nilai_per_kepala = ($total_kepala > 0) ? $sisa_untuk_ashobah / $total_kepala : 0;
                    
                    foreach ($hasil_faraidh as &$res) {
                        if ($res['hubungan'] == 'saudara laki-laki sekandung') {
                            $res['nominal'] += $nilai_per_kepala * 2;
                            $res['label'] = "Ashobah Bil Ghoiri (2 bagian)";
                        } elseif ($res['hubungan'] == 'saudara perempuan sekandung') {
                            $res['nominal'] += $nilai_per_kepala * 1;
                            $res['label'] = "Ashobah Bil Ghoiri (1 bagian)";
                        }
                    }
                } else {  // Hanya saudara laki-laki kandung saja
                    $nilai_per_orang = ($count_sdr_kand_lk > 0) ? $sisa_untuk_ashobah / $count_sdr_kand_lk : 0;
                    
                    foreach ($hasil_faraidh as &$res) {
                        if ($res['hubungan'] == 'saudara laki-laki sekandung') {
                            $res['nominal'] += $nilai_per_orang;
                            $res['label'] = "Ashobah Binafsihi";
                        }
                    }
                }
            } elseif ($count_sdr_kand_pr > 0) {  // Hanya saudara perempuan kandung (Ashobah Bil Ghoiri khusus)
                // Saudara perempuan kandung saja (tanpa laki-laki) akan dapat 1/2 atau 2/3 (sudah dihitung di bagian tetap)
                // Tidak perlu melakukan apa-apa
            }
            $sisa_untuk_ashobah = 0;
        }
        
        // 6. SAUDARA SEBAPAK (jika tidak ada saudara kandung)
        elseif ($has_sdr_sebpk && !$has_sdr_kand && !($has_anak_lk || $has_cucu_lk || $has_bapak) && $sisa_untuk_ashobah > 0) {
            if ($count_sdr_sebpk_lk > 0) {  // Ada saudara laki-laki sebapak
                if ($count_sdr_sebpk_pr > 0) {  // Ada saudara perempuan sebapak
                    $total_kepala = ($count_sdr_sebpk_lk * 2) + $count_sdr_sebpk_pr;
                    $nilai_per_kepala = ($total_kepala > 0) ? $sisa_untuk_ashobah / $total_kepala : 0;
                    
                    foreach ($hasil_faraidh as &$res) {
                        if ($res['hubungan'] == 'saudara laki-laki sebapak') {
                            $res['nominal'] += $nilai_per_kepala * 2;
                            $res['label'] = "Ashobah Bil Ghoiri (2 bagian)";
                        } elseif ($res['hubungan'] == 'saudara perempuan sebapak') {
                            $res['nominal'] += $nilai_per_kepala * 1;
                            $res['label'] = "Ashobah Bil Ghoiri (1 bagian)";
                        }
                    }
                } else {  // Hanya saudara laki-laki sebapak saja
                    $nilai_per_orang = ($count_sdr_sebpk_lk > 0) ? $sisa_untuk_ashobah / $count_sdr_sebpk_lk : 0;
                    
                    foreach ($hasil_faraidh as &$res) {
                        if ($res['hubungan'] == 'saudara laki-laki sebapak') {
                            $res['nominal'] += $nilai_per_orang;
                            $res['label'] = "Ashobah Binafsihi";
                        }
                    }
                }
            } elseif ($count_sdr_sebpk_pr > 0) {  // Hanya saudara perempuan sebapak
                // Saudara perempuan sebapak saja (tanpa laki-laki) akan dapat 1/2 atau 2/3
                // Tidak perlu melakukan apa-apa
            }
            $sisa_untuk_ashobah = 0;
        }

        // --- ASHOBAH MA'AL GHOIRI ---
        // Saudara perempuan kandung/sebapak yang berstatus Ashobah Ma'al Ghoiri
        // mendapat sisa harta setelah semua bagian tetap dibagikan
        $sisa_mal_ghoiri = $total_harta - array_sum(array_column($hasil_faraidh, 'nominal'));

        if ($sisa_mal_ghoiri > 0.01) { // toleransi floating point
            $indeks_mal_ghoiri = [];
            foreach ($hasil_faraidh as $idx => $r) {
                if ($r['label'] === "Ashobah Ma'al Ghoiri") {
                    $indeks_mal_ghoiri[] = $idx;
                }
            }
            
            if (count($indeks_mal_ghoiri) > 0) {
                $bagian_per_orang = $sisa_mal_ghoiri / count($indeks_mal_ghoiri);
                foreach ($indeks_mal_ghoiri as $idx) {
                    $hasil_faraidh[$idx]['nominal'] += $bagian_per_orang;
                }
            }
        }
        
        // --- TAHAP 3: RADD (Kembalian Sisa) ---
        // Hitung sisa setelah ashobah
        $total_sudah_dibagi = array_sum(array_column($hasil_faraidh, 'nominal'));
        $sisa_untuk_radd = $total_harta - $total_sudah_dibagi;
        
        // Cek apakah ada yang dapat ashobah
        $ada_ashobah = false;
        foreach ($hasil_faraidh as $r) {
            if (strpos($r['label'], 'Ashobah') !== false) {
                $ada_ashobah = true;
                break;
            }
        }
        
        if ($sisa_untuk_radd > 0 && !$ada_ashobah) {
            // Radd hanya untuk yang bukan suami/istri
            $ahli_waris_radd = [];
            foreach ($hasil_faraidh as $r) {
                if ($r['nominal'] > 0 && strpos($r['hubungan'], 'suami') === false && strpos($r['hubungan'], 'istri') === false) {
                    $ahli_waris_radd[] = $r;
                }
            }
            
            if (count($ahli_waris_radd) > 0) {
                $total_bagian_radd = array_sum(array_column($ahli_waris_radd, 'nominal'));
                
                foreach ($hasil_faraidh as &$r) {
                    if (strpos($r['hubungan'], 'suami') === false && strpos($r['hubungan'], 'istri') === false && $r['nominal'] > 0) {
                        $proporsi = $r['nominal'] / $total_bagian_radd;
                        $tambahan = $proporsi * $sisa_untuk_radd;
                        $r['nominal'] += $tambahan;
                        if (strpos($r['label'], 'Radd') === false) {
                            $r['label'] .= " + Radd";
                        }
                    }
                }
            }
        }
        
        // --- TAHAP 4: SISTEM 'AUL (Jika total bagian > 100%) ---
        $bagian_pecahan = [];
        
        // Kumpulkan semua pecahan
        foreach ($hasil_faraidh as $r) {
            if ($r['nominal'] > 0 && (strpos($r['label'], 'Ashobah') === false && strpos($r['label'], 'Terhijab') === false)) {
                list($pembilang, $penyebut) = self::label_ke_pecahan($r['label'], 1);
                if ($pembilang > 0) {
                    $bagian_pecahan[] = [$pembilang, $penyebut];
                }
            }
        }
        
        // Hitung total pecahan
        if (count($bagian_pecahan) > 0) {
            // Hitung KPK dari semua penyebut
            $penyebut_list = array_column($bagian_pecahan, 1);
            $kpk = 1;
            foreach ($penyebut_list as $penyebut) {
                $kpk = self::kpk($kpk, $penyebut);
            }
            
            // Hitung total pembilang setelah disamakan penyebutnya
            $total_pembilang = 0;
            foreach ($bagian_pecahan as $pecahan) {
                $pembilang = $pecahan[0];
                $penyebut = $pecahan[1];
                $total_pembilang += $pembilang * ($kpk / $penyebut);
            }
            
            // Jika total pembilang > KPK, maka perlu 'Aul
            if ($total_pembilang > $kpk) {
                // Terapkan 'Aul: naikkan asal masalah
                $asal_masalah_baru = $total_pembilang;
                
                // Reset semua nominal dan hitung ulang dengan 'Aul
                foreach ($hasil_faraidh as &$r) {
                    if ($r['nominal'] > 0 && (strpos($r['label'], 'Ashobah') === false && strpos($r['label'], 'Terhijab') === false)) {
                        list($pembilang, $penyebut) = self::label_ke_pecahan($r['label'], 1);
                        if ($pembilang > 0) {
                            // Hitung bagian dalam 'Aul
                            $faktor = $kpk / $penyebut;
                            $bagian_dalam_aul = ($pembilang * $faktor) / $asal_masalah_baru;
                            $r['nominal'] = $total_harta * $bagian_dalam_aul;
                            
                            // Update label untuk menunjukkan 'Aul
                            if (strpos($r['label'], "'Aul") === false) {
                                $r['label'] = str_replace(" + Radd", "", $r['label']) . " ('Aul)";
                            }
                        }
                    }
                }
                
                // Untuk ashobah, perlu penyesuaian juga
                foreach ($hasil_faraidh as &$r) {
                    if (strpos($r['label'], 'Ashobah') !== false && $r['nominal'] > 0) {
                        // Ashobah dalam 'Aul: sisa setelah bagian tetap
                        $total_bagian_tetap_aul = 0;
                        foreach ($hasil_faraidh as $r2) {
                            if (strpos($r2['label'], "'Aul") !== false && $r2['nominal'] > 0) {
                                $total_bagian_tetap_aul += $r2['nominal'];
                            }
                        }
                        $sisa_aul = $total_harta - $total_bagian_tetap_aul;
                        
                        if ($sisa_aul > 0 && $has_anak_lk) {
                            if ($has_anak_pr) {  // Ashobah Bil Ghoiri
                                $total_kepala = ($count_anak_lk * 2) + $count_anak_pr;
                                $nilai_per_kepala = ($total_kepala > 0) ? $sisa_aul / $total_kepala : 0;
                                
                                foreach ($hasil_faraidh as &$res) {
                                    if ($res['hubungan'] == 'anak laki-laki') {
                                        $res['nominal'] = $nilai_per_kepala * 2;
                                        $res['label'] = "Ashobah Bil Ghoiri ('Aul)";
                                    } elseif ($res['hubungan'] == 'anak perempuan') {
                                        $res['nominal'] = $nilai_per_kepala * 1;
                                        $res['label'] = "Ashobah Bil Ghoiri ('Aul)";
                                    }
                                }
                            } else {  // Ashobah Binafsihi
                                $nilai_per_orang = ($count_anak_lk > 0) ? $sisa_aul / $count_anak_lk : 0;
                                foreach ($hasil_faraidh as &$res) {
                                    if ($res['hubungan'] == 'anak laki-laki') {
                                        $res['nominal'] = $nilai_per_orang;
                                        $res['label'] = "Ashobah Binafsihi ('Aul)";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $hasil_faraidh;
    }
    
    // Helper functions
    private static function countOccurrences($array, $value)
    {
        $count = 0;
        foreach ($array as $item) {
            if ($item === $value) {
                $count++;
            }
        }
        return $count;
    }
    
    private static function label_ke_pecahan($label, $count = 1)
    {
        if (strpos($label, "1/2") !== false) {
            return [1, 2];
        } elseif (strpos($label, "1/4") !== false) {
            return [1, 4];
        } elseif (strpos($label, "1/8") !== false) {
            return [1, 8];
        } elseif (strpos($label, "1/3") !== false && strpos($label, "dibagi") === false) {
            return [1, 3];
        } elseif (strpos($label, "1/6") !== false && strpos($label, "dibagi") === false) {
            return [1, 6];
        } elseif (strpos($label, "2/3") !== false) {
            // Untuk 2/3 yang dibagi beberapa orang
            if (strpos($label, "dibagi") !== false) {
                // Cari angka pembagi
                preg_match('/dibagi\s*(\d+)/', $label, $matches);
                if (isset($matches[1])) {
                    $pembagi = intval($matches[1]);
                    // 2/3 dibagi n orang = (2/3) / n
                    return [2, 3 * $pembagi];
                }
            }
            return [2, 3];
        }
        return [0, 1];
    }
    
    private static function gcd($a, $b)
    {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }
        return $a;
    }
    
    private static function kpk($a, $b)
    {
        return ($a * $b) / self::gcd($a, $b);
    }
    
    // Public method untuk memanggil dari controller
    public static function calculate($harta_bersih, $daftar_ahli_waris)
    {
        return self::engine_faraidh_full($harta_bersih, $daftar_ahli_waris);
    }
}