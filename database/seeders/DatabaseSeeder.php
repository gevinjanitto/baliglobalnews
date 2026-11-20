<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Category;
use App\Models\LandingAd;
use App\Models\Page;
use App\Models\Post;
use App\Models\Region;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $email = strtolower(env('ADMIN_EMAIL', 'admin@baliglobalnews.com'));
        if (! User::where('email', $email)->exists()) {
            User::create(['name' => 'Admin Redaksi', 'email' => $email, 'password' => Hash::make(env('ADMIN_PASSWORD', 'BaliGlobal123!'))]);
        }

        if (Region::exists()) {
            return;
        }

        foreach (Setting::DEFAULTS as $k => $v) {
            Setting::put($k, $v);
        }

        $tree = [
            ['Provinsi Bali', 'bali', [
                'Pemprov Bali' => [], 'Denpasar' => ['Pemkot Denpasar'], 'Badung' => ['Pemda Badung'], 'Tabanan' => ['Pemda Tabanan', 'Setwan Tabanan'],
                'Gianyar' => [], 'Bangli' => [], 'Klungkung' => [], 'Karangasem' => [], 'Singaraja' => [], 'Jembrana' => [],
            ]],
            ['Nasional', 'nasional', ['BGN 008' => []]],
            ['Internasional', 'internasional', []],
        ];
        $r = [];
        foreach ($tree as $i => [$name, $slug, $children]) {
            $parent = Region::create(['name' => $name, 'slug' => $slug, 'sort_order' => $i, 'description' => "Berita terkini seputar {$name}."]);
            $r[$name] = $parent;
            $j = 0;
            foreach ($children as $child => $grand) {
                $c = Region::create(['name' => $child, 'slug' => Str::slug($child), 'parent_id' => $parent->id, 'sort_order' => $j++, 'description' => "Kabar terbaru dari {$child}."]);
                $r[$child] = $c;
                foreach ($grand as $k => $g) {
                    $r[$g] = Region::create(['name' => $g, 'slug' => Str::slug($g), 'parent_id' => $c->id, 'sort_order' => $k, 'show_in_menu' => false]);
                }
            }
        }

        $cats = ['Informasi', 'Politik', 'Kriminal', 'Pariwisata', 'Kuliner', 'Usaha', 'Kesehatan', 'Olahraga', 'Teknologi', 'BGN 020'];
        $c = [];
        foreach ($cats as $i => $name) {
            $c[$name] = Category::create([
                'name' => $name, 'slug' => Str::slug($name), 'sort_order' => $i,
                'show_on_home' => in_array($name, ['Pariwisata', 'Politik']), 'show_in_menu' => $name !== 'BGN 020',
                'description' => "Kumpulan berita {$name} pilihan redaksi Bali Global News.",
            ]);
        }

        $ads = [
            'header' => Ad::create(['name' => 'BPD Bali - Dashboard September', 'image' => 'seed/ad-header.jpg', 'link_url' => 'https://www.bpdbali.co.id/', 'format' => 'banner']),
            'bpd' => Ad::create(['name' => 'BPD Bali - Transaksi Online', 'image' => 'seed/ad-bpd.jpg', 'link_url' => 'https://www.bpdbali.co.id/', 'format' => 'banner']),
            'side' => Ad::create(['name' => 'BPD Bali - Sidebar', 'image' => 'seed/ad-sidebar.jpg', 'link_url' => 'https://www.bpdbali.co.id/', 'format' => 'kotak']),
        ];
        foreach (['header' => 'header', 'home_top' => 'bpd', 'home_sidebar' => 'side', 'home_bottom' => 'bpd'] as $slot => $key) {
            LandingAd::create(['slot' => $slot, 'ad_id' => $ads[$key]->id]);
        }

        $posts = [
            ['TPAKD Kabupaten Badung Raih Penghargaan Program Dukungan UMKM Terbaik Tahun 2026 dari OJK Provinsi Bali', 'Badung', 'Usaha', 'p1'],
            ['Aktif Edukasi Masyarakat Bertransaksi Aman, Bank BPD Bali Raih Apresiasi dari Bank Indonesia', 'BGN 008', 'Usaha', 'p2'],
            ['Harga Properti Komersial di Bali Turun', 'BGN 008', 'Usaha', 'p4'],
            ['KPU Badung Raih Dua Penghargaan Nasional Lewat Inovasi Demokrasi Berbudaya', 'Badung', 'Politik', 'p5'],
            ['Perkuat Keamanan Informasi dan Pelindungan Data Pribadi, Pemkab Bangli Gelar Sosialisasi Literasi Siber dan Pendampingan RoPA-DPIA', 'Pemprov Bali', 'Teknologi', 'p6'],
            ['Paripurna Internal DPRD Tabanan Sepakati 11 Usulan Ranperda Dalam Propemperda 2027', 'Tabanan', 'BGN 020', 'p7'],
            ['Sampaikan Laporan di Paripurna Internal, Banggar DPRD Tabanan Arahkan Belanja 2027 ke Empat Sektor Prioritas', 'Setwan Tabanan', 'Politik', 'p8'],
            ['Bupati Sanjaya Apresiasi Lomba Mancing Pesisir Pantai Selabih', 'Pemda Tabanan', 'Pariwisata', 'p9'],
            ['12 Inovasi Terbaik Jayaning Singasana 2026 Lolos ke Tahap Verifikasi, Brida Dorong Inovasi Berdampak bagi Masyarakat', 'Pemda Tabanan', 'Informasi', 'p10'],
            ['Ny. Arya Wibawa Dukung Sosialisasi Dapur Sehat Atasi Stunting bagi Kader Posyandu Desa/Kelurahan Se-Kota Denpasar', 'Denpasar', 'Kesehatan', 'p11'],
            ['Dengarkan Masukan Masyarakat Luas, Pemkot Denpasar Hentikan Sementara Pemasangan Secondary Skin di Jalan Gajah Mada', 'Pemkot Denpasar', 'Informasi', 'p12'],
            ['Wagub Giri Prasta Dorong TPAKD Perluas Akses Keuangan Produktif untuk Tingkatkan Kesejahteraan Masyarakat', 'Pemprov Bali', 'Usaha', 'p13'],
            ['Wagub Giri Prasta Tutup Kejuaraan Menembak PRG Cup 2026, Serahkan Bantuan Rp50 Juta kepada Perbakin Bali', 'Pemprov Bali', 'Olahraga', 'p14'],
            ['DPRD Badung Ucapkan Dirgahayu Kota Mangupura', 'Badung', 'Informasi', 'p15'],
            ['Satreskrim Polres Tabanan Ungkap Pencurian di Empat Lokasi, Satu Tersangka Diciduk', 'Tabanan', 'Kriminal', 'p16'],
            ['Gubernur Koster Ajak Masyarakat Budayakan Hidup Sehat, Jaga Keseimbangan Pikiran dan Fisik', 'Denpasar', 'Kesehatan', 'p17'],
            ['Denpasar Bercerita, Sayembara Cipta Puisi Jadi Ruang Kreativitas dan Rekam Jejak Kehidupan Kota', 'Denpasar', 'Pariwisata', 'p18'],
            ['Polda Bali Tangani 3 Kasus Narkoba Diungkap Bareskrim di Lapas Kerobokan', 'BGN 008', 'Kriminal', 'p19'],
            ['Naik Shuttle di Bali Kini Bisa Bayar Pakai QRIS Tap Bayar Rp68', 'BGN 008', 'Pariwisata', 'p20'],
            ['UMKM Badung Didorong Naik Kelas Lewat Program Pendampingan dan Akses Pembiayaan', 'Pemda Badung', 'Usaha', 'p3'],
        ];

        $time = now()->subMinutes(20);
        foreach ($posts as $i => [$title, $region, $cat, $img]) {
            $regionName = $r[$region]->name;
            $p = Post::create([
                'title' => $title,
                'slug' => Post::uniqueSlug($title),
                'excerpt' => null,
                'body' => $this->body($title, $regionName),
                'image' => "seed/{$img}.jpg",
                'image_caption' => 'Dok. Bali Global News',
                'region_id' => $r[$region]->id,
                'category_id' => $c[$cat]->id,
                'status' => 'published',
                'is_headline' => $i < 8,
                'is_editor_choice' => in_array($i, [7, 8, 9, 11, 12, 15, 16, 18]),
                'is_breaking' => $i < 5,
                'views' => random_int(800, 9800),
                'published_at' => (clone $time)->subMinutes($i * 47),
            ]);
            $p->categories()->attach($c[$cat]->id);
            $p->ads()->attach($ads['bpd']->id, ['placement' => 'tengah']);
            $p->ads()->attach($ads['side']->id, ['placement' => 'sidebar']);
        }

        Page::create(['title' => 'Box Redaksi', 'slug' => 'box-redaksi', 'sort_order' => 0, 'body' => file_get_contents(__DIR__.'/content/box-redaksi.html')]);
        Page::create(['title' => 'Pedoman Media Siber', 'slug' => 'pedoman-pemberitaan-media-siber', 'sort_order' => 1, 'body' => file_get_contents(__DIR__.'/content/pedoman-media-siber.html')]);
    }

    private function body(string $title, string $region): string
    {
        return "<p><strong>{$region}, Bali Global News</strong> — Informasi ini disampaikan dalam kegiatan yang dihadiri sejumlah pejabat daerah, tokoh masyarakat, serta perwakilan instansi terkait.</p>"
            ."<p>Dalam keterangannya, pihak terkait menegaskan bahwa langkah tersebut merupakan bagian dari komitmen untuk terus menghadirkan pelayanan terbaik bagi masyarakat. Kolaborasi lintas sektor dinilai menjadi kunci keberhasilan program yang telah berjalan selama ini.</p>"
            ."<p>Masyarakat menyambut baik kegiatan ini dan berharap program serupa dapat terus dilaksanakan secara berkelanjutan, sehingga manfaatnya dapat dirasakan lebih luas hingga ke tingkat desa dan kelurahan.</p>"
            .'<blockquote>“Kami ingin memastikan setiap kebijakan benar-benar berdampak dan dirasakan langsung oleh masyarakat,” ujarnya.</blockquote>'
            .'<p>Ke depan, pemerintah daerah bersama para pemangku kepentingan akan melakukan evaluasi berkala guna memastikan target yang telah ditetapkan dapat tercapai sesuai rencana. <em>(BGN)</em></p>';
    }
}
