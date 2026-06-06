<?php

namespace App\Services;

class FaraidhDetailService
{
    public function generate(array $hasil): array
    {
        $penyebut = [];
        $items = [];

        foreach ($hasil as $item) {

            if (($item['hubungan'] ?? '') === 'catatan') {
                continue;
            }

            $fraction = $this->parseFraction($item['bagian']);

            if ($fraction) {
                $penyebut[] = $fraction['denominator'];
            }

            $items[] = [
                'hubungan' => $item['hubungan'],
                'jumlah' => $item['jumlah'],
                'bagian' => $item['bagian'],
                'nominal' => $item['nominal'],

                'pembilang' => $fraction['numerator'] ?? null,
                'penyebut' => $fraction['denominator'] ?? null,

                'saham_awal' => null,
                'saham_akhir' => null,
                'saham_per_orang' => null,
            ];
        }

        // ==================================
        // ASAL MASALAH
        // ==================================

        $asalMasalah = count($penyebut)
            ? $this->lcmArray($penyebut)
            : null;

        // ==================================
        // SAHAM AWAL DZAWIL FURUDH
        // ==================================

        foreach ($items as $index => $item) {

            if (
                $asalMasalah &&
                $item['pembilang'] &&
                $item['penyebut']
            ) {
                $items[$index]['saham_awal'] =
                    ($item['pembilang'] * $asalMasalah)
                    / $item['penyebut'];
            }
        }

        // ==================================
        // TOTAL SAHAM FURUDH
        // ==================================

        $totalSahamAwal = 0;

        foreach ($items as $item) {
            $totalSahamAwal += $item['saham_awal'] ?? 0;
        }

        $sisaSaham = $asalMasalah
            ? max(0, $asalMasalah - $totalSahamAwal)
            : 0;

        // ==================================
        // TOTAL BOBOT ASHOBAH
        // ==================================

        $totalBobotAshobah = 0;

        foreach ($items as $item) {

            $bobot = $this->parseAshobahWeight(
                $item['bagian']
            );

            if ($bobot) {
                $totalBobotAshobah +=
                    ($bobot * $item['jumlah']);
            }
        }

        // ==================================
        // BAGI SAHAM ASHOBAH
        // ==================================

        if ($totalBobotAshobah > 0) {

            foreach ($items as $index => $item) {

                $bobot = $this->parseAshobahWeight(
                    $item['bagian']
                );

                if (!$bobot) {
                    continue;
                }

                $items[$index]['saham_awal'] =
                    ($bobot * $item['jumlah'] * $sisaSaham)
                    / $totalBobotAshobah;
            }
        }

        // ==================================
        // RADD / BAITUL MAAL
        // ==================================

        if ($totalBobotAshobah == 0 && $sisaSaham > 0) {

            foreach ($items as $index => $item) {

                if (
                    str_contains(
                        strtolower($item['hubungan']),
                        'baitul maal'
                    )
                ) {
                    $items[$index]['saham_awal'] =
                        $sisaSaham;
                }
            }
        }

        // ==================================
        // TASHIH SEDERHANA
        // ==================================

        $tashih = $asalMasalah;

        foreach ($items as $item) {

            if (
                $item['saham_awal'] !== null &&
                $item['jumlah'] > 1
            ) {

                if (
                    fmod(
                        $item['saham_awal'],
                        $item['jumlah']
                    ) != 0
                ) {
                    $tashih *= $item['jumlah'];
                }
            }
        }

        // ==================================
        // SAHAM AKHIR
        // ==================================

        foreach ($items as $index => $item) {

            if (!$asalMasalah || !$tashih) {
                continue;
            }

            $faktor = $tashih / $asalMasalah;

            if ($item['saham_awal'] !== null) {

                $items[$index]['saham_akhir'] =
                    $item['saham_awal'] * $faktor;

                $items[$index]['saham_per_orang'] =
                    $items[$index]['saham_akhir']
                    / $item['jumlah'];
            }
        }

        return [
            'asal_masalah' => $asalMasalah,
            'tashih' => $tashih,
            'items' => $items,
        ];
    }

    private function parseFraction(string $bagian): ?array
    {
        if (
            preg_match(
                '/(\d+)\/(\d+)/',
                $bagian,
                $matches
            )
        ) {
            return [
                'numerator' => (int)$matches[1],
                'denominator' => (int)$matches[2],
            ];
        }

        return null;
    }

    private function parseAshobahWeight(
        string $bagian
    ): ?int {

        if (
            preg_match(
                '/\((\d+)\s*bagian\)/i',
                $bagian,
                $match
            )
        ) {
            return (int)$match[1];
        }

        if (
            str_contains(
                strtolower($bagian),
                'ashobah'
            )
        ) {
            return 1;
        }

        return null;
    }

    private function lcmArray(array $numbers): int
    {
        $lcm = array_shift($numbers);

        foreach ($numbers as $number) {
            $lcm = $this->lcm($lcm, $number);
        }

        return $lcm;
    }

    private function lcm(int $a, int $b): int
    {
        return (int)(
            abs($a * $b)
            / $this->gcd($a, $b)
        );
    }

    private function gcd(int $a, int $b): int
    {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }

        return $a;
    }
}