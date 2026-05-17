<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\SekolahController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TahunAkademikController;
use App\Http\Controllers\Admin\MetodePembayaranController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\TestimoniController;

// =============================================
// CLIENT
// =============================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/daftar', [PendaftaranController::class, 'create'])->name('daftar');
Route::post('/daftar', [PendaftaranController::class, 'store'])->name('daftar.store');
Route::get('/daftar/selesai', [PendaftaranController::class, 'finish'])->name('pendaftaran.finish');
Route::get('/api/jurusan', [PendaftaranController::class, 'getJurusan'])->name('api.jurusan');

Route::get('/cek-status', [PembayaranController::class, 'cekStatus'])->name('cek.status');
Route::post('/bayar/store', [PembayaranController::class, 'storePembayaran'])->name('bayar.store');
Route::post('/bayar/snap-token', [PembayaranController::class, 'requestSnapToken'])->name('bayar.snapToken');
Route::post('/bayar/payment-success', [PembayaranController::class, 'handlePaymentSuccess'])->name('bayar.paymentSuccess');
Route::get('/bayar/download-pdf', [PembayaranController::class, 'downloadPdf'])->name('bayar.pdf');
Route::post('/testimoni', [PembayaranController::class, 'submitTestimoni'])->name('testimoni.submit');

// Midtrans callback CSRF exempt
Route::post('/bayar/callback', [PembayaranController::class, 'midtransCallback'])
    ->name('bayar.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// =============================================
// ADMIN AUTH
// =============================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
        Route::post('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');

        // Pendaftaran
        Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
            Route::get('/', [AdminPendaftaranController::class, 'index'])->name('index');
            Route::get('/export-pdf', [AdminPendaftaranController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export-excel', [AdminPendaftaranController::class, 'exportExcel'])->name('export.excel');
            Route::get('/buat', [PendaftaranController::class, 'adminCreate'])->name('create');
            Route::post('/buat', [PendaftaranController::class, 'adminStore'])->name('store');
            Route::get('/{pendaftaran}', [AdminPendaftaranController::class, 'show'])->name('show');
            Route::post('/{pendaftaran}/terima', [AdminPendaftaranController::class, 'konfirmasiDiterima'])->name('terima');
            Route::post('/{pendaftaran}/tolak', [AdminPendaftaranController::class, 'konfirmasiDitolak'])->name('tolak');
            Route::post('/{pendaftaran}/upload-bukti', [AdminPendaftaranController::class, 'uploadBuktiTf'])->name('upload.bukti');
            Route::get('/{pendaftaran}/pdf', [AdminPendaftaranController::class, 'downloadPdf'])->name('pdf');
            Route::delete('/{pendaftaran}', [AdminPendaftaranController::class, 'destroy'])->name('destroy');
        });

        Route::post('/pembayaran/{pembayaran}/konfirmasi', [AdminPendaftaranController::class, 'konfirmasiPembayaran'])->name('pembayaran.konfirmasi');

        // Master: Sekolah
        Route::prefix('sekolah')->name('sekolah.')->group(function () {
            Route::get('/', [SekolahController::class, 'index'])->name('index');
            Route::get('/buat', [SekolahController::class, 'create'])->name('create');
            Route::post('/', [SekolahController::class, 'store'])->name('store');
            Route::get('/{sekolah}/edit', [SekolahController::class, 'edit'])->name('edit');
            Route::put('/{sekolah}', [SekolahController::class, 'update'])->name('update');
            Route::delete('/{sekolah}', [SekolahController::class, 'destroy'])->name('destroy');
            Route::get('/{sekolah}/jurusan', [SekolahController::class, 'jurusan'])->name('jurusan');
            Route::post('/{sekolah}/jurusan', [SekolahController::class, 'storeJurusan'])->name('jurusan.store');
            Route::put('/jurusan/{jurusan}', [SekolahController::class, 'updateJurusan'])->name('jurusan.update');
            Route::delete('/jurusan/{jurusan}', [SekolahController::class, 'destroyJurusan'])->name('jurusan.destroy');
        });

        // Master: Tahun Akademik
        Route::prefix('tahun-akademik')->name('tahun.')->group(function () {
            Route::get('/', [TahunAkademikController::class, 'index'])->name('index');
            Route::post('/', [TahunAkademikController::class, 'store'])->name('store');
            Route::post('/{tahun}/aktifkan', [TahunAkademikController::class, 'setActive'])->name('aktif');
            Route::delete('/{tahun}', [TahunAkademikController::class, 'destroy'])->name('destroy');
        });

        // Master: Metode Pembayaran
        Route::prefix('metode-pembayaran')->name('metode.')->group(function () {
            Route::get('/', [MetodePembayaranController::class, 'index'])->name('index');
            Route::post('/', [MetodePembayaranController::class, 'store'])->name('store');
            Route::put('/{metode}', [MetodePembayaranController::class, 'update'])->name('update');
            Route::delete('/{metode}', [MetodePembayaranController::class, 'destroy'])->name('destroy');
        });

        // Master: User
        Route::prefix('pengguna')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/buat', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Testimoni
        Route::prefix('testimoni')->name('testimoni.')->group(function () {
            Route::get('/', [TestimoniController::class, 'index'])->name('index');
            Route::post('/', [TestimoniController::class, 'store'])->name('store');
            Route::post('/{testimoni}/toggle', [TestimoniController::class, 'toggle'])->name('toggle');
            Route::post('/{testimoni}/approve', [TestimoniController::class, 'approve'])->name('approve');
            Route::delete('/{testimoni}', [TestimoniController::class, 'destroy'])->name('destroy');
        });

        // Pengaturan Web
        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
        Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    });
});
