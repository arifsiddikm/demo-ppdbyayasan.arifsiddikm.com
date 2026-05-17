<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        return view('admin.master.metode.index', ['metodes' => MetodePembayaran::orderBy('urutan')->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_metode' => 'required|string|max:100',
            'tipe'        => 'required|in:bank_transfer,cash,otomatis',
            'urutan'      => 'nullable|integer',
        ]);
        MetodePembayaran::create([
            'nama_metode' => $request->nama_metode,
            'tipe'        => $request->tipe,
            'nama_bank'   => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama'   => $request->atas_nama,
            'instruksi'   => $request->instruksi,
            'urutan'      => $request->urutan ?? 0,
            'is_active'   => $request->boolean('is_active', true),
        ]);
        return back()->with('success', 'Metode pembayaran ditambahkan.');
    }

    public function update(Request $request, MetodePembayaran $metode)
    {
        $metode->update([
            'nama_metode' => $request->nama_metode,
            'tipe'        => $request->tipe,
            'nama_bank'   => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'atas_nama'   => $request->atas_nama,
            'instruksi'   => $request->instruksi,
            'urutan'      => $request->urutan ?? $metode->urutan,
            'is_active'   => $request->boolean('is_active'),
        ]);
        return back()->with('success', 'Metode pembayaran diperbarui.');
    }

    public function destroy(MetodePembayaran $metode)
    {
        $metode->delete();
        return back()->with('success', 'Metode dihapus.');
    }
}
