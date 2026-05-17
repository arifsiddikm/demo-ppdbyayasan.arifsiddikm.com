<?php
// =========================================================
// MIGRATION: 2024_01_01_000001_create_users_and_system_tables.php
// =========================================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== USERS =====
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['superadmin', 'admin'])->default('admin');
            $table->string('foto_profil')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // ===== PASSWORD RESET TOKENS =====
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // ===== SESSIONS =====
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // ===== PENGATURAN WEB =====
        Schema::create('pengaturan_webs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_yayasan')->default('Yayasan Indonesia');
            $table->string('singkatan_yayasan')->nullable();
            $table->text('alamat_yayasan')->nullable();
            $table->string('email_yayasan')->nullable();
            $table->string('phone_yayasan')->nullable();
            $table->string('website_yayasan')->nullable();
            $table->string('logo_yayasan')->nullable();
            $table->string('favicon_yayasan')->nullable();
            $table->text('deskripsi_yayasan')->nullable();
            $table->string('tagline')->nullable();
            $table->string('hero_bg_image')->nullable();
            $table->boolean('ppdb_aktif')->default(true);
            $table->text('pengumuman')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('pengaturan_webs');
    }
};
