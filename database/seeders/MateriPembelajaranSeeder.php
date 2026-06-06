<?php

namespace Database\Seeders;

use App\Models\MateriPembelajaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MateriPembelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // ================================================
            // PENGERTIAN
            // ================================================
            [
                'judul'     => 'Apa Itu Ilmu Faraidh?',
                'kategori'  => 'pengertian',
                'urutan'    => 1,
                'konten'    => 'Faraidh (الفرائض) secara bahasa adalah bentuk jamak dari al-faridhah (الفريضة) yang berarti sesuatu yang telah ditetapkan atau ditentukan kadarnya. Dalam istilah syariat Islam, ilmu faraidh adalah ilmu yang membahas tentang pembagian harta warisan kepada ahli waris yang berhak menerimanya sesuai dengan ketentuan Al-Qur\'an dan Hadis. Ilmu ini juga disebut sebagai Ilmu Mawaris (ilmu waris-mewaris).',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Kedudukan Ilmu Faraidh dalam Islam',
                'kategori'  => 'pengertian',
                'urutan'    => 2,
                'konten'    => 'Rasulullah SAW bersabda: "Pelajarilah Al-Qur\'an dan ajarkanlah kepada orang-orang, pelajarilah ilmu faraidh dan ajarkanlah, karena sesungguhnya aku adalah orang yang akan direnggut (mati), sedangkan ilmu akan dicabut dan fitnah akan tampak, hingga dua orang yang berselisih tentang pembagian warisan tidak mendapatkan seorang pun yang dapat memutuskan perkara mereka." (HR. Tirmidzi dan Nasa\'i). Hadis ini menunjukkan betapa pentingnya mempelajari dan mengajarkan ilmu faraidh.',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Tujuan Pembagian Waris dalam Islam',
                'kategori'  => 'pengertian',
                'urutan'    => 3,
                'konten'    => 'Pembagian warisan dalam Islam memiliki beberapa tujuan mulia: (1) Mewujudkan keadilan di antara ahli waris sesuai hak dan kedudukan masing-masing. (2) Mencegah perselisihan dan perpecahan keluarga akibat sengketa harta. (3) Menjaga keberlangsungan harta agar bermanfaat bagi generasi berikutnya. (4) Melaksanakan perintah Allah SWT yang telah menetapkan bagian masing-masing ahli waris secara rinci dalam Al-Qur\'an.',
                'gambar'    => null,
            ],

            // ================================================
            // DALIL
            // ================================================
            [
                'judul'     => 'Dalil Utama: QS. An-Nisa Ayat 11',
                'kategori'  => 'dalil',
                'urutan'    => 4,
                'konten'    => 'يُوصِيكُمُ اللَّهُ فِي أَوْلَادِكُمْ ۖ لِلذَّكَرِ مِثْلُ حَظِّ الْأُنثَيَيْنِ ۚ فَإِن كُنَّ نِسَاءً فَوْقَ اثْنَتَيْنِ فَلَهُنَّ ثُلُثَا مَا تَرَكَ ۖ وَإِن كَانَتْ وَاحِدَةً فَلَهَا النِّصْفُ ۚ وَلِأَبَوَيْهِ لِكُلِّ وَاحِدٍ مِّنْهُمَا السُّدُسُ مِمَّا تَرَكَ إِن كَانَ لَهُ وَلَدٌ

"Allah mensyariatkan bagimu tentang (pembagian warisan untuk) anak-anakmu, yaitu bagian seorang anak laki-laki sama dengan bagian dua orang anak perempuan. Dan jika anak itu semuanya perempuan yang jumlahnya lebih dari dua, maka bagian mereka dua pertiga dari harta yang ditinggalkan. Jika anak perempuan itu seorang saja, maka ia memperoleh setengah harta. Dan untuk kedua ibu-bapak, masing-masing mendapat seperenam dari harta yang ditinggalkan, jika yang meninggal itu mempunyai anak." (QS. An-Nisa: 11)',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Dalil Utama: QS. An-Nisa Ayat 12',
                'kategori'  => 'dalil',
                'urutan'    => 5,
                'konten'    => 'وَلَكُمْ نِصْفُ مَا تَرَكَ أَزْوَاجُكُمْ إِن لَّمْ يَكُن لَّهُنَّ وَلَدٌ ۚ فَإِن كَانَ لَهُنَّ وَلَدٌ فَلَكُمُ الرُّبُعُ مِمَّا تَرَكْنَ

"Dan bagimu (suami) seperdua dari harta yang ditinggalkan oleh istri-istrimu, jika mereka tidak mempunyai anak. Jika mereka mempunyai anak, maka kamu mendapat seperempat dari harta yang ditinggalkannya." (QS. An-Nisa: 12)

Ayat ini juga memuat bagian istri (1/4 atau 1/8), bagian saudara seibu (1/6 atau 1/3), serta ketentuan Kalalah (pewaris yang tidak meninggalkan anak maupun orang tua).',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Dalil Utama: QS. An-Nisa Ayat 176',
                'kategori'  => 'dalil',
                'urutan'    => 6,
                'konten'    => 'يَسْتَفْتُونَكَ قُلِ اللَّهُ يُفْتِيكُمْ فِي الْكَلَالَةِ ۚ إِنِ امْرُؤٌ هَلَكَ لَيْسَ لَهُ وَلَدٌ وَلَهُ أُخْتٌ فَلَهَا نِصْفُ مَا تَرَكَ

"Mereka meminta fatwa kepadamu tentang kalalah. Katakanlah, Allah memberi fatwa kepadamu tentang kalalah, yaitu jika seseorang meninggal dunia tanpa mempunyai anak tetapi mempunyai saudara perempuan, maka bagiannya adalah seperdua dari harta yang ditinggalkannya." (QS. An-Nisa: 176)

Ayat ini mengatur bagian saudara kandung dan saudara sebapak dalam kondisi Kalalah.',
                'gambar'    => null,
            ],

            // ================================================
            // ISTILAH
            // ================================================
            [
                'judul'     => 'Al-Muwarrits (الموروث)',
                'kategori'  => 'istilah',
                'urutan'    => 7,
                'konten'    => 'Al-Muwarrits adalah orang yang meninggal dunia dan meninggalkan harta warisan. Pewaris bisa meninggal secara hakiki (benar-benar meninggal), secara hukmi (misalnya dinyatakan hilang oleh pengadilan), atau secara taqdiri (secara perkiraan, seperti dalam kasus janin). Syarat utama: pewaris harus benar-benar telah wafat sebelum harta dapat dibagikan.',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Al-Warits (الوارث)',
                'kategori'  => 'istilah',
                'urutan'    => 8,
                'konten'    => 'Al-Warits adalah orang yang berhak menerima harta warisan dari pewaris. Ada tiga syarat ahli waris dapat menerima warisan: (1) Masih hidup saat pewaris meninggal. (2) Tidak ada penghalang seperti perbedaan agama atau pembunuhan terhadap pewaris. (3) Memiliki hubungan nasab, pernikahan, atau wala\' (memerdekakan budak) dengan pewaris.',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Al-Mauruts / Tirkah (التركة)',
                'kategori'  => 'istilah',
                'urutan'    => 9,
                'konten'    => 'Al-Mauruts atau Tirkah adalah harta peninggalan yang ditinggalkan oleh pewaris dan akan dibagikan kepada ahli waris. Sebelum dibagikan, harta harus dikurangi terlebih dahulu untuk: (1) Biaya pengurusan jenazah. (2) Pelunasan hutang pewaris. (3) Pelaksanaan wasiat (maksimal 1/3 dari total harta). Sisa setelah pengurangan itulah yang dibagikan kepada ahli waris.',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Ashabul Furudh (أصحاب الفروض)',
                'kategori'  => 'istilah',
                'urutan'    => 10,
                'konten'    => 'Ashabul Furudh adalah ahli waris yang mendapatkan bagian tertentu yang sudah ditetapkan oleh Al-Qur\'an. Bagian-bagian tersebut adalah: 1/2, 1/4, 1/8, 2/3, 1/3, dan 1/6. Ahli waris yang termasuk Ashabul Furudh antara lain: suami, istri, ibu, bapak, nenek, anak perempuan, cucu perempuan, saudara perempuan (dalam kondisi tertentu), dan saudara seibu.',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Ashobah (العصبة)',
                'kategori'  => 'istilah',
                'urutan'    => 11,
                'konten'    => 'Ashobah adalah ahli waris yang mendapatkan sisa harta setelah bagian Ashabul Furudh diambil. Ada tiga jenis Ashobah: (1) Ashobah Binafsihi — laki-laki yang menjadi Ashobah karena dirinya sendiri (anak laki-laki, bapak, kakek, saudara laki-laki). (2) Ashobah Bil Ghoiri — perempuan yang menjadi Ashobah karena ada laki-laki setingkat (anak perempuan bersama anak laki-laki). (3) Ashobah Ma\'al Ghoiri — perempuan yang menjadi Ashobah karena ada perempuan dari kelompok lain (saudara perempuan bersama anak/cucu perempuan).',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Hijab (الحجب)',
                'kategori'  => 'istilah',
                'urutan'    => 12,
                'konten'    => 'Hijab adalah penghalang yang menyebabkan seseorang tidak mendapatkan warisan atau mendapatkan bagian yang lebih kecil. Ada dua jenis Hijab: (1) Hijab Hirman — penghalang total, ahli waris tidak mendapat apa-apa (contoh: cucu terhalang oleh anak laki-laki). (2) Hijab Nuqshan — penghalang sebagian, ahli waris tetap mendapat bagian tapi lebih kecil (contoh: suami mendapat 1/4 bukan 1/2 karena ada anak).',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Radd (الرد)',
                'kategori'  => 'istilah',
                'urutan'    => 13,
                'konten'    => 'Radd adalah pengembalian sisa harta kepada ahli waris Ashabul Furudh secara proporsional, ketika tidak ada Ashobah. Contoh: pewaris meninggalkan ibu dan 1 anak perempuan. Ibu mendapat 1/6, anak perempuan 1/2. Total = 4/6, sisa 2/6 dikembalikan (Radd) ke ibu dan anak perempuan secara proporsional. Suami dan istri tidak mendapat Radd.',
                'gambar'    => null,
            ],
            [
                'judul'     => "Aul (العول)",
                'kategori'  => 'istilah',
                'urutan'    => 14,
                'konten'    => "'Aul adalah kondisi ketika jumlah bagian seluruh ahli waris melebihi 100% dari total harta. Solusinya adalah menaikkan asal masalah (penyebut) sehingga semua bagian berkurang secara proporsional. Contoh: suami 1/2, dua saudara perempuan 2/3, ibu 1/6. Total = 3/6 + 4/6 + 1/6 = 8/6 (melebihi 6/6). Maka asal masalah dinaikkan dari 6 menjadi 8, sehingga suami 3/8, dua saudara perempuan 4/8, ibu 1/8.",
                'gambar'    => null,
            ],
            [
                'judul'     => 'Kalalah (الكلالة)',
                'kategori'  => 'istilah',
                'urutan'    => 15,
                'konten'    => 'Kalalah adalah kondisi di mana pewaris meninggal tanpa meninggalkan keturunan (anak atau cucu) dan tanpa meninggalkan orang tua (bapak atau kakek). Dalam kondisi ini, saudara-saudara pewaris berhak mendapatkan warisan. Ketentuan Kalalah diatur dalam QS. An-Nisa: 12 dan 176.',
                'gambar'    => null,
            ],

            // ================================================
            // CONTOH (RUKUN WARIS)
            // ================================================
            [
                'judul'     => 'Rukun Waris-Mewaris',
                'kategori'  => 'contoh',
                'urutan'    => 16,
                'konten'    => 'Ada tiga rukun yang harus terpenuhi agar waris-mewaris dapat terjadi: (1) Al-Muwarrits — harus ada pewaris yang telah wafat. (2) Al-Warits — harus ada ahli waris yang masih hidup dan berhak menerima. (3) Al-Mauruts — harus ada harta yang ditinggalkan. Jika salah satu rukun tidak terpenuhi, maka proses waris-mewaris tidak dapat dilakukan.',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Sebab-Sebab Mendapat Warisan',
                'kategori'  => 'contoh',
                'urutan'    => 17,
                'konten'    => 'Seseorang berhak mendapat warisan karena tiga sebab: (1) Nasab (hubungan darah) — seperti anak, orang tua, saudara, kakek, nenek. (2) Pernikahan yang sah — suami atau istri yang masih terikat pernikahan saat pewaris wafat. (3) Wala\' (memerdekakan budak) — orang yang memerdekakan budak berhak mewarisi jika tidak ada ahli waris lain. Di antara ketiga sebab ini, nasab adalah sebab yang paling kuat.',
                'gambar'    => null,
            ],
            [
                'judul'     => 'Sebab-Sebab Terhalang Mendapat Warisan',
                'kategori'  => 'contoh',
                'urutan'    => 18,
                'konten'    => 'Ada tiga hal yang menyebabkan seseorang terhalang dari warisan meskipun memiliki hubungan dengan pewaris: (1) Perbudakan — hamba sahaya tidak dapat mewarisi. (2) Pembunuhan — orang yang membunuh pewaris tidak berhak mewarisi hartanya. (3) Perbedaan agama — orang Muslim tidak mewarisi dari non-Muslim, dan sebaliknya. Selain itu, ada juga Hijab (terhalang oleh ahli waris yang lebih dekat).',
                'gambar'    => null,
            ],
        ];

        foreach ($data as $item) {
            MateriPembelajaran::updateOrCreate(
                ['judul' => $item['judul']],
                [
                    'slug'    => Str::slug($item['judul']),
                    'kategori' => $item['kategori'],
                    'urutan'  => $item['urutan'],
                    'konten'  => $item['konten'],
                    'gambar'  => $item['gambar'],
                ]
            );
        }
    }
}