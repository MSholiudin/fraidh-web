<?php

namespace App\Services;

/**
 * FaraidhDetailService
 *
 * Menghasilkan data detail perhitungan faraidh (asal masalah, tashih,
 * saham awal, saham akhir, saham per orang) dari hasil tampilan controller.
 *
 * Mampu menangani semua label yang diproduksi FaraidhCalculator:
 *   - Dzawil furudh murni          : 1/2, 1/3, 1/4, 1/6, 1/8, 2/3, beserta varian "dibagi N"
 *   - Campuran furudh + ashobah    : "1/6 + Ashobah Binafsihi" (bapak)
 *   - Gharawain                    : "1/3 sisa (Gharawain)" — nominal dari engine, saham proporsional
 *   - Ashobah (semua varian)       : Binafsihi, Bil Ghoiri, Ma'al Ghoiri, bersama Kakek, dll
 *   - Muqosamah / Kakek            : "Muqosamah (terpilih)", "1/3 sisa (terpilih)", "1/6 (minimal)"
 *   - Musytarakah                  : "Musytarakah (1/3 dibagi N)"
 *   - Radd → Baitul Maal           : "Sisa (Radd)" — saham proporsional dari nominal
 *   - Akdariyah                    : 3/9, 2/9, 8/27, 4/27
 *   - 'Aul                         : label apa pun dengan suffix "('Aul)"
 *   - Terhijab (semua varian)      : dikecualikan dari perhitungan
 */
class FaraidhDetailService
{
    // =========================================================
    // ENTRY POINT
    // =========================================================

    public function generate(array $hasil): array
    {
        // Deteksi kasus khusus DULU sebelum build items
        $adaAkdariyah  = false;
        $adaGharrawain = false;
        $adaMuqosamah  = false;

        foreach ($hasil as $h) {
            $b = $h['bagian'] ?? '';
            if (str_contains($b, 'Akdariyah'))                                          $adaAkdariyah  = true;
            if (str_contains(strtolower($b), 'gharawain') ||
                str_contains(strtolower($b), 'gharrawain'))                             $adaGharrawain = true;
            if (str_contains(strtolower($b), 'muqosamah'))                             $adaMuqosamah  = true;
        }

        $items = $this->buildItems($hasil, $adaAkdariyah, $adaGharrawain);

        $asalMasalah = $this->hitungAsalMasalah($items);
        $items       = $this->hitungSahamDzawilFurudh($items, $asalMasalah);

        [$totalSahamFurudh, $sisaSaham] = $this->hitungSisaSaham($items, $asalMasalah);

        // Step khusus Gharrawain: ibu dapat 1/3 dari sisa SEBELUM bapak dapat ashobah
        if ($adaGharrawain && $asalMasalah) {
            $sisaUntukGharrawain = $asalMasalah - $totalSahamFurudh;
            $sahamIbuGharrawain  = $sisaUntukGharrawain / 3;
            foreach ($items as $index => $item) {
                $lower = strtolower($item['bagian']);
                if (str_contains($lower, 'gharawain') || str_contains($lower, 'gharrawain')) {
                    $items[$index]['saham_awal'] = $sahamIbuGharrawain;
                }
            }
            // Recalculate sisa setelah ibu Gharrawain dikurangi
            [$totalSahamFurudh, $sisaSaham] = $this->hitungSisaSaham($items, $asalMasalah);
        }

        $items  = $this->hitungSahamAshobah($items, $sisaSaham);
        $items  = $this->hitungSahamProporsional($items, $asalMasalah, $hasil);
        $tashih = $this->hitungTashih($items, $asalMasalah);
        $items  = $this->hitungSahamAkhir($items, $asalMasalah, $tashih, $adaAkdariyah);

        // =============================================
        // Nilai 'Aul
        // =============================================
        $nilaiAul     = null;
        $nilaiAulTashih = null;

        if ($adaAkdariyah) {
            // Hard-coded: asal=6, aul=9, aul_tashih=27
            $nilaiAul       = 9;
            $nilaiAulTashih = 27;
        } else {
            // Deteksi 'Aul dari label
            $adaAul = false;
            foreach ($hasil as $h) {
                if (str_contains($h['bagian'] ?? '', "'Aul")) { $adaAul = true; break; }
            }
            if ($adaAul && $asalMasalah) {
                $totalPembilang = 0;
                foreach ($items as $item) {
                    if ($item['pembilang'] !== null && $item['penyebut'] !== null) {
                        $totalPembilang += ($item['pembilang'] * $asalMasalah) / $item['penyebut'];
                    }
                }
                $nilaiAul = (int) round($totalPembilang);
            }
        }

        return [
            'asal_masalah'  => $adaAkdariyah ? 6 : $asalMasalah,
            'tashih'        => ($tashih && $tashih != $asalMasalah && !$nilaiAul) ? $tashih : null,
            'aul'           => $nilaiAul,
            'aul_tashih'    => $nilaiAulTashih,
            'is_akdariyah'  => $adaAkdariyah,
            'is_gharrawain' => $adaGharrawain,
            'is_muqosamah'  => $adaMuqosamah,
            'items'         => $items,
        ];
    }

    // =========================================================
    // STEP 1: BUILD ITEMS
    // =========================================================

    private function buildItems(array $hasil, bool $adaAkdariyah = false, bool $adaGharrawain = false): array
    {
        // Mapping label Akdariyah ke pecahan furudh aslinya
        $akdariyahFurudh = [
            '1/2 (aul akdariyah)' => ['numerator' => 1, 'denominator' => 2], // suami & sdr pr
            '1/3 (aul akdariyah)' => ['numerator' => 1, 'denominator' => 3], // ibu
            '1/6 (aul akdariyah)' => ['numerator' => 1, 'denominator' => 6], // kakek
        ];

        $items = [];

        foreach ($hasil as $item) {
            if (($item['hubungan'] ?? '') === 'catatan') continue;

            $bagian = $item['bagian'] ?? '';

            if ($this->isTerhijab($bagian)) continue;

            // Override fraction untuk Akdariyah
            if ($adaAkdariyah) {
                $fraction = $akdariyahFurudh[strtolower($bagian)] ?? $this->parseFraction($bagian);
            }
            // Gharrawain: bapak "1/6 + Ashobah" diperlakukan murni ashobah (skip 1/6 dari KPK)
            elseif ($adaGharrawain && preg_match('/^\d+\/\d+\s*\+\s*ashobah/i', $bagian)) {
                $fraction = null;
            }
            else {
                $fraction = $this->parseFraction($bagian);
            }

            $items[] = [
                'hubungan'        => $item['hubungan'],
                'jumlah'          => $item['jumlah'] ?? 1,
                'bagian'          => $bagian,
                'nominal'         => $item['nominal'] ?? 0,
                'pembilang'       => $fraction['numerator']   ?? null,
                'penyebut'        => $fraction['denominator'] ?? null,
                'saham_awal'      => null,
                'saham_akhir'     => null,
                'saham_per_orang' => null,
                'total_kepala'    => 0,
            ];
        }

        return $items;
    }

    // =========================================================
    // STEP 2: HITUNG ASAL MASALAH
    // =========================================================

    private function hitungAsalMasalah(array $items): ?int
    {
        $penyebutList = [];

        foreach ($items as $item) {
            if ($item['penyebut'] !== null) {
                $penyebutList[] = $item['penyebut'];
            }
        }

        if (empty($penyebutList)) {
            return null;
        }

        return $this->lcmArray($penyebutList);
    }

    // =========================================================
    // STEP 3: SAHAM DZAWIL FURUDH
    // =========================================================

    private function hitungSahamDzawilFurudh(array $items, ?int $asalMasalah): array
    {
        if (! $asalMasalah) {
            return $items;
        }

        foreach ($items as $index => $item) {
            if ($item['pembilang'] === null || $item['penyebut'] === null) {
                continue;
            }

            $items[$index]['saham_awal'] =
                ($item['pembilang'] * $asalMasalah) / $item['penyebut'];
        }

        return $items;
    }

    // =========================================================
    // STEP 4: HITUNG SISA SAHAM
    // =========================================================

    private function hitungSisaSaham(array $items, ?int $asalMasalah): array
    {
        $totalSahamFurudh = 0;

        foreach ($items as $item) {
            $totalSahamFurudh += $item['saham_awal'] ?? 0;
        }

        $sisaSaham = $asalMasalah
            ? max(0, $asalMasalah - $totalSahamFurudh)
            : 0;

        return [$totalSahamFurudh, $sisaSaham];
    }

    // =========================================================
    // STEP 5: SAHAM ASHOBAH
    //
    // Dalam faraidh, seluruh grup ashobah diperlakukan sebagai
    // 1 blok yang mendapat sisaSaham penuh. Pembagian antar individu
    // (rasio 2:1 laki:perempuan) diselesaikan lewat tashih.
    //
    // saham_awal setiap grup = sisaSaham (ditampilkan 1 kolom/rowspan)
    // total_kepala per grup  = bobot × jumlah_orang (untuk tashih)
    // =========================================================

    private function hitungSahamAshobah(array $items, float $sisaSaham): array
    {
        if ($sisaSaham <= 0) {
            return $items;
        }

        // Cek ada ashobah tidak
        $adaAshobah = false;
        foreach ($items as $item) {
            if ($this->parseAshobahWeight($item['bagian']) !== null) {
                $adaAshobah = true;
                break;
            }
        }
        if (! $adaAshobah) {
            return $items;
        }

        foreach ($items as $index => $item) {
            $bobot = $this->parseAshobahWeight($item['bagian']);
            if ($bobot === null) {
                continue;
            }

            // Setiap grup ashobah saham_awal-nya = sisaSaham penuh
            // (baris di tabel akan di-rowspan, jadi 1 nilai untuk semua grup)
            if ($items[$index]['saham_awal'] !== null) {
                // "1/6 + Ashobah" (bapak): tambahkan sisa di atas furudh
                $items[$index]['saham_awal'] += $sisaSaham;
            } else {
                $items[$index]['saham_awal'] = $sisaSaham;
            }

            // total_kepala per grup: bobot × jumlah_orang
            // Anak Lk (bobot 2, 1 orang) → 2 kepala
            // Anak Pr (bobot 1, 2 orang) → 2 kepala
            $items[$index]['total_kepala'] = $bobot * $item['jumlah'];
        }

        return $items;
    }

    // =========================================================
    // STEP 6: SAHAM PROPORSIONAL
    // Untuk label yang tidak bisa di-parse sebagai pecahan/ashobah:
    // Gharawain, Muqosamah, Radd/Baitul Maal
    // Saham dihitung proporsional dari nominal terhadap total nominal
    // =========================================================

    private function hitungSahamProporsional(
        array $items,
        ?int $asalMasalah,
        array $hasilAsli
    ): array {
        if (! $asalMasalah) {
            return $items;
        }

        // Hitung total nominal dari hasil asli (selain catatan & terhijab)
        $totalNominal = 0;
        foreach ($hasilAsli as $item) {
            if (($item['hubungan'] ?? '') === 'catatan') {
                continue;
            }
            if ($this->isTerhijab($item['bagian'] ?? '')) {
                continue;
            }
            $totalNominal += $item['nominal'] ?? 0;
        }

        if ($totalNominal <= 0) {
            return $items;
        }

        foreach ($items as $index => $item) {
            // Hanya proses item yang saham_awal-nya masih null
            if ($items[$index]['saham_awal'] !== null) {
                continue;
            }

            if (! $this->isLabelProporsional($item['bagian'])) {
                continue;
            }

            if (($item['nominal'] ?? 0) <= 0) {
                continue;
            }

            // Proporsional: (nominal_item / total_nominal) × asal_masalah
            $items[$index]['saham_awal'] =
                ($item['nominal'] / $totalNominal) * $asalMasalah;
        }

        return $items;
    }

    // =========================================================
    // STEP 7: TASHIH
    //
    // Tashih diperlukan ketika:
    // (a) Saham furudh suatu item tidak habis dibagi jumlah orangnya, ATAU
    // (b) Sisa ashobah tidak habis dibagi total kepala ashobah
    //     (total kepala = Σ bobot × jumlah_orang di semua grup ashobah)
    // =========================================================

    private function hitungTashih(array $items, ?int $asalMasalah): ?int
    {
        if (! $asalMasalah) {
            return null;
        }

        $tashih = $asalMasalah;

        // (a) Cek furudh: saham_awal tiap item vs jumlah orangnya
        foreach ($items as $item) {
            if ($item['saham_awal'] === null) continue;
            if ($this->parseAshobahWeight($item['bagian']) !== null) continue; // skip ashobah, dicek di (b)
            if ($item['jumlah'] <= 1) continue;

            if (fmod(round($item['saham_awal'], 6), $item['jumlah']) != 0) {
                $tashih *= $item['jumlah'];
            }
        }

        // (b) Cek ashobah: sisa ashobah vs total kepala
        // Ambil sisa ashobah = total saham_awal semua grup ashobah (sudah proporsional)
        // Total kepala = Σ total_kepala tiap grup ashobah
        $totalKepalaAshobah = 0;
        $sisaAshobah        = 0;

        foreach ($items as $item) {
            if ($this->parseAshobahWeight($item['bagian']) === null) continue;
            $totalKepalaAshobah += $item['total_kepala'] ?? ($item['jumlah'] ?? 1);
            $sisaAshobah        += $item['saham_awal'] ?? 0;
        }

        if ($totalKepalaAshobah > 1 && $sisaAshobah > 0) {
            if (fmod(round($sisaAshobah, 6), $totalKepalaAshobah) != 0) {
                $tashih *= $totalKepalaAshobah;
            }
        }

        return $tashih;
    }

    // =========================================================
    // STEP 8: SAHAM AKHIR & PER ORANG
    //
    // Furudh  : saham_akhir = saham_awal × faktor, per_orang = akhir / jumlah
    // Ashobah : saham_akhir = total_kepala_grup (bagian grup dari tashih)
    //           per_orang   = bobot × (tashih / total_kepala_semua_ashobah)
    // =========================================================

    private function hitungSahamAkhir(
        array $items,
        ?int $asalMasalah,
        ?int $tashih,
        bool $adaAkdariyah = false
    ): array {
        // Akdariyah: saham akhir sudah hard-coded dari engine (9,6,8,4 dalam aul_tashih=27)
        // Mapping: furudh asli → saham akhir dalam 27
        if ($adaAkdariyah) {
            $akdariyahSahamAkhir = [
                '1/2 (aul akdariyah)' => null, // dihandle per hubungan di bawah
                '1/3 (aul akdariyah)' => 6,
                '1/6 (aul akdariyah)' => 8,
            ];
            // Suami=9, Ibu=6, Kakek=8, Sdr Pr=4 — hard-coded dari engine
            $sahamAkhirPerHubungan = [
                'suami'   => 9,
                'ibu'     => 6,
                'kakek'   => 8,
            ];
            foreach ($items as $index => $item) {
                $lower = strtolower($item['bagian']);
                if (!str_contains($lower, 'aul akdariyah')) continue;

                $hub = strtolower($item['hubungan']);
                if (isset($sahamAkhirPerHubungan[$hub])) {
                    $akhir = $sahamAkhirPerHubungan[$hub];
                } else {
                    // Saudari perempuan
                    $akhir = 4;
                }
                $items[$index]['saham_akhir']     = $akhir;
                $items[$index]['saham_per_orang'] = $akhir / $item['jumlah'];
            }
            return $items;
        }

        if (! $asalMasalah || ! $tashih) {
            return $items;
        }

        $faktor = $tashih / $asalMasalah;

        // Hitung total kepala semua grup ashobah
        $totalKepalaAshobah = 0;
        foreach ($items as $item) {
            if ($this->parseAshobahWeight($item['bagian']) !== null) {
                $totalKepalaAshobah += $item['total_kepala'] ?? 0;
            }
        }

        // Sisa saham dalam tashih = tashih - saham furudh setelah tashih
        $sahamFurudhTashih = 0;
        foreach ($items as $item) {
            if ($item['saham_awal'] === null) continue;
            if ($this->parseAshobahWeight($item['bagian']) !== null) continue;
            $sahamFurudhTashih += $item['saham_awal'] * $faktor;
        }
        $sisaTashih = $tashih - $sahamFurudhTashih;

        foreach ($items as $index => $item) {
            if ($item['saham_awal'] === null) continue;

            $bobot = $this->parseAshobahWeight($item['bagian']);

            if ($bobot === null) {
                $sahamAkhir = $item['saham_awal'] * $faktor;
                $items[$index]['saham_akhir']     = $sahamAkhir;
                $items[$index]['saham_per_orang'] = $sahamAkhir / $item['jumlah'];
            } else {
                $totalKepalaGrup = $item['total_kepala'] ?? ($bobot * $item['jumlah']);
                $sahamAkhirGrup  = $totalKepalaAshobah > 0
                    ? ($totalKepalaGrup / $totalKepalaAshobah) * $sisaTashih
                    : 0;
                $items[$index]['saham_akhir']     = $sahamAkhirGrup;
                $items[$index]['saham_per_orang'] = $item['jumlah'] > 0
                    ? $sahamAkhirGrup / $item['jumlah']
                    : 0;
            }
        }

        return $items;
    }

    // =========================================================
    // CLASSIFIER: TERHIJAB
    // =========================================================

    private function isTerhijab(string $bagian): bool
    {
        $lower = strtolower($bagian);
        return str_contains($lower, 'terhijab');
    }

    // =========================================================
    // CLASSIFIER: LABEL PROPORSIONAL
    // Label yang tidak bisa di-parse sebagai pecahan/ashobah,
    // tapi nominalnya sudah benar dari engine → saham proporsional
    // =========================================================

    private function isLabelProporsional(string $bagian): bool
    {
        $lower = strtolower($bagian);

        return str_contains($lower, 'gharawain')
            || str_contains($lower, 'gharrwain')    // typo fallback
            || str_contains($lower, 'muqosamah')
            || str_contains($lower, 'radd')
            || str_contains($lower, 'sisa (radd)')
            || str_contains($lower, '1/3 sisa (terpilih)')
            || str_contains($lower, '1/6 (minimal)')
            || str_contains($lower, 'muqosamah = 1/3 sisa');
    }

    // =========================================================
    // PARSER: PECAHAN DARI LABEL
    // Mengembalikan [numerator, denominator] atau null
    // Hanya untuk label dzawil furudh murni
    // =========================================================

    private function parseFraction(string $bagian): ?array
    {
        // Skip label proporsional (tidak masuk asal masalah berbasis KPK)
        if ($this->isLabelProporsional($bagian)) {
            return null;
        }

        // Skip terhijab
        if ($this->isTerhijab($bagian)) {
            return null;
        }

        // PRIORITAS: "1/6 + Ashobah ..." → ambil HANYA pecahan awal (1/6)
        // Harus dicek SEBELUM parseAshobahWeight karena label ini mengandung
        // kata "ashobah" namun bagian furudhnya (1/6) tetap masuk asal masalah
        if (preg_match('/^(\d+)\/(\d+)\s*\+\s*ashobah/i', $bagian, $matches)) {
            return [
                'numerator'   => (int) $matches[1],
                'denominator' => (int) $matches[2],
            ];
        }

        // Skip ashobah murni (setelah pengecekan campuran di atas)
        if ($this->parseAshobahWeight($bagian) !== null) {
            return null;
        }

        // Standar: pola \d+/\d+ di mana saja dalam string
        if (preg_match('/(\d+)\/(\d+)/', $bagian, $matches)) {
            return [
                'numerator'   => (int) $matches[1],
                'denominator' => (int) $matches[2],
            ];
        }

        return null;
    }

    // =========================================================
    // PARSER: BOBOT ASHOBAH
    // Mengembalikan bobot (1 atau 2) atau null jika bukan ashobah
    // =========================================================

    private function parseAshobahWeight(string $bagian): ?int
    {
        // Ashobah dengan bobot eksplisit: "(2 bagian)" atau "(1 bagian)"
        if (preg_match('/\((\d+)\s*bagian\)/i', $bagian, $match)) {
            return (int) $match[1];
        }

        $lower = strtolower($bagian);

        // Semua varian label ashobah yang diproduksi engine
        $ashobahKeywords = [
            'ashobah binafsihi',
            'ashobah bil ghoiri',
            "ashobah ma'al ghoiri",
            'ashobah (bersama kakek)',
            'ashobah (sisa dari kakek)',
            'ashobah (seluruh sisa)',
            'ashobah habis',
            '+ ashobah',            // "1/6 + Ashobah Binafsihi" (bapak)
            'musytarakah',
        ];

        foreach ($ashobahKeywords as $keyword) {
            if (str_contains($lower, $keyword)) {
                return 1;
            }
        }

        // Fallback: ada kata 'ashobah' di mana saja
        if (str_contains($lower, 'ashobah')) {
            return 1;
        }

        return null;
    }

    // =========================================================
    // MATH HELPERS
    // =========================================================

    private function lcmArray(array $numbers): int
    {
        $lcm = (int) array_shift($numbers);
        foreach ($numbers as $number) {
            $lcm = $this->lcm($lcm, (int) $number);
        }
        return $lcm;
    }

    private function lcm(int $a, int $b): int
    {
        return (int) (abs($a * $b) / $this->gcd($a, $b));
    }

    private function gcd(int $a, int $b): int
    {
        while ($b !== 0) {
            $temp = $b;
            $b    = $a % $b;
            $a    = $temp;
        }
        return $a;
    }
}