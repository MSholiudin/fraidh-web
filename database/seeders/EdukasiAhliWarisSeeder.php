<?php

namespace Database\Seeders;

use App\Models\EdukasiAhliWaris;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EdukasiAhliWarisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // ================================================
            // PASANGAN
            // ================================================
            [
                'nama_ahli_waris'  => 'suami',
                'kelompok'         => 'Pasangan',
                'deskripsi_aturan' => '1/2 jika tidak ada anak atau cucu. 1/4 jika ada anak atau cucu.',
                'dalil_arab'       => 'وَلَكُمْ نِصْفُ مَا تَرَكَ أَزْوَاجُكُمْ إِن لَّمْ يَكُن لَّهُنَّ وَلَدٌ ۚ فَإِن كَانَ لَهُنَّ وَلَدٌ فَلَكُمُ الرُّبُعُ مِمَّا تَرَكْنَ',
                'dalil_terjemahan' => 'Dan bagimu (suami) seperdua dari harta yang ditinggalkan istri, jika mereka tidak mempunyai anak. Jika mereka mempunyai anak, maka kamu mendapat seperempat dari harta yang ditinggalkannya. (QS. An-Nisa: 12)',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Istri wafat, tidak ada anak maupun cucu.',
                        'solusi'   => 'Suami mendapat 1/2 dari seluruh harta warisan.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'suami', 'bagian' => '1/2', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Istri wafat, ada 1 anak laki-laki dan 1 anak perempuan.',
                        'solusi'   => 'Suami mendapat 1/4, sisa harta dibagi anak laki-laki dan perempuan dengan rasio 2:1.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'suami',   'bagian' => '1/4',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak lk', 'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak pr', 'bagian' => 'ashobah (sisa)', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 1,
            ],
            [
                'nama_ahli_waris'  => 'istri',
                'kelompok'         => 'Pasangan',
                'deskripsi_aturan' => '1/4 jika tidak ada anak atau cucu. 1/8 jika ada anak atau cucu. Jika suami memiliki lebih dari satu istri, bagian dibagi rata.',
                'dalil_arab'       => 'وَلَهُنَّ الرُّبُعُ مِمَّا تَرَكْتُمْ إِن لَّمْ يَكُن لَّكُمْ وَلَدٌ ۚ فَإِن كَانَ لَكُمْ وَلَدٌ فَلَهُنَّ الثُّمُنُ مِمَّا تَرَكْتُم',
                'dalil_terjemahan' => 'Para istri memperoleh seperempat harta yang kamu tinggalkan jika kamu tidak mempunyai anak. Jika kamu mempunyai anak, maka para istri memperoleh seperdelapan. (QS. An-Nisa: 12)',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Suami wafat, tidak ada anak maupun cucu.',
                        'solusi'   => 'Istri mendapat 1/4 dari seluruh harta warisan.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'istri', 'bagian' => '1/4', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Suami wafat, ada 1 anak laki-laki.',
                        'solusi'   => 'Istri mendapat 1/8, sisa seluruhnya untuk anak laki-laki (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 8,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'istri',   'bagian' => '1/8',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak lk', 'bagian' => 'ashobah (sisa)', 'saham' => 7, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Suami wafat, ada 1 anak laki-laki, suami memiliki 2 istri.',
                        'solusi'   => 'Total bagian istri 1/8, dibagi rata antara 2 istri sehingga masing-masing mendapat 1/16.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 8,
                            'tashih'       => 16,
                            'baris' => [
                                ['ahli_waris' => 'istri 1', 'bagian' => '1/8 ÷ 2', 'saham' => 1, 'saham_tashih' => 1],
                                ['ahli_waris' => 'istri 2', 'bagian' => '1/8 ÷ 2', 'saham' => 1, 'saham_tashih' => 1],
                                ['ahli_waris' => 'anak lk', 'bagian' => 'ashobah (sisa)', 'saham' => 6, 'saham_tashih' => 14],
                            ],
                        ],
                    ],
                ],
                'urutan' => 2,
            ],

            // ================================================
            // ORANG TUA
            // ================================================
            [
                'nama_ahli_waris'  => 'bapak',
                'kelompok'         => 'Orang Tua',
                'deskripsi_aturan' => '1/6 jika ada anak laki-laki atau cucu laki-laki. 1/6 + Ashobah (sisa) jika tidak ada anak laki-laki maupun cucu laki-laki.',
                'dalil_arab'       => 'وَلِأَبَوَيْهِ لِكُلِّ وَاحِدٍ مِّنْهُمَا السُّدُسُ مِمَّا تَرَكَ إِن كَانَ لَهُ وَلَدٌ',
                'dalil_terjemahan' => 'Dan untuk kedua ibu-bapak, bagian masing-masing seperenam dari harta yang ditinggalkan, jika yang meninggal itu mempunyai anak. (QS. An-Nisa: 11)',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Ada anak laki-laki, ibu, dan bapak.',
                        'solusi'   => 'Bapak dan ibu masing-masing mendapat 1/6, sisa untuk anak laki-laki (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak',   'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',     'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak lk', 'bagian' => 'ashobah (sisa)', 'saham' => 4, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Hanya ada 1 anak perempuan, ibu, dan bapak.',
                        'solusi'   => 'Anak perempuan 1/2, ibu 1/6, bapak 1/6 + sisa (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => 18,
                            'baris' => [
                                ['ahli_waris' => 'bapak',   'bagian' => '1/6 + sisa',    'saham' => 1, 'saham_tashih' => 8],
                                ['ahli_waris' => 'ibu',     'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => 3],
                                ['ahli_waris' => 'anak pr', 'bagian' => '1/2',           'saham' => 3, 'saham_tashih' => 9],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada anak maupun cucu, hanya bapak dan ibu.',
                        'solusi'   => 'Ibu mendapat 1/3, bapak mendapat sisa seluruhnya sebagai Ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak', 'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',   'bagian' => '1/3',           'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 3,
            ],
            [
                'nama_ahli_waris'  => 'ibu',
                'kelompok'         => 'Orang Tua',
                'deskripsi_aturan' => '1/6 jika ada anak, cucu, atau lebih dari satu saudara. 1/3 jika tidak ada anak, cucu, dan saudara tidak lebih dari satu.',
                'dalil_arab'       => 'فَإِن لَّمْ يَكُن لَّهُ وَلَدٌ وَوَرِثَهُ أَبَوَاهُ فَلِأُمِّهِ الثُّلُثُ ۚ فَإِن كَانَ لَهُ إِخْوَةٌ فَلِأُمِّهِ السُّدُسُ',
                'dalil_terjemahan' => 'Jika yang meninggal tidak mempunyai anak dan ia diwarisi oleh ibu-bapaknya, maka ibunya mendapat sepertiga. Jika yang meninggal itu mempunyai beberapa saudara, maka ibunya mendapat seperenam. (QS. An-Nisa: 11)',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Tidak ada anak dan hanya ada ibu serta bapak.',
                        'solusi'   => 'Ibu mendapat 1/3, bapak mendapat sisa (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',   'bagian' => '1/3',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'bapak', 'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 2 saudara kandung, tidak ada anak.',
                        'solusi'   => 'Ibu terhalang sebagian (Hijab Nuqshan) menjadi 1/6 karena ada 2+ saudara.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',   'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'bapak', 'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada anak laki-laki, ibu, dan bapak.',
                        'solusi'   => 'Ibu mendapat 1/6 karena ada anak. Bapak 1/6, sisa untuk anak laki-laki.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',     'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'bapak',   'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak lk', 'bagian' => 'ashobah (sisa)', 'saham' => 4, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 4,
            ],

            // ================================================
            // ANAK
            // ================================================
            [
                'nama_ahli_waris'  => 'anak laki-laki',
                'kelompok'         => 'Anak',
                'deskripsi_aturan' => 'Ashobah Binafsihi — mengambil seluruh sisa harta setelah bagian tetap dibagikan. Jika bersama anak perempuan, berbagi sisa dengan rasio 2:1 (Ashobah Bil Ghoiri). Tidak terhalang oleh siapapun.',
                'dalil_arab'       => 'يُوصِيكُمُ اللَّهُ فِي أَوْلَادِكُمْ ۖ لِلذَّكَرِ مِثْلُ حَظِّ الْأُنثَيَيْنِ',
                'dalil_terjemahan' => 'Allah mensyariatkan bagimu tentang pembagian warisan untuk anak-anakmu, yaitu bagian seorang anak laki-laki sama dengan bagian dua orang anak perempuan. (QS. An-Nisa: 11)',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Anak laki-laki mewaris sendirian tanpa ahli waris lain.',
                        'solusi'   => 'Mendapat seluruh harta warisan.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak lk', 'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Bersama bapak, ibu, dan 2 anak perempuan.',
                        'solusi'   => 'Bapak 1/6, ibu 1/6, sisa dibagi anak laki-laki (2 bagian) dan tiap anak perempuan (1 bagian).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => 18,
                            'baris' => [
                                ['ahli_waris' => 'bapak',   'bagian' => '1/6',              'saham' => 1, 'saham_tashih' => 3],
                                ['ahli_waris' => 'ibu',     'bagian' => '1/6',              'saham' => 1, 'saham_tashih' => 3],
                                ['ahli_waris' => 'anak lk', 'bagian' => 'ashobah bilghoiri', 'saham' => 4, 'saham_tashih' => 8],
                                ['ahli_waris' => 'anak pr', 'bagian' => 'ashobah bilghoiri', 'saham' => 4, 'saham_tashih' => 4],
                            ],
                        ],
                    ],
                ],
                'urutan' => 5,
            ],
            [
                'nama_ahli_waris'  => 'anak perempuan',
                'kelompok'         => 'Anak',
                'deskripsi_aturan' => '1/2 jika seorang dan tidak ada anak laki-laki. 2/3 jika dua orang atau lebih dan tidak ada anak laki-laki. Ashobah Bil Ghoiri (rasio 1:2) jika ada anak laki-laki.',
                'dalil_arab'       => 'فَإِن كُنَّ نِسَاءً فَوْقَ اثْنَتَيْنِ فَلَهُنَّ ثُلُثَا مَا تَرَكَ ۖ وَإِن كَانَتْ وَاحِدَةً فَلَهَا النِّصْفُ',
                'dalil_terjemahan' => 'Jika anak perempuan itu lebih dari dua, maka bagi mereka dua pertiga dari harta yang ditinggalkan. Jika anak perempuan itu seorang saja, maka ia memperoleh separuh harta. (QS. An-Nisa: 11)',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => '1 anak perempuan, tidak ada anak laki-laki, tidak ada Ashobah.',
                        'solusi'   => 'Mendapat 1/2, sisa dikembalikan kepadanya (Radd).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak pr', 'bagian' => '1/2', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => '3 anak perempuan, tidak ada anak laki-laki.',
                        'solusi'   => 'Mendapat 2/3 dibagi rata, masing-masing 2/9.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => 9,
                            'baris' => [
                                ['ahli_waris' => 'anak pr 1', 'bagian' => '2/3 ÷ 3', 'saham' => 2, 'saham_tashih' => 2],
                                ['ahli_waris' => 'anak pr 2', 'bagian' => '2/3 ÷ 3', 'saham' => 2, 'saham_tashih' => 2],
                                ['ahli_waris' => 'anak pr 3', 'bagian' => '2/3 ÷ 3', 'saham' => 2, 'saham_tashih' => 2],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 1 anak laki-laki dan 2 anak perempuan.',
                        'solusi'   => 'Sisa harta (Ashobah) dibagi: anak laki-laki 2 bagian, tiap anak perempuan 1 bagian.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak lk',   'bagian' => 'ashobah bilghoiri', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak pr 1', 'bagian' => 'ashobah bilghoiri', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak pr 2', 'bagian' => 'ashobah bilghoiri', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 6,
            ],

            // ================================================
            // CUCU
            // ================================================
            [
                'nama_ahli_waris'  => 'cucu laki-laki',
                'kelompok'         => 'Cucu',
                'deskripsi_aturan' => 'Menggantikan kedudukan anak laki-laki jika tidak ada anak. Ashobah Binafsihi jika sendirian. Ashobah Bil Ghoiri jika bersama cucu perempuan. Terhalang oleh anak laki-laki.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Cucu dari anak laki-laki menggantikan kedudukan anak berdasarkan ijma ulama.',
                'hijab_oleh'       => 'anak laki-laki',
                'studi_kasus'      => [
                    [
                        'skenario' => 'Ada anak laki-laki dan cucu laki-laki.',
                        'solusi'   => 'Cucu laki-laki terhalang (Mahjub) oleh anak laki-laki, tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak lk',  'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'cucu lk',  'bagian' => 'mahjub',          'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada anak, mewaris bersama istri.',
                        'solusi'   => 'Istri mendapat 1/4, cucu laki-laki mendapat sisa (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'istri',   'bagian' => '1/4',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'cucu lk', 'bagian' => 'ashobah (sisa)', 'saham' => 3, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 7,
            ],
            [
                'nama_ahli_waris'  => 'cucu perempuan',
                'kelompok'         => 'Cucu',
                'deskripsi_aturan' => '1/2 jika seorang dan tidak ada anak. 2/3 jika dua atau lebih dan tidak ada anak. 1/6 (Takmilah) jika bersama 1 anak perempuan untuk menyempurnakan 2/3. Terhalang oleh anak laki-laki atau 2+ anak perempuan (kecuali ada cucu laki-laki).',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Cucu perempuan dari anak laki-laki menggantikan kedudukan anak perempuan berdasarkan ijma ulama.',
                'hijab_oleh'       => 'anak laki-laki, 2 anak perempuan (kecuali ada cucu laki-laki)',
                'studi_kasus'      => [
                    [
                        'skenario' => 'Bersama 1 anak perempuan, tidak ada cucu laki-laki.',
                        'solusi'   => 'Anak perempuan mendapat 1/2, cucu perempuan mendapat 1/6 (Takmilah untuk menyempurnakan 2/3).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak pr', 'bagian' => '1/2',             'saham' => 3, 'saham_tashih' => null],
                                ['ahli_waris' => 'cucu pr', 'bagian' => '1/6 (takmilah)',  'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 2 anak perempuan, tidak ada cucu laki-laki.',
                        'solusi'   => 'Cucu perempuan terhalang (Mahjub) karena 2/3 sudah penuh untuk 2 anak perempuan.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak pr 1', 'bagian' => '2/3 ÷ 2', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak pr 2', 'bagian' => '2/3 ÷ 2', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'cucu pr',   'bagian' => 'mahjub',   'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 2 anak perempuan dan 1 cucu laki-laki.',
                        'solusi'   => 'Cucu perempuan tidak terhalang, ikut Ashobah Bil Ghoiri bersama cucu laki-laki dengan rasio 2:1.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => 9,
                            'baris' => [
                                ['ahli_waris' => 'anak pr 1', 'bagian' => '2/3 ÷ 2',       'saham' => 1, 'saham_tashih' => 3],
                                ['ahli_waris' => 'anak pr 2', 'bagian' => '2/3 ÷ 2',       'saham' => 1, 'saham_tashih' => 3],
                                ['ahli_waris' => 'cucu lk',   'bagian' => 'ashobah bilghoiri', 'saham' => 1, 'saham_tashih' => 2],
                                ['ahli_waris' => 'cucu pr',   'bagian' => 'ashobah bilghoiri', 'saham' => 1, 'saham_tashih' => 1],
                            ],
                        ],
                    ],
                ],
                'urutan' => 8,
            ],

            // ================================================
            // KAKEK & NENEK
            // ================================================
            [
                'nama_ahli_waris'  => 'kakek',
                'kelompok'         => 'Kakek & Nenek',
                'deskripsi_aturan' => 'Terhalang oleh bapak. 1/6 jika ada anak laki-laki atau cucu laki-laki. 1/6 + Ashobah jika tidak ada anak/cucu laki-laki. Jika bersama saudara: memilih yang paling menguntungkan antara 1/3 harta, muqossamah, atau 1/6 + Ashobah.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Kakek menggantikan kedudukan bapak jika bapak tidak ada, berdasarkan ijma ulama.',
                'hijab_oleh'       => 'bapak',
                'studi_kasus'      => [
                    [
                        'skenario' => 'Ada bapak dan kakek sekaligus.',
                        'solusi'   => 'Kakek terhalang penuh (Mahjub) oleh bapak, tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak', 'bagian' => '1/6 + sisa',  'saham' => 6, 'saham_tashih' => null],
                                ['ahli_waris' => 'kakek', 'bagian' => 'mahjub',       'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada bapak, ada anak laki-laki dan kakek.',
                        'solusi'   => 'Kakek mendapat 1/6, sisa untuk anak laki-laki (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'kakek',   'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak lk', 'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Hanya kakek dan 1 saudara kandung (kalalah).',
                        'solusi'   => 'Kakek memilih muqossamah (½ masing-masing) karena lebih menguntungkan dari 1/3.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'kakek',          'bagian' => 'muqossamah (½)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara kandung','bagian' => 'muqossamah (½)', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 9,
            ],
            [
                'nama_ahli_waris'  => 'nenek pihak bapak',
                'kelompok'         => 'Kakek & Nenek',
                'deskripsi_aturan' => 'Mendapat 1/6. Terhalang oleh ibu dan bapak. Jika bersama nenek pihak ibu yang derajatnya sejajar, berbagi rata 1/6.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Nenek mendapat 1/6 berdasarkan hadis Nabi SAW riwayat Abu Dawud.',
                'hijab_oleh'       => 'ibu, bapak',
                'studi_kasus'      => [
                    [
                        'skenario' => 'Ada ibu dan nenek pihak bapak.',
                        'solusi'   => 'Nenek pihak bapak terhalang penuh oleh ibu.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',              'bagian' => '1/3',  'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'nenek pihak bapak','bagian' => 'mahjub','saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada nenek pihak bapak dan nenek pihak ibu, tidak ada ibu/bapak.',
                        'solusi'   => 'Keduanya berbagi rata 1/6 (masing-masing 1/12).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => 12,
                            'baris' => [
                                ['ahli_waris' => 'nenek pihak bapak', 'bagian' => '1/6 ÷ 2', 'saham' => 1, 'saham_tashih' => 1],
                                ['ahli_waris' => 'nenek pihak ibu',   'bagian' => '1/6 ÷ 2', 'saham' => 1, 'saham_tashih' => 1],
                            ],
                        ],
                    ],
                ],
                'urutan' => 10,
            ],
            [
                'nama_ahli_waris'  => 'nenek pihak ibu',
                'kelompok'         => 'Kakek & Nenek',
                'deskripsi_aturan' => 'Mendapat 1/6. Terhalang oleh ibu saja (tidak terhalang oleh bapak). Jika bersama nenek pihak bapak yang derajatnya sejajar, berbagi rata 1/6.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Nenek mendapat 1/6 berdasarkan hadis Nabi SAW riwayat Abu Dawud.',
                'hijab_oleh'       => 'ibu',
                'studi_kasus'      => [
                    [
                        'skenario' => 'Ada ibu dan nenek pihak ibu.',
                        'solusi'   => 'Nenek pihak ibu terhalang penuh oleh ibu.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',            'bagian' => '1/3',   'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'nenek pihak ibu','bagian' => 'mahjub', 'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada ibu, nenek pihak ibu mewaris sendirian bersama anak laki-laki.',
                        'solusi'   => 'Nenek pihak ibu mendapat 1/6, sisa untuk anak laki-laki (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'nenek pihak ibu', 'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak lk',         'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 11,
            ],

            // ================================================
            // SAUDARA SEIBU
            // ================================================
            [
                'nama_ahli_waris'  => 'saudara seibu',
                'kelompok'         => 'Saudara',
                'deskripsi_aturan' => '1/6 jika seorang. 1/3 jika dua orang atau lebih, dibagi rata antara laki-laki dan perempuan. Terhalang oleh anak, cucu, bapak, dan kakek.',
                'dalil_arab'       => 'فَإِن كَانُوا أَكْثَرَ مِن ذَٰلِكَ فَهُمْ شُرَكَاءُ فِي الثُّلُثِ',
                'dalil_terjemahan' => 'Jika saudara-saudara seibu itu lebih dari seorang, maka mereka bersekutu dalam yang sepertiga itu. (QS. An-Nisa: 12)',
                'hijab_oleh'       => 'anak, cucu, bapak, kakek',
                'studi_kasus'      => [
                    [
                        'skenario' => 'Ada 1 saudara seibu, tidak ada anak/cucu/bapak/kakek (kalalah).',
                        'solusi'   => 'Saudara seibu mendapat 1/6.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara seibu', 'bagian' => '1/6', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 3 saudara seibu (2 laki-laki, 1 perempuan), kalalah.',
                        'solusi'   => 'Mendapat 1/3 dibagi rata (masing-masing 1/9) karena laki-perempuan setara untuk saudara seibu.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => 9,
                            'baris' => [
                                ['ahli_waris' => 'saudara seibu lk 1', 'bagian' => '1/3 ÷ 3', 'saham' => 1, 'saham_tashih' => 1],
                                ['ahli_waris' => 'saudara seibu lk 2', 'bagian' => '1/3 ÷ 3', 'saham' => 1, 'saham_tashih' => 1],
                                ['ahli_waris' => 'saudara seibu pr',   'bagian' => '1/3 ÷ 3', 'saham' => 1, 'saham_tashih' => 1],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada anak laki-laki dan saudara seibu.',
                        'solusi'   => 'Saudara seibu terhalang penuh (Mahjub) oleh anak laki-laki.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak lk',      'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara seibu','bagian' => 'mahjub',          'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 12,
            ],

            // ================================================
            // SAUDARA KANDUNG
            // ================================================
            [
                'nama_ahli_waris'  => 'saudara laki-laki sekandung',
                'kelompok'         => 'Saudara',
                'deskripsi_aturan' => 'Ashobah Binafsihi — mengambil seluruh sisa harta. Terhalang oleh anak laki-laki, cucu laki-laki, dan bapak.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Saudara laki-laki kandung mendapat Ashobah berdasarkan ijma ulama.',
                'hijab_oleh'       => 'anak laki-laki, cucu laki-laki, bapak',
                'studi_kasus'      => [
                    [
                        'skenario' => 'Mewaris bersama ibu, tidak ada anak/cucu/bapak (kalalah).',
                        'solusi'   => 'Ibu mendapat 1/6, saudara laki-laki kandung mendapat sisa (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',                    'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara lk sekandung',   'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada bapak dan saudara laki-laki kandung.',
                        'solusi'   => 'Saudara laki-laki kandung terhalang penuh oleh bapak.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak',                'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara lk sekandung', 'bagian' => 'mahjub',          'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 13,
            ],
            [
                'nama_ahli_waris'  => 'saudara perempuan sekandung',
                'kelompok'         => 'Saudara',
                'deskripsi_aturan' => '1/2 jika seorang dan tidak ada saudara laki-laki kandung. 2/3 jika dua atau lebih dan tidak ada saudara laki-laki kandung. Ashobah Bil Ghoiri (rasio 1:2) jika ada saudara laki-laki kandung. Ashobah Ma\'al Ghoiri jika ada anak/cucu perempuan. Terhalang oleh anak laki-laki, cucu laki-laki, dan bapak.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Saudara perempuan kandung mendapat bagian berdasarkan QS. An-Nisa: 176.',
                'hijab_oleh'       => 'anak laki-laki, cucu laki-laki, bapak',
                'studi_kasus'      => [
                    [
                        'skenario' => '1 saudara perempuan kandung, tidak ada saudara laki-laki, kalalah.',
                        'solusi'   => 'Mendapat 1/2.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara pr sekandung', 'bagian' => '1/2', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Bersama 1 anak perempuan, tidak ada saudara laki-laki kandung.',
                        'solusi'   => 'Anak perempuan 1/2, saudara perempuan kandung mendapat sisa (Ashobah Ma\'al Ghoiri).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak pr',              'bagian' => '1/2',                   'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara pr sekandung', 'bagian' => 'ashobah ma\'al ghoiri', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Bersama 1 saudara laki-laki kandung, kalalah.',
                        'solusi'   => 'Sisa dibagi rasio 2:1 (Ashobah Bil Ghoiri), saudara laki-laki 2 bagian, saudara perempuan 1 bagian.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara lk sekandung', 'bagian' => 'ashobah bilghoiri', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara pr sekandung', 'bagian' => 'ashobah bilghoiri', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 14,
            ],

            // ================================================
            // SAUDARA SEBAPAK
            // ================================================
            [
                'nama_ahli_waris'  => 'saudara laki-laki sebapak',
                'kelompok'         => 'Saudara',
                'deskripsi_aturan' => 'Ashobah Binafsihi. Menggantikan saudara kandung jika tidak ada. Terhalang oleh anak laki-laki, cucu laki-laki, bapak, dan saudara laki-laki kandung.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Saudara laki-laki sebapak mendapat Ashobah menggantikan saudara kandung berdasarkan ijma ulama.',
                'hijab_oleh'       => 'anak laki-laki, cucu laki-laki, bapak, saudara laki-laki sekandung',
                'studi_kasus'      => [
                    [
                        'skenario' => 'Ada saudara laki-laki kandung dan saudara laki-laki sebapak.',
                        'solusi'   => 'Saudara laki-laki sebapak terhalang penuh oleh saudara laki-laki kandung.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara lk sekandung', 'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara lk sebapak',   'bagian' => 'mahjub',          'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada saudara kandung, mewaris bersama ibu.',
                        'solusi'   => 'Ibu mendapat 1/6, saudara laki-laki sebapak mendapat sisa (Ashobah).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',                 'bagian' => '1/6',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara lk sebapak',  'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 15,
            ],
            [
                'nama_ahli_waris'  => 'saudara perempuan sebapak',
                'kelompok'         => 'Saudara',
                'deskripsi_aturan' => '1/2 jika seorang dan tidak ada saudara laki-laki sebapak. 2/3 jika dua atau lebih. 1/6 (Takmilah) jika bersama 1 saudara perempuan kandung untuk menyempurnakan 2/3. Ashobah Bil Ghoiri jika ada saudara laki-laki sebapak. Ashobah Ma\'al Ghoiri jika ada anak/cucu perempuan. Terhalang oleh anak laki-laki, cucu laki-laki, bapak, saudara laki-laki kandung, dan 2+ saudara perempuan kandung (kecuali ada saudara laki-laki sebapak).',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Saudara perempuan sebapak mendapat bagian berdasarkan QS. An-Nisa: 176.',
                'hijab_oleh'       => 'anak laki-laki, cucu laki-laki, bapak, saudara laki-laki sekandung, 2 saudara perempuan sekandung',
                'studi_kasus'      => [
                    [
                        'skenario' => '1 saudara perempuan sebapak, tidak ada saudara kandung sama sekali.',
                        'solusi'   => 'Mendapat 1/2.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara pr sebapak', 'bagian' => '1/2', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Bersama 1 saudara perempuan kandung, tidak ada saudara laki-laki.',
                        'solusi'   => 'Saudara perempuan kandung mendapat 1/2, saudara perempuan sebapak mendapat 1/6 (Takmilah untuk 2/3).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara pr sekandung', 'bagian' => '1/2',            'saham' => 3, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara pr sebapak',   'bagian' => '1/6 (takmilah)', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 2 saudara perempuan kandung, tidak ada saudara laki-laki sebapak.',
                        'solusi'   => 'Saudara perempuan sebapak terhalang karena 2/3 sudah habis untuk 2 saudara perempuan kandung.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara pr sekandung 1', 'bagian' => '2/3 ÷ 2', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara pr sekandung 2', 'bagian' => '2/3 ÷ 2', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara pr sebapak',     'bagian' => 'mahjub',   'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 16,
            ],
        ];

        foreach ($data as $item) {
            EdukasiAhliWaris::updateOrCreate(
                ['nama_ahli_waris' => $item['nama_ahli_waris']],
                [
                    'slug'              => Str::slug($item['nama_ahli_waris']),
                    'kelompok'          => $item['kelompok'],
                    'deskripsi_aturan'  => $item['deskripsi_aturan'],
                    'dalil_arab'        => $item['dalil_arab'],
                    'dalil_terjemahan'  => $item['dalil_terjemahan'],
                    'hijab_oleh'        => $item['hijab_oleh'],
                    'studi_kasus'       => $item['studi_kasus'],
                    'urutan'            => $item['urutan'],
                ]
            );
        }
    }
}