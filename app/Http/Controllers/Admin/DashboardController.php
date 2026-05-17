<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use App\Models\Sekolah;
use App\Models\TahunAkademik;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunAkademik::where('is_active', 1)->first();

        $stats = [
            'total_daftar'    => Pendaftaran::count(),
            'diproses'        => Pendaftaran::where('status','diproses')->count(),
            'diterima'        => Pendaftaran::where('status','diterima')->count(),
            'menunggu_bayar'  => Pendaftaran::where('status','menunggu_pembayaran')->count(),
            'lunas'           => Pendaftaran::where('status','lunas')->count(),
            'ditolak'         => Pendaftaran::where('status','ditolak')->count(),
        ];

        $revenueTotal = Pembayaran::where('status_pembayaran','sukses')->sum('nominal');
        $revenueMonth = Pembayaran::where('status_pembayaran','sukses')
            ->whereMonth('tanggal_pembayaran', now()->month)->sum('nominal');

        $pendingVerif = Pembayaran::where('status_pembayaran','menunggu_verifikasi')->count();

        // Per sekolah
        $perSekolah = Pendaftaran::select('sekolah_id', DB::raw('count(*) as total'))
            ->with('sekolah:id,nama_sekolah,tingkatan')
            ->groupBy('sekolah_id')
            ->get();

        // Chart: pendaftaran per bulan (tahun ini)
        $chartBulan = Pendaftaran::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('count(*) as total')
            )->whereYear('created_at', date('Y'))
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) $chartData[] = $chartBulan[$i] ?? 0;

        // Pendaftaran terbaru
        $recentPendaftaran = Pendaftaran::with(['siswa','sekolah'])
            ->latest()->limit(8)->get();

        // Pembayaran menunggu verifikasi
        $bayarPending = Pembayaran::with(['pendaftaran.siswa','pendaftaran.sekolah','metodePembayaran'])
            ->where('status_pembayaran','menunggu_verifikasi')
            ->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats','revenueTotal','revenueMonth','pendingVerif',
            'perSekolah','chartData','recentPendaftaran','bayarPending','tahunAktif'
        ));
    }
}
