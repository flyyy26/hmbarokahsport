<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // KATEGORI: UMUM
            [
                'question' => 'Apa itu Barokah Sport?',
                'answer' => 'Barokah Sport adalah toko online yang menyediakan berbagai peralatan dan perlengkapan olahraga berkualitas. Kami berkomitmen untuk memberikan produk terbaik dengan harga terjangkau untuk mendukung aktivitas olahraga Anda.',
                'category' => 'umum',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah Barokah Sport memiliki toko fisik?',
                'answer' => 'Ya, kami memiliki toko fisik di JL. Tamansari, Tasikmalaya. Anda dapat mengunjungi toko kami untuk melihat produk secara langsung atau melakukan pembelian di tempat.',
                'category' => 'umum',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara menghubungi customer service?',
                'answer' => 'Anda dapat menghubungi customer service kami melalui WhatsApp di 08123516518, telepon di 083857238, atau email ke barokahsport@gmail.com. Tim kami siap membantu Anda Senin-Minggu, 08.00-21.00 WIB.',
                'category' => 'umum',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah produk yang dijual original?',
                'answer' => 'Ya, semua produk yang kami jual adalah 100% original dan berkualitas. Kami bekerja sama dengan distributor resmi dan brand ternama untuk memastikan keaslian produk yang Anda beli.',
                'category' => 'umum',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara mengetahui promo terbaru?',
                'answer' => 'Anda dapat mengikuti media sosial kami di Instagram (@barokah.sport), Facebook (barokahsport), dan TikTok (barokahsport). Kami rutin mengupdate informasi promo, diskon, dan produk terbaru di sana.',
                'category' => 'umum',
                'order' => 5,
                'is_active' => true,
            ],

            // KATEGORI: PRODUK
            [
                'question' => 'Produk olahraga apa saja yang dijual?',
                'answer' => 'Kami menyediakan berbagai produk olahraga seperti sepatu olahraga, pakaian olahraga, perlengkapan fitness, alat kebugaran, aksesoris olahraga, peralatan bulutangkis, peralatan basket, dan masih banyak lagi.',
                'category' => 'produk',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah ada garansi untuk produk?',
                'answer' => 'Ya, setiap produk yang Anda beli mendapatkan garansi sesuai dengan kebijakan masing-masing brand. Garansi mencakup kerusakan pabrik dan cacat produksi. Untuk informasi lebih lanjut, silakan hubungi customer service kami.',
                'category' => 'produk',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara memilih ukuran yang tepat?',
                'answer' => 'Kami menyediakan size chart / tabel ukuran untuk setiap produk di halaman detail produk. Anda juga dapat berkonsultasi dengan customer service kami untuk mendapatkan rekomendasi ukuran yang sesuai dengan kebutuhan Anda.',
                'category' => 'produk',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah produk bisa di-custom atau di-personalisasi?',
                'answer' => 'Untuk beberapa produk tertentu, kami menyediakan layanan personalisasi seperti sablon nama atau nomor di jersey. Silakan hubungi customer service kami untuk informasi lebih lanjut mengenai layanan ini.',
                'category' => 'produk',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah produk yang ditampilkan sesuai dengan aslinya?',
                'answer' => 'Kami berusaha menampilkan gambar produk yang sesuai dengan aslinya. Namun, warna mungkin sedikit berbeda karena pengaturan layar masing-masing perangkat. Deskripsi produk selalu kami tuliskan secara detail dan akurat.',
                'category' => 'produk',
                'order' => 10,
                'is_active' => true,
            ],

            // KATEGORI: PENGIRIMAN
            [
                'question' => 'Berapa lama waktu pengiriman?',
                'answer' => 'Waktu pengiriman bervariasi tergantung lokasi Anda. Untuk wilayah Jabodetabek biasanya 1-2 hari, Jawa dan Bali 2-3 hari, serta luar Jawa 3-5 hari. Estimasi pengiriman akan muncul saat Anda melakukan checkout.',
                'category' => 'pengiriman',
                'order' => 11,
                'is_active' => true,
            ],
            [
                'question' => 'Apa saja kurir yang tersedia?',
                'answer' => 'Kami bekerja sama dengan berbagai jasa pengiriman terpercaya seperti JNE, TIKI, POS Indonesia, J&T, dan Sicepat. Anda dapat memilih kurir favorit Anda saat checkout sesuai dengan kebutuhan.',
                'category' => 'pengiriman',
                'order' => 12,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah gratis ongkos kirim?',
                'answer' => 'Kami menyediakan gratis ongkir untuk pembelian minimal Rp 500.000 di wilayah tertentu. Syarat dan ketentuan berlaku. Cek promo gratis ongkir yang sedang berjalan di halaman utama atau media sosial kami.',
                'category' => 'pengiriman',
                'order' => 13,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara melacak pesanan?',
                'answer' => 'Setelah pesanan diproses, kami akan mengirimkan nomor resi pengiriman melalui email atau WhatsApp. Anda dapat melacak pesanan menggunakan nomor resi tersebut di website kurir yang dipilih.',
                'category' => 'pengiriman',
                'order' => 14,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah pengiriman ke luar kota tersedia?',
                'answer' => 'Ya, kami melayani pengiriman ke seluruh Indonesia. Biaya pengiriman akan dihitung berdasarkan berat produk dan lokasi tujuan. Anda dapat melihat estimasi ongkir di halaman checkout.',
                'category' => 'pengiriman',
                'order' => 15,
                'is_active' => true,
            ],

            // KATEGORI: PEMBAYARAN
            [
                'question' => 'Metode pembayaran apa saja yang tersedia?',
                'answer' => 'Kami menerima berbagai metode pembayaran seperti transfer bank (BCA, Mandiri, BNI, BRI), e-wallet (OVO, GoPay, DANA, ShopeePay), dan kartu kredit/debit. Semua transaksi aman dan terpercaya.',
                'category' => 'pembayaran',
                'order' => 16,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara melakukan pembayaran?',
                'answer' => 'Setelah checkout, Anda akan mendapatkan instruksi pembayaran melalui email. Anda dapat melakukan transfer ke rekening yang tertera atau menggunakan e-wallet pilihan Anda. Jangan lupa konfirmasi pembayaran setelah melakukan transfer.',
                'category' => 'pembayaran',
                'order' => 17,
                'is_active' => true,
            ],
            [
                'question' => 'Berapa batas waktu pembayaran?',
                'answer' => 'Batas waktu pembayaran adalah 24 jam setelah checkout. Jika melebihi waktu tersebut, pesanan akan otomatis dibatalkan. Anda dapat memesan ulang produk tersebut kapan saja.',
                'category' => 'pembayaran',
                'order' => 18,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah pembayaran bisa dicicil?',
                'answer' => 'Saat ini kami belum menyediakan layanan cicilan. Namun, Anda dapat menggunakan kartu kredit untuk pembayaran yang memberikan fleksibilitas pembayaran sesuai dengan kebijakan bank penerbit.',
                'category' => 'pembayaran',
                'order' => 19,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana jika pembayaran gagal?',
                'answer' => 'Jika pembayaran gagal, Anda dapat mencoba kembali menggunakan metode pembayaran lain atau menghubungi customer service kami untuk bantuan. Pesanan Anda tetap aman dan dapat dilanjutkan.',
                'category' => 'pembayaran',
                'order' => 20,
                'is_active' => true,
            ],

            // KATEGORI: GARANSI & RETUR
            [
                'question' => 'Bagaimana kebijakan retur produk?',
                'answer' => 'Kami menerima retur produk dalam waktu 7 hari setelah produk diterima. Syarat retur: produk tidak pernah dipakai, kondisi masih baru, dan masih dengan kemasan asli. Biaya pengiriman untuk retur ditanggung oleh pembeli.',
                'category' => 'garansi',
                'order' => 21,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana jika produk rusak saat pengiriman?',
                'answer' => 'Jika produk rusak saat pengiriman, segera laporkan kepada kami maksimal 24 jam setelah menerima produk. Kami akan mengurus penggantian produk tanpa biaya tambahan. Pastikan untuk merekam video unboxing sebagai bukti.',
                'category' => 'garansi',
                'order' => 22,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah bisa menukar produk dengan ukuran yang berbeda?',
                'answer' => 'Ya, kami menyediakan layanan penukaran ukuran dalam waktu 7 hari setelah produk diterima. Produk harus dalam kondisi baru dan belum dipakai. Biaya pengiriman untuk penukaran ditanggung oleh pembeli.',
                'category' => 'garansi',
                'order' => 23,
                'is_active' => true,
            ],
            [
                'question' => 'Berapa lama proses klaim garansi?',
                'answer' => 'Proses klaim garansi biasanya memakan waktu 3-7 hari kerja setelah kami menerima produk yang diklaim. Tim kami akan memeriksa produk dan memberikan solusi terbaik untuk Anda.',
                'category' => 'garansi',
                'order' => 24,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah ada biaya untuk klaim garansi?',
                'answer' => 'Klaim garansi tidak dikenakan biaya tambahan untuk produk yang mengalami kerusakan pabrik atau cacat produksi. Namun, kerusakan akibat pemakaian sendiri tidak termasuk dalam garansi dan mungkin dikenakan biaya perbaikan.',
                'category' => 'garansi',
                'order' => 25,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}