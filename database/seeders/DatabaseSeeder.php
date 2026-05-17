<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== PENGATURAN WEB =====
        DB::table('pengaturan_webs')->insert([
            'nama_yayasan'      => 'Yayasan Indonesia',
            'singkatan_yayasan' => 'YI',
            'alamat_yayasan'    => 'Jl. Pendidikan No. 1, Jakarta Selatan, DKI Jakarta 12345',
            'email_yayasan'     => 'info@yayasanindonesia.sch.id',
            'phone_yayasan'     => '021-87654321',
            'website_yayasan'   => 'https://yayasanindonesia.sch.id',
            'deskripsi_yayasan' => 'Yayasan Indonesia adalah lembaga pendidikan terkemuka yang berkomitmen mencetak generasi penerus bangsa yang berilmu, berakhlak, dan berdaya saing global.',
            'tagline'           => 'Membangun Generasi Unggul Bangsa',
            'ppdb_aktif'        => true,
            'pengumuman'        => 'PPDB Tahun Ajaran 2025/2026 resmi dibuka! Daftarkan putra-putri Anda sekarang.',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ===== USERS ADMIN =====
        DB::table('users')->insert([
            [
                'name'       => 'Super Admin',
                'email'      => 'superadmin@ppdb.id',
                'password'   => Hash::make('Admin123!!'),
                'role'       => 'superadmin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Admin PPDB',
                'email'      => 'admin@ppdb.id',
                'password'   => Hash::make('Admin123!!'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ===== TAHUN AKADEMIK =====
        DB::table('tahun_akademiks')->insert([
            [
                'nama_tahun'             => '2024/2025',
                'tanggal_mulai_daftar'   => '2024-01-10',
                'tanggal_tutup_daftar'   => '2024-06-30',
                'is_active'              => false,
                'created_at'             => now(),
                'updated_at'             => now(),
            ],
            [
                'nama_tahun'             => '2025/2026',
                'tanggal_mulai_daftar'   => '2025-01-15',
                'tanggal_tutup_daftar'   => '2025-07-15',
                'is_active'              => true,
                'created_at'             => now(),
                'updated_at'             => now(),
            ],
        ]);

        // ===== SEKOLAH (9 sekolah: 3 SMP, 3 SMA, 3 SMK) =====
        $sekolahs = [
            // SMP
            ['nama_sekolah'=>'SMP Nusantara Bangsa', 'singkatan'=>'SMPNB', 'tingkatan'=>'SMP', 'npsn'=>'20100001', 'alamat'=>'Jl. Nusantara No. 12, Jakarta Selatan', 'kota'=>'Jakarta Selatan', 'phone'=>'021-70001111', 'email'=>'smpnb@yayasanindonesia.sch.id', 'deskripsi'=>'SMP Nusantara Bangsa adalah sekolah menengah pertama unggulan dengan kurikulum merdeka belajar.', 'akreditasi'=>'A', 'kuota'=>200, 'tahun_berdiri'=>1990, 'urutan'=>1],
            ['nama_sekolah'=>'SMP Bina Insan Mulia', 'singkatan'=>'SMPBIM', 'tingkatan'=>'SMP', 'npsn'=>'20100002', 'alamat'=>'Jl. Kemerdekaan No. 45, Jakarta Timur', 'kota'=>'Jakarta Timur', 'phone'=>'021-70002222', 'email'=>'smpbim@yayasanindonesia.sch.id', 'deskripsi'=>'SMP Bina Insan Mulia memfokuskan pada pendidikan karakter dan prestasi akademik.', 'akreditasi'=>'A', 'kuota'=>180, 'tahun_berdiri'=>1995, 'urutan'=>2],
            ['nama_sekolah'=>'SMP Tunas Harapan', 'singkatan'=>'SMPTH', 'tingkatan'=>'SMP', 'npsn'=>'20100003', 'alamat'=>'Jl. Harapan Baru No. 7, Depok', 'kota'=>'Depok', 'phone'=>'021-70003333', 'email'=>'smpth@yayasanindonesia.sch.id', 'deskripsi'=>'SMP Tunas Harapan dengan fasilitas lengkap dan tenaga pengajar berpengalaman.', 'akreditasi'=>'B', 'kuota'=>150, 'tahun_berdiri'=>2000, 'urutan'=>3],
            // SMA
            ['nama_sekolah'=>'SMA Generasi Emas', 'singkatan'=>'SMAGE', 'tingkatan'=>'SMA', 'npsn'=>'20200001', 'alamat'=>'Jl. Emas No. 88, Jakarta Selatan', 'kota'=>'Jakarta Selatan', 'phone'=>'021-70004444', 'email'=>'smage@yayasanindonesia.sch.id', 'deskripsi'=>'SMA Generasi Emas mencetak lulusan berkualitas siap masuk universitas terbaik.', 'akreditasi'=>'A', 'kuota'=>250, 'tahun_berdiri'=>1992, 'urutan'=>4],
            ['nama_sekolah'=>'SMA Cendekia Utama', 'singkatan'=>'SMACU', 'tingkatan'=>'SMA', 'npsn'=>'20200002', 'alamat'=>'Jl. Ilmu No. 22, Bekasi', 'kota'=>'Bekasi', 'phone'=>'021-70005555', 'email'=>'smacu@yayasanindonesia.sch.id', 'deskripsi'=>'SMA Cendekia Utama fokus pada pengembangan sains, teknologi, dan riset.', 'akreditasi'=>'A', 'kuota'=>220, 'tahun_berdiri'=>1998, 'urutan'=>5],
            ['nama_sekolah'=>'SMA Bhakti Persada', 'singkatan'=>'SMABP', 'tingkatan'=>'SMA', 'npsn'=>'20200003', 'alamat'=>'Jl. Bhakti No. 15, Tangerang', 'kota'=>'Tangerang', 'phone'=>'021-70006666', 'email'=>'smabp@yayasanindonesia.sch.id', 'deskripsi'=>'SMA Bhakti Persada mendidik generasi muda yang berprestasi dan berkarakter.', 'akreditasi'=>'B', 'kuota'=>200, 'tahun_berdiri'=>2003, 'urutan'=>6],
            // SMK
            ['nama_sekolah'=>'SMK Teknologi Mandiri', 'singkatan'=>'SMKTM', 'tingkatan'=>'SMK', 'npsn'=>'20300001', 'alamat'=>'Jl. Industri No. 100, Jakarta Barat', 'kota'=>'Jakarta Barat', 'phone'=>'021-70007777', 'email'=>'smktm@yayasanindonesia.sch.id', 'deskripsi'=>'SMK Teknologi Mandiri menghasilkan tenaga ahli di bidang teknologi informasi dan industri.', 'akreditasi'=>'A', 'kuota'=>300, 'tahun_berdiri'=>1993, 'urutan'=>7],
            ['nama_sekolah'=>'SMK Kriya Nusantara', 'singkatan'=>'SMKKN', 'tingkatan'=>'SMK', 'npsn'=>'20300002', 'alamat'=>'Jl. Kreasi No. 55, Bogor', 'kota'=>'Bogor', 'phone'=>'0251-70008888', 'email'=>'smkkn@yayasanindonesia.sch.id', 'deskripsi'=>'SMK Kriya Nusantara unggul dalam bidang desain, multimedia, dan pariwisata.', 'akreditasi'=>'A', 'kuota'=>280, 'tahun_berdiri'=>1997, 'urutan'=>8],
            ['nama_sekolah'=>'SMK Bina Karya Indonesia', 'singkatan'=>'SMKBKI', 'tingkatan'=>'SMK', 'npsn'=>'20300003', 'alamat'=>'Jl. Karya Utama No. 33, Depok', 'kota'=>'Depok', 'phone'=>'021-70009999', 'email'=>'smkbki@yayasanindonesia.sch.id', 'deskripsi'=>'SMK Bina Karya Indonesia menyiapkan tenaga profesional bisnis dan akuntansi.', 'akreditasi'=>'B', 'kuota'=>250, 'tahun_berdiri'=>2001, 'urutan'=>9],
        ];

        foreach ($sekolahs as $s) {
            DB::table('sekolahs')->insert(array_merge($s, ['is_active'=>true, 'created_at'=>now(), 'updated_at'=>now()]));
        }

        // ===== JURUSAN =====
        $jurusans = [
            // SMP 1,2,3 - tidak ada jurusan khusus
            // SMA 4 (id=4) - Generasi Emas
            ['sekolah_id'=>4, 'nama_jurusan'=>'IPA (Ilmu Pengetahuan Alam)', 'kode_jurusan'=>'IPA', 'kuota'=>100],
            ['sekolah_id'=>4, 'nama_jurusan'=>'IPS (Ilmu Pengetahuan Sosial)', 'kode_jurusan'=>'IPS', 'kuota'=>100],
            ['sekolah_id'=>4, 'nama_jurusan'=>'Bahasa dan Sastra', 'kode_jurusan'=>'BHS', 'kuota'=>50],
            // SMA 5 (id=5) - Cendekia Utama
            ['sekolah_id'=>5, 'nama_jurusan'=>'IPA (Ilmu Pengetahuan Alam)', 'kode_jurusan'=>'IPA', 'kuota'=>120],
            ['sekolah_id'=>5, 'nama_jurusan'=>'IPS (Ilmu Pengetahuan Sosial)', 'kode_jurusan'=>'IPS', 'kuota'=>100],
            // SMA 6 (id=6) - Bhakti Persada
            ['sekolah_id'=>6, 'nama_jurusan'=>'IPA (Ilmu Pengetahuan Alam)', 'kode_jurusan'=>'IPA', 'kuota'=>90],
            ['sekolah_id'=>6, 'nama_jurusan'=>'IPS (Ilmu Pengetahuan Sosial)', 'kode_jurusan'=>'IPS', 'kuota'=>90],
            ['sekolah_id'=>6, 'nama_jurusan'=>'Agama Islam', 'kode_jurusan'=>'AGM', 'kuota'=>50],
            // SMK 7 (id=7) - Teknologi Mandiri
            ['sekolah_id'=>7, 'nama_jurusan'=>'Teknik Komputer dan Jaringan (TKJ)', 'kode_jurusan'=>'TKJ', 'kuota'=>72],
            ['sekolah_id'=>7, 'nama_jurusan'=>'Rekayasa Perangkat Lunak (RPL)', 'kode_jurusan'=>'RPL', 'kuota'=>72],
            ['sekolah_id'=>7, 'nama_jurusan'=>'Teknik Elektronika Industri', 'kode_jurusan'=>'TEI', 'kuota'=>72],
            ['sekolah_id'=>7, 'nama_jurusan'=>'Teknik Mesin', 'kode_jurusan'=>'TM', 'kuota'=>72],
            // SMK 8 (id=8) - Kriya Nusantara
            ['sekolah_id'=>8, 'nama_jurusan'=>'Desain Komunikasi Visual (DKV)', 'kode_jurusan'=>'DKV', 'kuota'=>72],
            ['sekolah_id'=>8, 'nama_jurusan'=>'Multimedia', 'kode_jurusan'=>'MM', 'kuota'=>72],
            ['sekolah_id'=>8, 'nama_jurusan'=>'Perhotelan dan Pariwisata', 'kode_jurusan'=>'HT', 'kuota'=>72],
            ['sekolah_id'=>8, 'nama_jurusan'=>'Tata Boga', 'kode_jurusan'=>'TB', 'kuota'=>36],
            // SMK 9 (id=9) - Bina Karya Indonesia
            ['sekolah_id'=>9, 'nama_jurusan'=>'Akuntansi dan Keuangan Lembaga', 'kode_jurusan'=>'AKL', 'kuota'=>72],
            ['sekolah_id'=>9, 'nama_jurusan'=>'Bisnis Daring dan Pemasaran (BDP)', 'kode_jurusan'=>'BDP', 'kuota'=>72],
            ['sekolah_id'=>9, 'nama_jurusan'=>'Otomatisasi Tata Kelola Perkantoran (OTKP)', 'kode_jurusan'=>'OTKP', 'kuota'=>72],
        ];

        foreach ($jurusans as $j) {
            DB::table('jurusans')->insert(array_merge($j, ['is_active'=>true, 'created_at'=>now(), 'updated_at'=>now()]));
        }

        // ===== METODE PEMBAYARAN =====
        // Insert satu per satu agar kolom nullable tidak mismatch
        DB::table('metode_pembayarans')->insert(['nama_metode'=>'Transfer BCA','tipe'=>'bank_transfer','nama_bank'=>'Bank BCA','no_rekening'=>'1234567890','atas_nama'=>'Yayasan Indonesia','instruksi'=>'Transfer ke rekening BCA atas nama Yayasan Indonesia. Sertakan kode pendaftaran pada berita transfer.','is_active'=>true,'urutan'=>1,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('metode_pembayarans')->insert(['nama_metode'=>'Transfer Mandiri','tipe'=>'bank_transfer','nama_bank'=>'Bank Mandiri','no_rekening'=>'1400012345678','atas_nama'=>'Yayasan Indonesia','instruksi'=>'Transfer ke rekening Mandiri atas nama Yayasan Indonesia. Sertakan kode pendaftaran pada berita transfer.','is_active'=>true,'urutan'=>2,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('metode_pembayarans')->insert(['nama_metode'=>'Transfer BNI','tipe'=>'bank_transfer','nama_bank'=>'Bank BNI','no_rekening'=>'0123456789','atas_nama'=>'Yayasan Indonesia','instruksi'=>'Transfer ke rekening BNI atas nama Yayasan Indonesia. Sertakan kode pendaftaran pada berita transfer.','is_active'=>true,'urutan'=>3,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('metode_pembayarans')->insert(['nama_metode'=>'Bayar Tunai (Cash)','tipe'=>'cash','nama_bank'=>null,'no_rekening'=>null,'atas_nama'=>null,'instruksi'=>'Bayar tunai di kantor Yayasan Indonesia, Jl. Pendidikan No. 1, Jakarta Selatan. Bawa kode pendaftaran dan cetak form ini. Jam operasional: Senin-Jumat 08.00-15.00 WIB.','is_active'=>true,'urutan'=>4,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('metode_pembayarans')->insert(['nama_metode'=>'Bayar via Midtrans','tipe'=>'otomatis','nama_bank'=>null,'no_rekening'=>null,'atas_nama'=>null,'instruksi'=>'Pembayaran otomatis via Midtrans. Mendukung berbagai metode: Transfer Bank, GoPay, OVO, DANA, Kartu Kredit, dan lainnya.','is_active'=>true,'urutan'=>5,'created_at'=>now(),'updated_at'=>now()]);

        // ===== TESTIMONI =====
        DB::table('testimonis')->insert([
            ['nama'=>'Budi Santoso', 'asal_sekolah'=>'SMK Teknologi Mandiri', 'tahun_masuk'=>'2024', 'isi_testimoni'=>'Proses pendaftaran sangat mudah dan cepat. Formulir online sangat membantu, tidak perlu antri panjang. Alhamdulillah diterima di jurusan RPL yang saya inginkan!', 'rating'=>5, 'is_active'=>true, 'urutan'=>1, 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'Siti Rahayu', 'asal_sekolah'=>'SMA Generasi Emas', 'tahun_masuk'=>'2024', 'isi_testimoni'=>'PPDB online ini sangat memudahkan orang tua seperti saya. Bisa daftar dari rumah, upload dokumen online, dan pantau status pendaftaran kapan saja.', 'rating'=>5, 'is_active'=>true, 'urutan'=>2, 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'Ahmad Fadhillah', 'asal_sekolah'=>'SMP Nusantara Bangsa', 'tahun_masuk'=>'2023', 'isi_testimoni'=>'Saya sangat terkesan dengan sistem PPDB ini. Notifikasi email langsung diterima setelah daftar. Admin juga responsif dalam mengkonfirmasi berkas.', 'rating'=>5, 'is_active'=>true, 'urutan'=>3, 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'Dewi Lestari', 'asal_sekolah'=>'SMA Cendekia Utama', 'tahun_masuk'=>'2024', 'isi_testimoni'=>'Sistem pembayaran via Midtrans sangat memudahkan. Bisa bayar pakai GoPay dari handphone, langsung dapat konfirmasi. Recommended!', 'rating'=>5, 'is_active'=>true, 'urutan'=>4, 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'Rizky Pratama', 'asal_sekolah'=>'SMK Kriya Nusantara', 'tahun_masuk'=>'2024', 'isi_testimoni'=>'Yayasan Indonesia sekolah terbaik pilihan keluarga kami. Fasilitas lengkap, guru profesional, dan sistem PPDB yang modern. Mantap!', 'rating'=>5, 'is_active'=>true, 'urutan'=>5, 'created_at'=>now(), 'updated_at'=>now()],
            ['nama'=>'Nurul Hidayah', 'asal_sekolah'=>'SMP Bina Insan Mulia', 'tahun_masuk'=>'2023', 'isi_testimoni'=>'Daftar PPDB online sangat praktis. Tidak perlu bolak-balik ke sekolah hanya untuk mengumpulkan berkas. Semua bisa dilakukan dari rumah.', 'rating'=>4, 'is_active'=>true, 'urutan'=>6, 'created_at'=>now(), 'updated_at'=>now()],
        ]);

        // ===== DATA DUMMY PENDAFTAR =====
        $this->seedDummyPendaftar();
    }

    private function seedDummyPendaftar(): void
    {
        $tahunAkademikId = 2; // 2025/2026
        $now = now();

        $dummyData = [
            // [sekolah_id, jurusan_id|null, jalur, status, nama, nisn, jk, email, phone, asal_sekolah, status_bayar]
            [7, 10, 'reguler', 'lunas', 'Andi Kurniawan', '0012345678', 'laki_laki', 'andi.k@gmail.com', '081234567890', 'SMP Negeri 1 Jakarta', 'sukses'],
            [4, 1, 'reguler', 'lunas', 'Sari Dewi Putri', '0023456789', 'perempuan', 'saridewi@gmail.com', '082345678901', 'SMP Negeri 2 Jakarta', 'sukses'],
            [8, 13, 'prestasi', 'lunas', 'Fajar Ramadhan', '0034567890', 'laki_laki', 'fajar.r@gmail.com', '083456789012', 'SMP Swasta Al-Azhar', 'sukses'],
            [5, 4, 'reguler', 'menunggu_pembayaran', 'Maya Sari', '0045678901', 'perempuan', 'mayasari@gmail.com', '084567890123', 'SMP Negeri 5 Bekasi', 'pending'],
            [9, 17, 'reguler', 'diterima', 'Doni Saputra', '0056789012', 'laki_laki', 'doni.s@gmail.com', '085678901234', 'SMP Swasta Budi Luhur', 'pending'],
            [1, null, 'reguler', 'diterima', 'Indah Permatasari', '0067890123', 'perempuan', 'indahpermata@gmail.com', '086789012345', 'SD Negeri 3 Jakarta', null],
            [7, 11, 'afirmasi', 'diproses', 'Rizal Maulana', '0078901234', 'laki_laki', 'rizal.m@gmail.com', '087890123456', 'SMP Negeri 8 Jakarta', null],
            [2, null, 'reguler', 'diproses', 'Fitria Ningsih', '0089012345', 'perempuan', 'fitrian@gmail.com', '088901234567', 'SD Islam Terpadu', null],
            [6, 6, 'reguler', 'lunas', 'Bagas Ardianto', '0090123456', 'laki_laki', 'bagas.a@gmail.com', '089012345678', 'SMP Muhammadiyah 2', 'sukses'],
            [8, 15, 'prestasi', 'menunggu_pembayaran', 'Lina Maharani', '0001234567', 'perempuan', 'linamah@gmail.com', '081122334455', 'SMP Negeri 11 Bogor', 'menunggu_verifikasi'],
            [3, null, 'pindahan', 'diterima', 'Wahyu Sejati', '0012344321', 'laki_laki', 'wahyu.s@gmail.com', '082233445566', 'SMP Negeri 3 Depok', null],
            [9, 18, 'reguler', 'lunas', 'Nadia Ratnasari', '0023455432', 'perempuan', 'nadiaratna@gmail.com', '083344556677', 'SMP Swasta Taruna', 'sukses'],
        ];

        foreach ($dummyData as $idx => $d) {
            [$sekolahId, $jurusanId, $jalur, $status, $nama, $nisn, $jk, $email, $phone, $asalSekolah, $statusBayar] = $d;

            $kode = 'PPDB25-' . strtoupper(substr(md5($nama . $idx), 0, 8));

            $tglSubmit = Carbon::now()->subDays(rand(5, 60));

            $pendaftaranId = DB::table('pendaftarans')->insertGetId([
                'kode_regis'         => $kode,
                'tahun_akademik_id'  => $tahunAkademikId,
                'sekolah_id'         => $sekolahId,
                'jurusan_id'         => $jurusanId,
                'jalur_pendaftaran'  => $jalur,
                'status'             => $status,
                'tanggal_submit'     => $tglSubmit,
                'tanggal_verifikasi' => in_array($status, ['diterima','menunggu_pembayaran','lunas']) ? $tglSubmit->addDays(1) : null,
                'dibuat_oleh'        => 'publik',
                'diverifikasi_oleh'  => in_array($status, ['diterima','menunggu_pembayaran','lunas']) ? 1 : null,
                'created_at'         => $tglSubmit,
                'updated_at'         => $tglSubmit,
            ]);

            DB::table('siswas')->insert([
                'pendaftaran_id' => $pendaftaranId,
                'nisn'           => $nisn,
                'nama_siswa'     => $nama,
                'jk'             => $jk,
                'phone'          => $phone,
                'email'          => $email,
                'agama'          => 'islam',
                'tempat_lahir'   => 'Jakarta',
                'tanggal_lahir'  => Carbon::now()->subYears(rand(13, 16))->format('Y-m-d'),
                'alamat'         => 'Jl. Contoh No. ' . rand(1, 99) . ', Jakarta',
                'asal_sekolah'   => $asalSekolah,
                'tahun_lulus'    => '2024',
                'nomor_ijazah'   => 'IJZ' . rand(100000, 999999),
                'created_at'     => $tglSubmit,
                'updated_at'     => $tglSubmit,
            ]);

            DB::table('wali_siswas')->insert([
                'pendaftaran_id' => $pendaftaranId,
                'nama_wali'      => 'Orang Tua ' . $nama,
                'hubungan'       => 'orang_tua',
                'jenis_wali'     => 'ayah',
                'pekerjaan'      => 'Wiraswasta',
                'notelp_wali'    => $phone,
                'email'          => $email,
                'created_at'     => $tglSubmit,
                'updated_at'     => $tglSubmit,
            ]);

            // Insert pembayaran jika ada
            if ($statusBayar) {
                $tipeMetode = $statusBayar === 'sukses' ? 1 : ($statusBayar === 'menunggu_verifikasi' ? 1 : 5);
                DB::table('pembayarans')->insert([
                    'pendaftaran_id'       => $pendaftaranId,
                    'metode_pembayaran_id' => $tipeMetode,
                    'nominal'              => 200000,
                    'order_id'             => 'INV' . strtoupper(substr(md5($kode), 0, 10)),
                    'status_pembayaran'    => $statusBayar,
                    'tanggal_pembayaran'   => $tglSubmit->addDays(2)->format('Y-m-d'),
                    'verifikasi_oleh'      => $statusBayar === 'sukses' ? 1 : null,
                    'verifikasi_tanggal'   => $statusBayar === 'sukses' ? $tglSubmit->addDays(3) : null,
                    'created_at'           => $tglSubmit->addDays(2),
                    'updated_at'           => $tglSubmit->addDays(2),
                ]);
            }
        }
    }
}
