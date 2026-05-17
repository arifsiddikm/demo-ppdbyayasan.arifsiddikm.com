# CLAUDE PROMPT — PPDB Yayasan Indonesia

> PRD lengkap untuk membangun ulang website PPDB Yayasan Indonesia dari awal menggunakan Claude AI.
> Upload file ini ke Claude dan minta build website sesuai spesifikasi di bawah.

---

## Konsep Umum

Bangun website **PPDB Yayasan Indonesia** — sistem penerimaan peserta didik baru terpusat untuk 1 yayasan dengan beberapa sekolah (SMP, SMA, SMK).

**Tech Stack:**
- Laravel 13 (MVC biasa, tanpa Filament)
- MySQL
- Tailwind CSS via CDN (`cdn.tailwindcss.com`) — **PENTING: gunakan CSS native murni di `<style>` tag, jangan `@apply`**
- SweetAlert2 untuk confirm dialog
- PHPMailer untuk email (SMTP Hostinger)
- DomPDF untuk generate PDF
- Midtrans via Riplabs untuk payment gateway

**Aturan umum:**
- Kirim file dalam ZIP dengan struktur folder Laravel yang benar (siap merge)
- Buatkan logo SVG, favicon, meta SEO di setiap halaman
- Desain modern, nuansa putih-biru (Kurikulum Merdeka)
- Form input, button, checkbox, radio harus ada CSS styling yang proper
- Logout dan konfirmasi penting pakai SweetAlert
- Button autofill di halaman login admin (bukan auto-login, tetap klik manual)
- Security: CSRF, validasi backend, sanitasi input

---

## Database & Migrations

Gabungkan `users`, `password_reset_tokens`, `sessions` dalam 1 file migration.

**Tabel utama:**
- `pengaturan_webs` — nama yayasan, singkatan, alamat, tagline, logo, ppdb_aktif, pengumuman
- `sekolahs` — nama, singkatan, tingkatan (SMP/SMA/SMK), npsn, alamat, kota, phone, email, deskripsi, akreditasi, kuota, tahun_berdiri, logo, urutan, is_active
- `jurusans` — sekolah_id (FK), nama_jurusan, kode_jurusan, kuota, is_active
- `tahun_akademiks` — nama_tahun, tanggal_mulai_daftar, tanggal_tutup_daftar, is_active
- `metode_pembayarans` — nama_metode, tipe (bank_transfer/cash/otomatis), nama_bank, no_rekening, atas_nama, instruksi, is_active, urutan
- `pendaftarans` — kode_regis, tahun_akademik_id, sekolah_id, jurusan_id, jalur_pendaftaran (reguler/prestasi/afirmasi/pindahan), ket_jalur_pendaftaran, status (diproses/diterima/ditolak/menunggu_pembayaran/lunas), tanggal_submit, tanggal_verifikasi, dibuat_oleh (publik/admin), diverifikasi_oleh
- `siswas` — pendaftaran_id, nisn (10 digit), nama_siswa, jk, phone, email, agama, tempat_lahir, tanggal_lahir, alamat, asal_sekolah, tahun_lulus, nomor_ijazah
- `wali_siswas` — pendaftaran_id, nama_wali, hubungan, jenis_wali (ayah/ibu/wali), pekerjaan, notelp_wali, email
- `dokumens` — pendaftaran_id, jenis_dokumen (pas_foto/kk/akta/ijazah/skhun/stl/lampiran_jalur), file_path, original_name
- `pembayarans` — pendaftaran_id, metode_pembayaran_id, nominal, order_id, snap_token, status_pembayaran (pending/menunggu_verifikasi/sukses), proof_path, tanggal_pembayaran, verifikasi_oleh, verifikasi_tanggal
- `testimonis` — pendaftaran_id, nama, asal_sekolah, tahun_masuk, isi_testimoni, rating, is_active, urutan

**Seeder:** 9 sekolah (3 SMP, 3 SMA, 3 SMK), jurusan untuk SMA & SMK, 5 metode bayar (BCA/Mandiri/BNI/Cash/Midtrans), 2 admin, 6 testimoni, 12 dummy pendaftar dengan berbagai status.

---

## Halaman Client (Publik)

### Landing Page (`/`)
- **Navbar:** Logo YI, menu Beranda/Sekolah/Cara Daftar/Cek Status, button Login Admin (kecil, outline), button Daftar Sekarang (primary)
- **Hero:** Background foto sekolah, gradient overlay, headline bold, 2 CTA button (Daftar/Cek Status), badge statistik (jumlah sekolah, pendaftar, dll), grid pattern background
- **Section Cara Daftar:** Timeline di kiri (6 langkah, auto-slide 2 detik, bisa diklik), box animasi di kanan berubah sesuai langkah aktif
- **Section Sekolah:** Grid card sekolah dengan logo, badge akreditasi, jurusan, tombol Daftar Sekarang
- **Section Testimoni:** Auto-slide loop (carousel)
- **CTA Box:** Gradasi biru, eye-catching
- **Footer:** Logo, deskripsi, link cepat, kontak, sosmed

### Form Pendaftaran (`/daftar`)
Multi-step form dengan progress bar, 6 langkah:
1. **Sekolah** — pilih sekolah (dropdown/card), pilih jurusan (muncul jika SMA/SMK)
2. **Data Diri** — NISN (10 digit wajib), nama, JK, tempat/tgl lahir, agama, alamat, phone, email
3. **Jalur** — pilih jalur (reguler/prestasi/afirmasi/pindahan), keterangan & upload lampiran jika diperlukan, asal sekolah, tahun lulus, nomor ijazah
4. **Orang Tua** — data wali (minimal 1, bisa tambah), nama, jenis wali, pekerjaan, notelp, email
5. **Dokumen** — upload pas foto, KK, akta (wajib), ijazah, SKHUN, STL (opsional)
6. **Review** — tampilkan semua data sebelum submit

**Penting:**
- Navigasi prev/next dengan validasi per step
- Data tidak hilang saat validasi gagal (withInput)
- Upload file tanpa MIME validation (hindari dependency `finfo` PHP extension)
- Gunakan `$file->move()` langsung ke storage, bypass Flysystem

### Halaman Selesai Daftar (`/daftar/selesai?kode=xxx`)
Tampilkan kode registrasi, status, instruksi selanjutnya.

### Cek Status & Pembayaran (`/cek-status`)
- Form cari dengan kode registrasi
- Tampilkan detail pendaftaran, status dengan badge warna
- Jika status `menunggu_pembayaran`: tampilkan pilihan metode bayar
  - Bank Transfer: pilih bank, instruksi, form upload bukti TF
  - Midtrans: tombol bayar → popup Snap Midtrans → redirect otomatis setelah sukses (gunakan `onSuccess` callback + `location.href`)
- Jika lunas: tombol download PDF formulir

---

## Halaman Admin (`/admin`)

### Login (`/admin/login`)
- Form email + password
- Button autofill (isi email & password, tidak auto-submit)

### Dashboard
- Statistik: total pendaftar, menunggu verifikasi, lunas, revenue
- Grafik pendaftaran (Chart.js)
- Tabel pendaftaran terbaru

### Kelola Pendaftaran (`/admin/pendaftaran`)
- Tabel dengan filter status, sekolah, pencarian
- Aksi: Lihat Detail, Terima/Tolak, Konfirmasi Pembayaran, Download PDF, Hapus
- Export CSV/PDF

### Detail Pendaftaran (`/admin/pendaftaran/{id}`)
- Semua data siswa, wali, dokumen (bisa preview/download)
- Tombol konfirmasi terima/tolak berkas (SweetAlert confirm)
- Section pembayaran: upload bukti TF admin, toggle selesaikan pendaftaran, konfirmasi bayar
- Email terkirim otomatis saat konfirmasi

### Buat Pendaftaran (Admin) (`/admin/pendaftaran/buat`)
- Form multi-step sama persis dengan client (6 langkah)
- Tersimpan dengan `dibuat_oleh = 'admin'`

### Master Data
- **Sekolah:** CRUD + kelola jurusan per sekolah
- **Tahun Akademik:** CRUD + set aktif
- **Metode Pembayaran:** CRUD
- **Pengguna:** CRUD admin
- **Testimoni:** approve/toggle/hapus
- **Pengaturan Web:** update info yayasan, logo, pengumuman

### Profil Admin
- Update nama, email, password, foto profil

---

## Email (PHPMailer SMTP)

**Konfigurasi:**
```
Host: smtp.hostinger.com
Port: 465
Encryption: ssl
Username: noreply@arifsiddikm.com
Password: SatuDua345!!
From: noreply@arifsiddikm.com
Admin: arifsiddikmuharam@gmail.com
```

**Trigger email:**
1. **Siswa daftar** → email ke siswa (kode, instruksi selanjutnya) + notif ke admin
2. **Admin terima berkas** → email ke siswa (berkas diterima, segera bayar, link cek status dengan kode di URL)
3. **Siswa upload bukti TF** → email ke siswa (konfirmasi terima) + notif ke admin
4. **Pembayaran Midtrans sukses** → email ke siswa (selesai + PDF lampiran) + notif ke admin
5. **Admin konfirmasi pembayaran manual** → email ke siswa (selesai + PDF lampiran)

Di frontend: tampilkan loading badge saat proses kirim email.

---

## Payment Gateway (Midtrans via Riplabs)

**Env:**
```env
MIDTRANS_CLIENT_KEY=SB-Mid-client-YQ6BjX9sqs3xGMHr
MIDTRANS_SERVER_KEY=SB-Mid-server-3RAh5nBbKZtdE-x1eVKvUm-i
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SNAP_JS_URL=https://app.sandbox.midtrans.com/snap/snap.js
MIDTRANS_ORDER_PREFIX=INV
RIPLABS_KEY=a9s8d7bas98d7981273xbasduky8b71o247bai8f
RIPLABS_SNAPTOKEN_URL=https://restapi.riplabs.co.id/snaptokenppdbyayasan/getsnaptoken
MIDTRANS_CALLBACK_KEY=a9s8d7bas98d7981273xbasduky8b71o247bai8f
ADMIN_WHATSAPP=6289514392694
```

**Flow Riplabs:**
```php
// POST ke RIPLABS_SNAPTOKEN_URL
$fields = [
  'key'        => env('RIPLABS_KEY'),
  'order_id'   => 'PPDBYAYASAN' . $orderId,
  'total_harga'=> $nominal,
  'nama'       => $namaSiswa,
  'email'      => $emailSiswa,
  'namaproduk' => 'Biaya Pendaftaran PPDB',
];
// Response: { "status": true, "snaptoken": "xxx" }
```

**Callback Midtrans:** Route tanpa CSRF middleware. Mapping `transaction_status` → update `status_pembayaran`.

**Frontend:** Setelah `snap.pay()` → `onSuccess` → fetch `/bayar/payment-success` → `location.href` redirect ke cek-status.

---

## Fitur Tambahan

- **PDF Formulir:** Generate dengan DomPDF, berisi semua data pendaftaran, bisa download dan dikirim via email sebagai lampiran
- **Security:** CSRF token, validasi backend semua input, middleware admin auth, sanitasi XSS
- **File Upload:** Bypass `finfo` — gunakan `$file->move()` langsung, validasi hanya `file` dan `max` (tanpa `mimes:`)
- **Storage:** Jalankan `php artisan storage:link` setelah deploy

---

## File yang Wajib Dibuat

Kirim dalam ZIP dengan struktur folder Laravel:
```
app/Http/Controllers/PendaftaranController.php
app/Http/Controllers/PembayaranController.php
app/Http/Controllers/Admin/*.php
app/Models/*.php
app/Services/MailService.php
app/Services/MidtransService.php
app/Services/PdfService.php
database/migrations/*.php
database/seeders/DatabaseSeeder.php
resources/views/layouts/app.blade.php
resources/views/client/*.blade.php
resources/views/admin/**/*.blade.php
routes/web.php
config/midtrans.php
.env.example
README.md
```
