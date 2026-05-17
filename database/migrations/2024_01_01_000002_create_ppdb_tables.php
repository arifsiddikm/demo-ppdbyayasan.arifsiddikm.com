<?php
// =========================================================
// MIGRATION: 2024_01_01_000002_create_ppdb_tables.php
// =========================================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== SEKOLAHS =====
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah');
            $table->string('singkatan')->nullable();
            $table->enum('tingkatan', ['SMP', 'SMA', 'SMK']);
            $table->string('npsn', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('logo')->nullable();
            $table->string('akreditasi', 5)->default('A');
            $table->integer('kuota')->default(0);
            $table->integer('tahun_berdiri')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // ===== JURUSANS =====
        Schema::create('jurusans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->string('nama_jurusan');
            $table->string('kode_jurusan', 10)->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('kuota')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ===== TAHUN AKADEMIK =====
        Schema::create('tahun_akademiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tahun'); // e.g. "2025/2026"
            $table->date('tanggal_mulai_daftar')->nullable();
            $table->date('tanggal_tutup_daftar')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // ===== METODE PEMBAYARAN =====
        Schema::create('metode_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_metode'); // BCA, Mandiri, Cash, Midtrans
            $table->enum('tipe', ['bank_transfer', 'cash', 'otomatis'])->default('bank_transfer');
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('atas_nama')->nullable();
            $table->text('instruksi')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // ===== PENDAFTARANS =====
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_regis', 30)->unique();
            $table->foreignId('tahun_akademik_id')->nullable()->constrained('tahun_akademiks')->nullOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->onDelete('cascade');
            $table->foreignId('jurusan_id')->nullable()->constrained('jurusans')->nullOnDelete();
            $table->enum('jalur_pendaftaran', ['reguler', 'prestasi', 'afirmasi', 'pindahan'])->default('reguler');
            $table->text('ket_jalur_pendaftaran')->nullable();
            $table->enum('status', ['diproses', 'diterima', 'ditolak', 'menunggu_pembayaran', 'lunas'])->default('diproses');
            $table->string('catatan_admin')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_submit')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->string('dibuat_oleh')->default('publik'); // 'publik' or 'admin'
            $table->timestamps();
        });

        // ===== SISWAS =====
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->string('nisn', 20)->nullable();
            $table->string('nama_siswa', 100);
            $table->enum('jk', ['laki_laki', 'perempuan'])->default('laki_laki');
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('agama', ['islam', 'protestan', 'katolik', 'hindu', 'budha', 'khonghucu'])->default('islam');
            $table->string('tempat_lahir', 60)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('asal_sekolah', 100)->nullable();
            $table->string('tahun_lulus', 4)->nullable();
            $table->string('nomor_ijazah', 100)->nullable();
            $table->timestamps();
        });

        // ===== WALI SISWAS =====
        Schema::create('wali_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->string('nama_wali', 100);
            $table->enum('hubungan', ['orang_tua', 'saudara_kandung', 'saudara_keluarga', 'wali'])->default('orang_tua');
            $table->string('jenis_wali')->default('ayah'); // ayah/ibu/lainnya
            $table->string('pekerjaan', 60)->nullable();
            $table->string('notelp_wali', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('nik', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        // ===== DOKUMENS =====
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->string('jenis_dokumen', 50); // pas_foto, kk, akta, ijazah, skhun, stl, lampiran_jalur
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });

        // ===== PEMBAYARANS =====
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->onDelete('cascade');
            $table->foreignId('metode_pembayaran_id')->nullable()->constrained('metode_pembayarans')->nullOnDelete();
            $table->integer('nominal')->default(0);
            $table->string('order_id', 60)->unique()->nullable();
            $table->text('snap_token')->nullable();
            $table->enum('status_pembayaran', ['pending', 'menunggu_verifikasi', 'sukses', 'gagal', 'kadaluarsa'])->default('pending');
            $table->string('proof_path')->nullable(); // bukti transfer
            $table->date('tanggal_pembayaran')->nullable();
            $table->foreignId('verifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verifikasi_tanggal')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();
        });

        // ===== TESTIMONIS =====
        Schema::create('testimonis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('asal_sekolah')->nullable();
            $table->string('tahun_masuk')->nullable();
            $table->text('isi_testimoni');
            $table->integer('rating')->default(5);
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonis');
        Schema::dropIfExists('pembayarans');
        Schema::dropIfExists('dokumens');
        Schema::dropIfExists('wali_siswas');
        Schema::dropIfExists('siswas');
        Schema::dropIfExists('pendaftarans');
        Schema::dropIfExists('metode_pembayarans');
        Schema::dropIfExists('tahun_akademiks');
        Schema::dropIfExists('jurusans');
        Schema::dropIfExists('sekolahs');
    }
};
