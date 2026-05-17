<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('testimonis', function (Blueprint $table) {
            $table->foreignId('pendaftaran_id')->nullable()->after('id')->constrained('pendaftarans')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('testimonis', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Pendaftaran::class);
            $table->dropColumn('pendaftaran_id');
        });
    }
};
