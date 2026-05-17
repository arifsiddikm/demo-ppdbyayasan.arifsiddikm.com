<?php
namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Testimoni;
use App\Models\PengaturanWeb;
use App\Models\Pendaftaran;
use App\Models\TahunAkademik;

class HomeController extends Controller
{
    public function index()
    {
        $setting  = PengaturanWeb::getSetting();
        $sekolahs = Sekolah::with(['jurusans' => fn($q) => $q->where('is_active', 1)])
            ->where('is_active', 1)->orderBy('urutan')->get();
        $testimonis  = Testimoni::where('is_active', 1)->orderBy('urutan')->get();
        $tahunAktif  = TahunAkademik::where('is_active', 1)->first();
        $totalDaftar = Pendaftaran::count();
        $totalLunas  = Pendaftaran::where('status','lunas')->count();

        return view('client.home', compact('setting','sekolahs','testimonis','tahunAktif','totalDaftar','totalLunas'));
    }
}
