# PPDB Yayasan Indonesia

Website Penerimaan Peserta Didik Baru (PPDB) terpusat untuk 1 yayasan dengan beberapa sekolah (SMP, SMA, SMK). Siswa dapat mendaftar online, upload dokumen, cek status, dan melakukan pembayaran.

🌐 **Live Demo:** [demo-ppdbyayasan.arifsiddikm.com](https://demo-ppdbyayasan.arifsiddikm.com)

---

## Tech Stack

- **Backend:** PHP 8.3 + Laravel 13
- **Database:** MySQL
- **Frontend:** Tailwind CSS CDN · SweetAlert2 · CKEditor
- **Payment:** Midtrans (via Riplabs) + Bank Transfer Manual + Cash
- **Email:** PHPMailer (SMTP Hostinger)
- **PDF:** DomPDF

---

## Fitur

**Client (Publik)**
- Landing page: hero, cara daftar (timeline interaktif), daftar sekolah, testimoni, CTA
- Form pendaftaran multi-step (6 langkah): Sekolah → Data Diri → Jalur → Orang Tua → Dokumen → Review
- Cek status pendaftaran & pembayaran via kode registrasi
- Pembayaran: Bank Transfer manual, Cash, Midtrans (GoPay, OVO, DANA, Kartu Kredit, dll)
- Upload bukti transfer
- Download formulir PDF setelah pendaftaran selesai
- Notifikasi email otomatis di setiap tahap

**Admin Panel** (`/admin`)
- Dashboard: statistik pendaftar, revenue, grafik
- Kelola pendaftaran: verifikasi berkas, konfirmasi pembayaran, upload bukti TF
- Bantu daftar siswa dari admin panel (form multi-step)
- CRUD Master: Sekolah + Jurusan, Tahun Akademik, Metode Pembayaran, Pengguna, Testimoni
- Pengaturan web yayasan
- Export data PDF & Excel
- Manajemen profil & foto profil admin

---

## Instalasi

```bash
# 1. Clone repo
git clone https://github.com/arifsiddikm/ppdb-yayasan.git
cd ppdb-yayasan

# 2. Install dependencies
composer install

# 3. Setup .env
cp file env to .env and setting your password
php artisan key:generate

# 4. Setup database MySQL di .env lalu jalankan migrasi
php artisan migrate
php artisan db:seed

# 5. Storage link
php artisan storage:link

# 6. Cache config (untuk production)
php artisan config:cache
php artisan route:cache

# 7. Jalankan server
php artisan serve
```

Akses di `http://localhost:8000`

---

## Login Admin

```
URL   : http://localhost:8000/admin/login
Email : superadmin@ppdb.id
Pass  : Admin123!!
```

---

## Konfigurasi .env Penting

```env
APP_URL=https://your-domain.com
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ppdb_yayasan
DB_USERNAME=root
DB_PASSWORD=

MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
ADMIN_EMAIL=admin@yourdomain.com

MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_IS_PRODUCTION=false
RIPLABS_KEY=your-riplabs-key
RIPLABS_SNAPTOKEN_URL=https://restapi.riplabs.co.id/snaptokenppdbyayasan/getsnaptoken
```

---

### Support me on

<a href="https://saweria.co/arifsiddikm" target="_blank"><img src="https://user-images.githubusercontent.com/26188697/180601310-e82c63e4-412b-4c36-b7b5-7ba713c80380.png" alt="Sawer me" height="41" width="174"></a>
