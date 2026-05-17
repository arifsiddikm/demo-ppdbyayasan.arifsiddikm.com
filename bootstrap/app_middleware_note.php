<?php
/**
 * CATATAN: Daftarkan AdminMiddleware di bootstrap/app.php (Laravel 11)
 * atau di app/Http/Kernel.php (Laravel 10)
 *
 * === LARAVEL 11 (bootstrap/app.php) ===
 * Tambahkan di dalam ->withMiddleware(function (Middleware $middleware) { ... }):
 *
 *   $middleware->alias([
 *       'admin' => \App\Http\Middleware\AdminMiddleware::class,
 *   ]);
 *
 * === LARAVEL 10 (app/Http/Kernel.php) ===
 * Tambahkan di $routeMiddleware:
 *
 *   'admin' => \App\Http\Middleware\AdminMiddleware::class,
 *
 * Routes sudah menggunakan class langsung (bukan alias), jadi ini opsional.
 * Tapi kalau ingin pakai ->middleware('admin') cukup daftarkan alias di atas.
 */
