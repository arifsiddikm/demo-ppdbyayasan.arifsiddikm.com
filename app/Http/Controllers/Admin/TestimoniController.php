<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    public function index()
    {
        $testimonis = Testimoni::orderBy('is_active','desc')->orderBy('urutan')->get();
        $pending    = $testimonis->filter(fn($t) => !$t->is_active && $t->pendaftaran_id);
        $aktif      = $testimonis->filter(fn($t) => $t->is_active);
        $adminInput = $testimonis->filter(fn($t) => !$t->is_active && !$t->pendaftaran_id);

        return view('admin.master.testimoni.index', compact('testimonis','pending','aktif','adminInput'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:100',
            'isi_testimoni' => 'required|string',
            'rating'        => 'required|integer|min:1|max:5',
            'asal_sekolah'  => 'nullable|string|max:100',
            'tahun_masuk'   => 'nullable|string|max:10',
            'urutan'        => 'nullable|integer',
        ]);
        Testimoni::create([
            'nama'          => $request->nama,
            'isi_testimoni' => $request->isi_testimoni,
            'rating'        => $request->rating,
            'asal_sekolah'  => $request->asal_sekolah,
            'tahun_masuk'   => $request->tahun_masuk,
            'urutan'        => $request->urutan ?? 0,
            'is_active'     => $request->boolean('is_active', true),
        ]);
        return back()->with('success', 'Testimoni ditambahkan.');
    }

    public function toggle(Testimoni $testimoni)
    {
        $testimoni->update(['is_active' => !$testimoni->is_active]);
        $status = $testimoni->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Testimoni berhasil {$status}.");
    }

    public function approve(Testimoni $testimoni)
    {
        $testimoni->update(['is_active' => true]);
        return back()->with('success', 'Testimoni disetujui dan ditampilkan di website.');
    }

    public function destroy(Testimoni $testimoni)
    {
        $testimoni->delete();
        return back()->with('success', 'Testimoni dihapus.');
    }
}
