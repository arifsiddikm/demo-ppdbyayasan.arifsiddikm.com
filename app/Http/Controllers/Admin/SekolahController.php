<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use App\Models\Jurusan;
use App\Models\TahunAkademik;
use App\Models\MetodePembayaran;
use App\Models\User;
use App\Models\PengaturanWeb;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

// =============================================
// SEKOLAH CONTROLLER
// =============================================
class SekolahController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::withCount('pendaftarans')->orderBy('urutan')->get();
        return view('admin.master.sekolah.index', compact('sekolahs'));
    }
    public function create() { return view('admin.master.sekolah.form', ['sekolah'=>null]); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_sekolah'=>'required|string|max:100','singkatan'=>'nullable|string|max:20',
            'tingkatan'=>'required|in:SMP,SMA,SMK','npsn'=>'nullable|string|max:20',
            'alamat'=>'nullable|string','kota'=>'nullable|string|max:60',
            'phone'=>'nullable|string|max:20','email'=>'nullable|email',
            'website'=>'nullable|url','deskripsi'=>'nullable|string',
            'akreditasi'=>'nullable|string|max:5','kuota'=>'required|integer|min:0',
            'tahun_berdiri'=>'nullable|integer','is_active'=>'boolean','urutan'=>'nullable|integer',
            'logo'=>'nullable|image|max:2048',
        ]);
        if ($request->hasFile('logo')) $data['logo'] = $request->file('logo')->store('sekolah/logo','public');
        Sekolah::create($data);
        return redirect()->route('admin.sekolah.index')->with('success','Sekolah berhasil ditambahkan.');
    }
    public function edit(Sekolah $sekolah) { return view('admin.master.sekolah.form', compact('sekolah')); }
    public function update(Request $request, Sekolah $sekolah)
    {
        $data = $request->validate([
            'nama_sekolah'=>'required|string|max:100','singkatan'=>'nullable|string|max:20',
            'tingkatan'=>'required|in:SMP,SMA,SMK','npsn'=>'nullable|string|max:20',
            'alamat'=>'nullable|string','kota'=>'nullable|string|max:60',
            'phone'=>'nullable|string|max:20','email'=>'nullable|email',
            'deskripsi'=>'nullable|string','akreditasi'=>'nullable|string|max:5',
            'kuota'=>'required|integer|min:0','tahun_berdiri'=>'nullable|integer',
            'is_active'=>'nullable|boolean','urutan'=>'nullable|integer',
            'logo'=>'nullable|image|max:2048',
        ]);
        if ($request->hasFile('logo')) {
            if ($sekolah->logo) Storage::disk('public')->delete($sekolah->logo);
            $data['logo'] = $request->file('logo')->store('sekolah/logo','public');
        }
        $data['is_active'] = $request->boolean('is_active');
        $sekolah->update($data);
        return redirect()->route('admin.sekolah.index')->with('success','Sekolah berhasil diperbarui.');
    }
    public function destroy(Sekolah $sekolah)
    {
        if ($sekolah->logo) Storage::disk('public')->delete($sekolah->logo);
        $sekolah->delete();
        return back()->with('success','Sekolah dihapus.');
    }

    // JURUSAN
    public function jurusan(Sekolah $sekolah) { return view('admin.master.sekolah.jurusan', compact('sekolah')); }
    public function storeJurusan(Request $request, Sekolah $sekolah)
    {
        $request->validate(['nama_jurusan'=>'required|string|max:100','kode_jurusan'=>'nullable|string|max:10','kuota'=>'required|integer|min:0']);
        Jurusan::create(['sekolah_id'=>$sekolah->id,'nama_jurusan'=>$request->nama_jurusan,'kode_jurusan'=>$request->kode_jurusan,'kuota'=>$request->kuota,'is_active'=>true]);
        return back()->with('success','Jurusan ditambahkan.');
    }
    public function updateJurusan(Request $request, Jurusan $jurusan)
    {
        $request->validate(['nama_jurusan'=>'required|string|max:100','kode_jurusan'=>'nullable|string|max:10','kuota'=>'required|integer|min:0','is_active'=>'nullable|boolean']);
        $jurusan->update(['nama_jurusan'=>$request->nama_jurusan,'kode_jurusan'=>$request->kode_jurusan,'kuota'=>$request->kuota,'is_active'=>$request->boolean('is_active',true)]);
        return back()->with('success','Jurusan diperbarui.');
    }
    public function destroyJurusan(Jurusan $jurusan) { $jurusan->delete(); return back()->with('success','Jurusan dihapus.'); }
}
