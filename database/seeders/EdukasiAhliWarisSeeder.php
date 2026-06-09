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
                        'solusi'   => 'Tidak ada anak maupun cucu, sehingga suami mendapat 1/2. Asal masalah 2: suami mendapat 1 saham.',
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
                        'solusi'   => 'Karena ada anak, suami turun menjadi 1/4. Asal masalah 4: suami 1 saham, sisa 3 saham untuk anak laki-laki (2 saham) dan anak perempuan (1 saham) dengan rasio 2:1.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'suami',           'bagian' => '1/4',                'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak laki-laki',  'bagian' => 'ashobah binafsihi',  'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak perempuan',  'bagian' => 'ashobah binafsihi',  'saham' => 1, 'saham_tashih' => null],
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
                        'solusi'   => 'Tidak ada anak maupun cucu, sehingga istri mendapat 1/4. Asal masalah 4: istri mendapat 1 saham.',
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
                        'solusi'   => 'Karena ada anak, istri turun menjadi 1/8. Asal masalah 8: istri 1 saham, sisa 7 saham untuk anak laki-laki sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 8,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'istri',          'bagian' => '1/8',           'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak laki-laki', 'bagian' => 'ashobah (sisa)', 'saham' => 7, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Suami wafat, ada 1 anak laki-laki, suami memiliki 2 istri.',
                        'solusi'   => 'Karena ada anak, total bagian istri 1/8 dibagi rata untuk 2 istri. 1/8 dari asal masalah 8 = 1, tidak bisa dibagi 2, jadi tashih 16; masing-masing istri mendapat 1 saham tashih. Sisa 14 saham untuk anak laki-laki.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 8,
                            'tashih'       => 16,
                            'baris' => [
                                ['ahli_waris' => 'istri 1',         'bagian' => '1/8 ÷ 2',       'saham' => '1/16', 'saham_tashih' => 1],
                                ['ahli_waris' => 'istri 2',         'bagian' => '1/8 ÷ 2',       'saham' => '1/16', 'saham_tashih' => 1],
                                ['ahli_waris' => 'anak laki-laki',  'bagian' => 'ashobah (sisa)', 'saham' => 7,      'saham_tashih' => 14],
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
                        'solusi'   => 'Ada anak laki-laki, sehingga bapak dan ibu masing-masing mendapat 1/6. Asal masalah 6: bapak 1 saham, ibu 1 saham, sisa 4 saham untuk anak laki-laki sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak',          'bagian' => '1/6',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',            'bagian' => '1/6',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak laki-laki', 'bagian' => 'ashobah (sisa)', 'saham' => 4, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Hanya ada 1 anak perempuan, ibu, dan bapak.',
                        'solusi'   => 'Ada anak perempuan tanpa anak laki-laki, sehingga bapak mendapat 1/6 + sisa (ashobah). Asal masalah 6: anak perempuan 3 saham (1/2), ibu 1 saham (1/6), bapak mendapat 2 saham sisa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak',          'bagian' => 'Ashobah', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',            'bagian' => '1/6',     'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak perempuan', 'bagian' => '1/2',     'saham' => 3, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada anak maupun cucu, hanya bapak dan ibu.',
                        'solusi'   => 'Tidak ada anak maupun cucu. Ibu mendapat 1/3, bapak mendapat sisa seluruhnya sebagai ashobah. Asal masalah 3: ibu 1 saham, bapak 2 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak', 'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',   'bagian' => '1/3',            'saham' => 1, 'saham_tashih' => null],
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
                        'solusi'   => 'Tidak ada anak dan tidak ada saudara lebih dari satu, sehingga ibu mendapat 1/3. Asal masalah 3: ibu 1 saham, bapak 2 saham sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',   'bagian' => '1/3',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'bapak', 'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 2 saudara kandung, tidak ada anak.',
                        'solusi'   => 'Ada 2 saudara kandung menyebabkan ibu terhalang sebagian (hijab nuqshan) dari 1/3 menjadi 1/6. Asal masalah 6: ibu 1 saham, bapak 5 saham sebagai ashobah. Kedua saudara kandung terhijab, tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',              'bagian' => '1/6',            'saham' => 1,    'saham_tashih' => null],
                                ['ahli_waris' => 'bapak',            'bagian' => 'ashobah (sisa)', 'saham' => 5,    'saham_tashih' => null],
                                ['ahli_waris' => 'saudara kandung 1','bagian' => 'Terhijab',       'saham' => null, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara kandung 2','bagian' => 'Terhijab',       'saham' => null, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada anak laki-laki, ibu, dan bapak.',
                        'solusi'   => 'Ada anak laki-laki, sehingga ibu turun menjadi 1/6. Asal masalah 6: ibu 1 saham, bapak 1 saham (1/6), sisa 4 saham untuk anak laki-laki sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',            'bagian' => '1/6',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'bapak',          'bagian' => '1/6',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak laki-laki', 'bagian' => 'ashobah (sisa)', 'saham' => 4, 'saham_tashih' => null],
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
                        'solusi'   => 'Tidak ada ahli waris lain, seluruh harta jatuh kepada anak laki-laki sebagai ashobah binafsihi.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak laki-laki', 'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Bersama bapak, ibu, dan 2 anak perempuan.',
                        'solusi'   => 'Bapak dan ibu masing-masing mendapat 1/6. Sisa 4/6 untuk ashobah (1 anak laki-laki + 2 anak perempuan, rasio 2:1 = 4 kepala). Asal masalah 6: bapak 1, ibu 1, ashobah 4. Karena 4 tidak habis dibagi 4 kepala (AL=2, AP=1, AP=1), dilakukan tashih: 6 × 3 = 18. Bapak 3, ibu 3, anak laki-laki 8, anak perempuan 4.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => 18,
                            'baris' => [
                                ['ahli_waris' => 'bapak',          'bagian' => '1/6',              'saham' => 1,    'saham_tashih' => 3],
                                ['ahli_waris' => 'ibu',            'bagian' => '1/6',              'saham' => 1,    'saham_tashih' => 3],
                                ['ahli_waris' => 'anak laki-laki', 'bagian' => 'ashobah bilghoiri','saham' => 4,    'saham_tashih' => 8],
                                ['ahli_waris' => 'anak perempuan', 'bagian' => 'ashobah bilghoiri','saham' => null, 'saham_tashih' => 4],
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
                        'solusi'   => 'Hanya 1 anak perempuan tanpa ashobah, mendapat 1/2. Asal masalah 2: anak perempuan 1 saham. Sisa 1 saham dikembalikan kepadanya melalui radd.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak perempuan', 'bagian' => '1/2', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => '3 anak perempuan, tidak ada anak laki-laki.',
                        'solusi'   => '3 anak perempuan tanpa anak laki-laki mendapat 2/3 dibagi rata. 2/3 dari asal masalah 3 = 2, tidak bisa dibagi 3 orang, jadi tashih 9; 9 × 2/3 = 6, dibagi 3 = 2 saham tashih masing-masing.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => 9,
                            'baris' => [
                                ['ahli_waris' => 'anak perempuan 1', 'bagian' => '2/3 ÷ 3', 'saham' => '3/9', 'saham_tashih' => 2],
                                ['ahli_waris' => 'anak perempuan 2', 'bagian' => '2/3 ÷ 3', 'saham' => '3/9', 'saham_tashih' => 2],
                                ['ahli_waris' => 'anak perempuan 3', 'bagian' => '2/3 ÷ 3', 'saham' => '3/9', 'saham_tashih' => 2],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 1 anak laki-laki dan 2 anak perempuan.',
                        'solusi'   => 'Ada anak laki-laki, sehingga semua masuk ashobah bilghoiri dengan rasio 2:1. Asal masalah 4: anak laki-laki 2 saham, tiap anak perempuan 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak laki-laki',   'bagian' => 'ashobah bilghoiri', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak perempuan 1', 'bagian' => 'ashobah bilghoiri', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak perempuan 2', 'bagian' => 'ashobah bilghoiri', 'saham' => 1, 'saham_tashih' => null],
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
                        'solusi'   => 'Ada anak laki-laki yang menghijab cucu laki-laki secara penuh. Anak laki-laki mendapat seluruh harta sebagai ashobah, cucu laki-laki tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak laki-laki', 'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'cucu laki-laki', 'bagian' => 'mahjub',          'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada anak, mewaris bersama istri.',
                        'solusi'   => 'Tidak ada anak, sehingga cucu laki-laki menggantikan posisi anak. Istri mendapat 1/4 karena tidak ada anak. Asal masalah 4: istri 1 saham, cucu laki-laki 3 saham sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'istri',          'bagian' => '1/4',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'cucu laki-laki', 'bagian' => 'ashobah (sisa)', 'saham' => 3, 'saham_tashih' => null],
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
                        'solusi'   => 'Anak perempuan mendapat 1/2, jatah 2/3 belum terpenuhi sehingga cucu perempuan mendapat 1/6 takmilah untuk menyempurnakan 2/3. Asal masalah 6: anak perempuan 3 saham, cucu perempuan 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak perempuan', 'bagian' => '1/2',            'saham' => 3, 'saham_tashih' => null],
                                ['ahli_waris' => 'cucu perempuan', 'bagian' => '1/6 (takmilah)', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 2 anak perempuan, tidak ada cucu laki-laki.',
                        'solusi'   => 'Ada 2 anak perempuan yang sudah menghabiskan jatah 2/3, sehingga cucu perempuan terhijab penuh. Asal masalah 3: tiap anak perempuan 1 saham, cucu perempuan tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak perempuan 1', 'bagian' => '2/3 ÷ 2', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak perempuan 2', 'bagian' => '2/3 ÷ 2', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'cucu perempuan',   'bagian' => 'mahjub',   'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 2 anak perempuan dan 1 cucu laki-laki.',
                        'solusi'   => '2 anak perempuan mendapat 2/3 dibagi rata, sisa 1/3 untuk cucu laki-laki dan cucu perempuan (ashobah bilghoiri, rasio 2:1). Asal masalah 3: tiap anak perempuan 1 saham, ashobah 1 saham. Karena 1 tidak habis dibagi 3 kepala (CL=2, CP=1), tashih 9; anak perempuan 1 menjadi 3, anak perempuan 2 menjadi 3, cucu laki-laki 2, cucu perempuan 1.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => 9,
                            'baris' => [
                                ['ahli_waris' => 'anak perempuan 1', 'bagian' => '2/3 ÷ 2',          'saham' => 1,    'saham_tashih' => 3],
                                ['ahli_waris' => 'anak perempuan 2', 'bagian' => '2/3 ÷ 2',          'saham' => 1,    'saham_tashih' => 3],
                                ['ahli_waris' => 'cucu laki-laki',   'bagian' => 'ashobah bilghoiri', 'saham' => 1,    'saham_tashih' => 2],
                                ['ahli_waris' => 'cucu perempuan',   'bagian' => 'ashobah bilghoiri', 'saham' => null, 'saham_tashih' => 1],
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
                        'solusi'   => 'Bapak masih ada sehingga menghijab kakek secara penuh. Bapak mendapat 1/6 + sisa ashobah, kakek tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak', 'bagian' => '1/6 + sisa', 'saham' => 6, 'saham_tashih' => null],
                                ['ahli_waris' => 'kakek', 'bagian' => 'mahjub',      'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada bapak, ada anak laki-laki dan kakek.',
                        'solusi'   => 'Tidak ada bapak, kakek menggantikan posisinya. Ada anak laki-laki, sehingga kakek mendapat 1/6. Asal masalah 6: kakek 1 saham, sisa 5 saham untuk anak laki-laki sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'kakek',          'bagian' => '1/6',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak laki-laki', 'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Hanya kakek dan 1 saudara kandung (kalalah).',
                        'solusi'   => 'Kalalah dengan 1 saudara. Opsi muqosamah: kakek dihitung 2 kepala, saudara 2 kepala → kakek 2/4 = 1/2. Opsi 1/3 harta: kakek = 1/3. Muqosamah (1/2) lebih besar, dipilih. Asal masalah 2: kakek 1 saham, saudara 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'kakek',           'bagian' => 'muqossamah (½)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara kandung', 'bagian' => 'muqossamah (½)', 'saham' => 1, 'saham_tashih' => null],
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
                        'solusi'   => 'Ibu masih ada sehingga menghijab nenek pihak bapak secara penuh. Asal masalah 3: ibu 1 saham (1/3), nenek tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',               'bagian' => '1/3',   'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'nenek pihak bapak', 'bagian' => 'mahjub', 'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada nenek pihak bapak dan nenek pihak ibu, tidak ada ibu/bapak.',
                        'solusi'   => 'Tidak ada ibu maupun bapak, keduanya berbagi rata 1/6. 1/6 dari asal masalah 6 = 1, tidak bisa dibagi 2, jadi tashih 12; masing-masing mendapat 1 saham tashih.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => 12,
                            'baris' => [
                                ['ahli_waris' => 'nenek pihak bapak', 'bagian' => '1/6 ÷ 2', 'saham' => 1,    'saham_tashih' => 1],
                                ['ahli_waris' => 'nenek pihak ibu',   'bagian' => '1/6 ÷ 2', 'saham' => null, 'saham_tashih' => 1],
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
                        'solusi'   => 'Ibu masih ada sehingga menghijab nenek pihak ibu secara penuh. Asal masalah 3: ibu 1 saham (1/3), nenek tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',             'bagian' => '1/3',   'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'nenek pihak ibu', 'bagian' => 'mahjub', 'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada ibu, nenek pihak ibu mewaris sendirian bersama anak laki-laki.',
                        'solusi'   => 'Tidak ada ibu, nenek pihak ibu mendapat 1/6. Asal masalah 6: nenek 1 saham, sisa 5 saham untuk anak laki-laki sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'nenek pihak ibu', 'bagian' => '1/6',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak laki-laki',  'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
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
                        'solusi'   => 'Kalalah dengan 1 saudara seibu, mendapat 1/6. Asal masalah 6: saudara seibu 1 saham.',
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
                        'solusi'   => 'Mendapat 1/3 dibagi rata karena laki-perempuan setara untuk saudara seibu. 1/3 dari asal masalah 3 = 1, tidak bisa dibagi 3 orang, jadi tashih 9; 9 dibagi 3 = 3 saham per orang, lalu 3 dibagi 3 = 1 saham tashih masing-masing.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => 9,
                            'baris' => [
                                ['ahli_waris' => 'saudara seibu laki-laki 1', 'bagian' => '1/3 ÷ 3', 'saham' => null, 'saham_tashih' => 1],
                                ['ahli_waris' => 'saudara seibu laki-laki 2', 'bagian' => '1/3 ÷ 3', 'saham' => 1,    'saham_tashih' => 1],
                                ['ahli_waris' => 'saudara seibu perempuan',   'bagian' => '1/3 ÷ 3', 'saham' => null, 'saham_tashih' => 1],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada anak laki-laki dan saudara seibu.',
                        'solusi'   => 'Ada anak laki-laki yang menghijab saudara seibu secara penuh. Anak laki-laki mendapat seluruh harta sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak laki-laki', 'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara seibu',  'bagian' => 'mahjub',          'saham' => 0, 'saham_tashih' => null],
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
                        'solusi'   => 'Kalalah, tidak ada anak/cucu/bapak. Ibu mendapat 1/6. Asal masalah 6: ibu 1 saham, saudara laki-laki kandung 5 saham sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',                         'bagian' => '1/6',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki sekandung', 'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada bapak dan saudara laki-laki kandung.',
                        'solusi'   => 'Bapak masih ada sehingga menghijab saudara laki-laki kandung secara penuh. Bapak mendapat seluruh harta sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'bapak',                       'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki sekandung', 'bagian' => 'mahjub',          'saham' => 0, 'saham_tashih' => null],
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
                        'solusi'   => 'Kalalah, hanya 1 saudara perempuan kandung tanpa saudara laki-laki. Mendapat 1/2. Asal masalah 2: 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara perempuan sekandung', 'bagian' => '1/2', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Bersama 1 anak perempuan, tidak ada saudara laki-laki kandung.',
                        'solusi'   => 'Anak perempuan mendapat 1/2, sisa 1/2 jatuh ke saudara perempuan kandung sebagai ashobah ma\'al ghoiri. Asal masalah 2: anak perempuan 1 saham, saudara perempuan kandung 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'anak perempuan',              'bagian' => '1/2',                   'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara perempuan sekandung', 'bagian' => 'ashobah ma\'al ghoiri', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Bersama 1 saudara laki-laki kandung, kalalah.',
                        'solusi'   => 'Kalalah bersama 1 saudara laki-laki kandung, masuk ashobah bilghoiri rasio 2:1. Asal masalah 3: saudara laki-laki 2 saham, saudara perempuan 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara laki-laki sekandung', 'bagian' => 'ashobah bilghoiri', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara perempuan sekandung', 'bagian' => 'ashobah bilghoiri', 'saham' => 1, 'saham_tashih' => null],
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
                        'solusi'   => 'Saudara laki-laki kandung masih ada sehingga menghijab saudara laki-laki sebapak secara penuh. Saudara kandung mendapat seluruh harta sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 1,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara laki-laki sekandung', 'bagian' => 'ashobah (semua)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki sebapak',   'bagian' => 'mahjub',          'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Tidak ada saudara kandung, mewaris bersama ibu.',
                        'solusi'   => 'Tidak ada saudara kandung, kalalah. Ibu mendapat 1/6. Asal masalah 6: ibu 1 saham, saudara laki-laki sebapak 5 saham sebagai ashobah.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'ibu',                       'bagian' => '1/6',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki sebapak', 'bagian' => 'ashobah (sisa)', 'saham' => 5, 'saham_tashih' => null],
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
                        'solusi'   => 'Tidak ada saudara kandung sama sekali, kalalah. 1 saudara perempuan sebapak mendapat 1/2. Asal masalah 2: 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara perempuan sebapak', 'bagian' => '1/2', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Bersama 1 saudara perempuan kandung, tidak ada saudara laki-laki.',
                        'solusi'   => 'Saudara perempuan kandung mendapat 1/2, jatah 2/3 belum terpenuhi sehingga saudara perempuan sebapak mendapat 1/6 takmilah. Asal masalah 6: saudara perempuan kandung 3 saham, saudara perempuan sebapak 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara perempuan sekandung', 'bagian' => '1/2',            'saham' => 3, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara perempuan sebapak',   'bagian' => '1/6 (takmilah)', 'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Ada 2 saudara perempuan kandung, tidak ada saudara laki-laki sebapak.',
                        'solusi'   => '2 saudara perempuan kandung sudah menghabiskan jatah 2/3, sehingga saudara perempuan sebapak terhijab penuh. Asal masalah 3: tiap saudara perempuan kandung 1 saham, saudara perempuan sebapak tidak mendapat apa-apa.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'saudara perempuan sekandung 1', 'bagian' => '2/3 ÷ 2', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara perempuan sekandung 2', 'bagian' => '2/3 ÷ 2', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara perempuan sebapak',     'bagian' => 'mahjub',   'saham' => 0, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 16,
            ],

            // ================================================
            // KASUS KHUSUS
            // ================================================
            [
                'nama_ahli_waris'  => 'gharrawain',
                'kelompok'         => 'Kasus Khusus',
                'deskripsi_aturan' => 'Terjadi ketika pewaris hanya meninggalkan suami atau istri, ibu, dan bapak — tanpa anak, cucu, maupun saudara. Dalam kondisi ini ibu tidak mendapat 1/3 dari seluruh harta, melainkan 1/3 dari sisa setelah bagian suami/istri diambil.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Dinamakan Gharrawain (dua yang bercahaya) karena dua kasus ini sangat terkenal dalam ilmu faraidh: kasus dengan suami dan kasus dengan istri.',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Pewaris meninggalkan suami, ibu, dan bapak.',
                        'solusi'   => 'Suami mendapat 1/2 = 3 saham. Sisa 3 saham: ibu mendapat 1/3 dari sisa = 1 saham, bapak mendapat 2/3 sisa = 2 saham sebagai ashobah. Asal masalah 6.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'suami', 'bagian' => '1/2',            'saham' => 3, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',   'bagian' => '1/3 sisa',       'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'bapak', 'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Pewaris meninggalkan istri, ibu, dan bapak.',
                        'solusi'   => 'Istri mendapat 1/4 = 1 saham. Sisa 3 saham: ibu mendapat 1/3 dari sisa = 1 saham, bapak mendapat 2/3 sisa = 2 saham sebagai ashobah. Asal masalah 4.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'istri', 'bagian' => '1/4',            'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',   'bagian' => '1/3 sisa',       'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'bapak', 'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 17,
            ],

            [
                'nama_ahli_waris'  => 'akdariyah',
                'kelompok'         => 'Kasus Khusus',
                'deskripsi_aturan' => 'Terjadi ketika pewaris meninggalkan suami, ibu, kakek, dan tepat satu saudari perempuan (kandung atau sebapak) — tanpa anak maupun cucu. Disebut Akdariyah karena penyelesaiannya unik: bagian kakek dan saudari perempuan digabung lalu dibagi ulang dengan rasio 2:1.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Dinamakan Akdariyah karena Zaid bin Tsabit pernah ditanya tentang kasus ini oleh seseorang dari Akdar, dan jawabannya berbeda dari kasus biasa.',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Pewaris meninggalkan suami, ibu, kakek, dan 1 saudara perempuan kandung.',
                        'solusi'   => 'Langkah 1 — tetapkan bagian awal: suami 1/2, ibu 1/3, kakek 1/6, saudari 1/2. Total = 3/6 + 2/6 + 1/6 + 3/6 = 9/6, melebihi asal masalah → aul ke /9. Langkah 2 — gabungkan bagian kakek (1 saham) + saudari (3 saham) = 4 saham, lalu bagi ulang rasio 2:1 → kakek 8/27, saudari 4/27. Tashih akhir menjadi 27: suami 9, ibu 6, kakek 8, saudari 4.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => 27,
                            'aul'          => 9,
                            'baris' => [
                                ['ahli_waris' => 'suami',                     'bagian' => '1/2 (Aul Akdariyah)', 'saham' => 3, 'saham_tashih' => 9],
                                ['ahli_waris' => 'ibu',                       'bagian' => '1/3 (Aul Akdariyah)', 'saham' => 2, 'saham_tashih' => 6],
                                ['ahli_waris' => 'kakek',                     'bagian' => '1/6 (Aul Akdariyah)', 'saham' => 1, 'saham_tashih' => 8],
                                ['ahli_waris' => 'saudari perempuan kandung', 'bagian' => '1/2 (Aul Akdariyah)', 'saham' => 3, 'saham_tashih' => 4],
                            ],
                        ],
                    ],
                ],
                'urutan' => 18,
            ],

            [
                'nama_ahli_waris'  => 'musytarakah',
                'kelompok'         => 'Kasus Khusus',
                'deskripsi_aturan' => 'Terjadi ketika pewaris meninggalkan suami, ibu, dua atau lebih saudara seibu, dan saudara laki-laki kandung — tanpa anak, cucu, bapak, maupun kakek. Dalam kondisi ini saudara laki-laki kandung yang biasanya hanya dapat ashobah, ikut berbagi 1/3 bersama saudara seibu (tidak mendapat sisa karena sisa sudah habis untuk suami dan ibu).',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Disebut Musytarakah (berserikat) karena saudara kandung dan saudara seibu berserikat dalam 1/3. Juga disebut Himariyah karena ada yang berkata kepada Umar: "Anggaplah bapak kami adalah keledai (himar), kami tetap bersaudara satu ibu."',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Pewaris meninggalkan suami, ibu, 2 saudara seibu, dan 1 saudara laki-laki kandung.',
                        'solusi'   => 'Suami mendapat 1/2 = 3 saham, ibu 1/6 = 1 saham. Total furudh 4 saham, sisa 2 saham = 1/3. Saudara seibu normalnya berhak 1/3, namun saudara laki-laki kandung protes karena ashobah sudah habis. Maka 1/3 dibagi rata (musytarakah) antara 2 saudara seibu + 1 saudara kandung = 3 orang. Asal masalah 6, sisa 2 tidak bisa dibagi 3 orang, jadi tashih 18; sisa menjadi 6, dibagi 3 = 2 saham tashih masing-masing.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => 18,
                            'baris' => [
                                ['ahli_waris' => 'suami',                   'bagian' => '1/2',                    'saham' => 3, 'saham_tashih' => 9],
                                ['ahli_waris' => 'ibu',                     'bagian' => '1/6',                    'saham' => 1, 'saham_tashih' => 3],
                                ['ahli_waris' => 'saudara seibu 1',         'bagian' => 'musytarakah (1/3 ÷ 3)', 'saham' => 2, 'saham_tashih' => 2],
                                ['ahli_waris' => 'saudara seibu 2',         'bagian' => 'musytarakah (1/3 ÷ 3)', 'saham' => 2, 'saham_tashih' => 2],
                                ['ahli_waris' => 'saudara laki-laki kand',  'bagian' => 'musytarakah (1/3 ÷ 3)', 'saham' => 2, 'saham_tashih' => 2],
                            ],
                        ],
                    ],
                ],
                'urutan' => 19,
            ],

            [
                'nama_ahli_waris'  => 'aul',
                'kelompok'         => 'Kasus Khusus',
                'deskripsi_aturan' => 'Terjadi ketika total bagian seluruh ahli waris melebihi asal masalah (penyebut tidak cukup). Solusinya: semua bagian dikurangi secara proporsional — penyebut diganti dengan jumlah total pembilang. Aul hanya bisa terjadi pada asal masalah 6, 12, dan 24.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Aul pertama kali diterapkan oleh Umar bin Khattab RA setelah bermusyawarah dengan para sahabat.',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Suami, ibu, dan 2 anak perempuan (asal masalah 12, total saham 13).',
                        'solusi'   => 'Suami harusnya 1/4 = 3 saham, ibu 1/6 = 2 saham, 2 anak perempuan 2/3 = 8 saham (4+4). Total = 17 > 12 → aul. Penyebut dinaikkan menjadi 17. Tiap ahli waris menerima sahamnya dibagi 17 dari total harta.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 12,
                            'tashih'       => null,
                            'aul'          => 13,
                            'baris' => [
                                ['ahli_waris' => 'suami',          'bagian' => "1/4 ('Aul)", 'saham' => 3, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',            'bagian' => "1/6 ('Aul)", 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak perempuan 1','bagian' => "2/3 ('Aul)", 'saham' => 8, 'saham_tashih' => null],
                                ['ahli_waris' => 'anak perempuan 2','bagian' => "2/3 ('Aul)", 'saham' => 8, 'saham_tashih' => null],
                            ],
                            'catatan' => "Total saham 17 > asal masalah 12 → penyebut dinaikkan menjadi 17. Tiap ahli waris menerima saham/17 × harta.",
                        ],
                    ],
                    [
                        'skenario' => 'Suami, ibu, 2 saudari kandung, dan 1 saudara seibu (asal masalah 6, total saham 9).',
                        'solusi'   => 'Suami 1/2 = 3 saham, ibu 1/6 = 1 saham, 2 saudari kandung 2/3 = 4 saham (2+2), saudara seibu 1/6 = 1 saham. Total = 9 > 6 → aul ke /9. Tiap ahli waris menerima sahamnya dibagi 9 dari total harta.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 6,
                            'tashih'       => null,
                            'aul'          => 9,
                            'baris' => [
                                ['ahli_waris' => 'suami',          'bagian' => "1/2 ('Aul)", 'saham' => 3, 'saham_tashih' => null],
                                ['ahli_waris' => 'ibu',            'bagian' => "1/6 ('Aul)", 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudari kand 1', 'bagian' => "2/3 ('Aul)", 'saham' => 4, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudari kand 2', 'bagian' => "2/3 ('Aul)", 'saham' => 4, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara seibu',  'bagian' => "1/6 ('Aul)", 'saham' => 1, 'saham_tashih' => null],
                            ],
                            'catatan' => "Total saham 9 > asal masalah 6 → aul ke /9. Saudari kandung berbagi 4 saham berdua.",
                        ],
                    ],
                ],
                'urutan' => 20,
            ],

            [
                'nama_ahli_waris'  => 'muqosamah',
                'kelompok'         => 'Kasus Khusus',
                'deskripsi_aturan' => 'Terjadi ketika kakek mewaris bersama saudara (tanpa anak/cucu). Kakek memilih bagian terbesar dari tiga opsi: (1) Muqosamah — berbagi kepala dengan saudara (kakek dihitung 2 kepala), (2) 1/3 dari seluruh harta, (3) 1/6 dari seluruh harta. Opsi yang menghasilkan bagian terbesar untuk kakek yang dipilih.',
                'dalil_arab'       => null,
                'dalil_terjemahan' => 'Kakek bersama saudara adalah salah satu masalah paling rumit dalam ilmu faraidh. Zaid bin Tsabit memilih kakek berbagi dengan saudara, sedangkan Ibnu Mas\'ud memilih kakek menghijab saudara.',
                'hijab_oleh'       => null,
                'studi_kasus'      => [
                    [
                        'skenario' => 'Kakek dan 1 saudara laki-laki kandung (tanpa ahli waris lain).',
                        'solusi'   => 'Opsi muqosamah: kakek dihitung 2 kepala, saudara 2 kepala → kakek 2/4 = 1/2. Opsi 1/3 harta: kakek = 1/3. Muqosamah (1/2) lebih besar, dipilih. Asal masalah 2: kakek 1 saham, saudara 1 saham.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 2,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'kakek',                   'bagian' => 'muqossamah (1/2)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki kand',  'bagian' => 'ashobah (sisa)',   'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Kakek dan 4 saudara laki-laki kandung (tanpa ahli waris lain).',
                        'solusi'   => 'Opsi muqosamah: kakek 2 kepala, 4 saudara masing-masing 2 kepala = 10 kepala → kakek 2/10 = 1/5. Opsi 1/3 harta: kakek = 1/3. 1/3 lebih besar, dipilih. Asal masalah 3: kakek 1 saham, sisa 2 saham untuk 4 saudara (perlu tashih).',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 3,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'kakek',                     'bagian' => '1/3 (terpilih)', 'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki kand 1',  'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki kand 2',  'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki kand 3',  'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki kand 4',  'bagian' => 'ashobah (sisa)', 'saham' => 2, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                    [
                        'skenario' => 'Kakek, istri, dan 2 saudara laki-laki kandung.',
                        'solusi'   => 'Istri mendapat 1/4 = 1 saham lebih dulu. Sisa 3 saham. Opsi muqosamah dari sisa: kakek 2/(2+4) × 3 = 1 saham. Opsi 1/3 sisa: 1/3 × 3 = 1 saham. Opsi 1/6 total: 4 × 1/6 < 1. Muqosamah dan 1/3 sisa setara, pilih salah satu. Asal masalah 4: istri 1, kakek 1, tiap saudara 1.',
                        'tabel_perhitungan' => [
                            'asal_masalah' => 4,
                            'tashih'       => null,
                            'baris' => [
                                ['ahli_waris' => 'istri',                    'bagian' => '1/4',             'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'kakek',                    'bagian' => '1/3 sisa',        'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki kand 1', 'bagian' => 'ashobah (sisa)',  'saham' => 1, 'saham_tashih' => null],
                                ['ahli_waris' => 'saudara laki-laki kand 2', 'bagian' => 'ashobah (sisa)',  'saham' => 1, 'saham_tashih' => null],
                            ],
                        ],
                    ],
                ],
                'urutan' => 21,
            ],

        ];

        foreach ($data as $item) {
            EdukasiAhliWaris::updateOrCreate(
                ['nama_ahli_waris' => $item['nama_ahli_waris']],
                [
                    'slug'             => Str::slug($item['nama_ahli_waris']),
                    'kelompok'         => $item['kelompok'],
                    'deskripsi_aturan' => $item['deskripsi_aturan'],
                    'dalil_arab'       => $item['dalil_arab'],
                    'dalil_terjemahan' => $item['dalil_terjemahan'],
                    'hijab_oleh'       => $item['hijab_oleh'],
                    'studi_kasus'      => $item['studi_kasus'],
                    'urutan'           => $item['urutan'],
                ]
            );
        }
    }
}