<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class TahunAkademikController extends Controller
{
    public function index()
    {
        return view('admin.master.tahun.index', ['tahuns' => TahunAkademik::latest()->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun'           => 'required|string|max:20',
            'tanggal_mulai_daftar' => 'nullable|date',
            'tanggal_tutup_daftar' => 'nullable|date',
        ]);
        if ($request->boolean('is_active')) {
            TahunAkademik::query()->update(['is_active' => false]);
        }
        TahunAkademik::create([
            'nama_tahun'           => $request->nama_tahun,
            'tanggal_mulai_daftar' => $request->tanggal_mulai_daftar,
            'tanggal_tutup_daftar' => $request->tanggal_tutup_daftar,
            'is_active'            => $request->boolean('is_active'),
        ]);
        return back()->with('success', 'Tahun akademik ditambahkan.');
    }

    public function setActive(TahunAkademik $tahun)
    {
        TahunAkademik::query()->update(['is_active' => false]);
        $tahun->update(['is_active' => true]);
        return back()->with('success', 'Tahun akademik diaktifkan.');
    }

    public function destroy(TahunAkademik $tahun)
    {
        $tahun->delete();
        return back()->with('success', 'Tahun akademik dihapus.');
    }
}
