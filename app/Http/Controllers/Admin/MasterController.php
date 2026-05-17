<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TahunAkademik;
use App\Models\MetodePembayaran;
use App\Models\PengaturanWeb;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

// =============================================
class UserController extends Controller
{
    public function index()      { return view('admin.master.user.index', ['users' => User::latest()->get()]); }
    public function create()     { return view('admin.master.user.form',  ['user'  => null]); }
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,superadmin',
        ]);
        User::create(['name'=>$request->name,'email'=>$request->email,'password'=>Hash::make($request->password),'role'=>$request->role]);
        return redirect()->route('admin.user.index')->with('success','Admin berhasil ditambahkan.');
    }
    public function edit(User $user)  { return view('admin.master.user.form', compact('user')); }
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required','email', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:admin,superadmin',
            'password' => 'nullable|min:8|confirmed',
        ]);
        $data = ['name'=>$request->name,'email'=>$request->email,'role'=>$request->role];
        if ($request->filled('password')) $data['password'] = Hash::make($request->password);
        $user->update($data);
        return redirect()->route('admin.user.index')->with('success','Admin diperbarui.');
    }
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) return back()->withErrors(['error'=>'Tidak bisa hapus akun sendiri.']);
        $user->delete();
        return back()->with('success','Admin dihapus.');
    }
}

// =============================================
class TahunAkademikController extends Controller
{
    public function index() { return view('admin.master.tahun.index', ['tahuns' => TahunAkademik::latest()->get()]); }
    public function store(Request $request)
    {
        $request->validate(['nama_tahun'=>'required|string|max:20','tanggal_mulai_daftar'=>'nullable|date','tanggal_tutup_daftar'=>'nullable|date']);
        if ($request->boolean('is_active')) TahunAkademik::query()->update(['is_active'=>false]);
        TahunAkademik::create(['nama_tahun'=>$request->nama_tahun,'tanggal_mulai_daftar'=>$request->tanggal_mulai_daftar,'tanggal_tutup_daftar'=>$request->tanggal_tutup_daftar,'is_active'=>$request->boolean('is_active')]);
        return back()->with('success','Tahun akademik ditambahkan.');
    }
    public function setActive(TahunAkademik $tahun)
    {
        TahunAkademik::query()->update(['is_active'=>false]);
        $tahun->update(['is_active'=>true]);
        return back()->with('success','Tahun akademik diaktifkan.');
    }
    public function destroy(TahunAkademik $tahun) { $tahun->delete(); return back()->with('success','Tahun akademik dihapus.'); }
}

// =============================================
class MetodePembayaranController extends Controller
{
    public function index() { return view('admin.master.metode.index', ['metodes' => MetodePembayaran::orderBy('urutan')->get()]); }
    public function store(Request $request)
    {
        $request->validate(['nama_metode'=>'required|string|max:100','tipe'=>'required|in:bank_transfer,cash,otomatis','nama_bank'=>'nullable|string','no_rekening'=>'nullable|string','atas_nama'=>'nullable|string','instruksi'=>'nullable|string','urutan'=>'nullable|integer']);
        MetodePembayaran::create($request->only(['nama_metode','tipe','nama_bank','no_rekening','atas_nama','instruksi','urutan']) + ['is_active'=>$request->boolean('is_active',true)]);
        return back()->with('success','Metode pembayaran ditambahkan.');
    }
    public function update(Request $request, MetodePembayaran $metode)
    {
        $metode->update($request->only(['nama_metode','tipe','nama_bank','no_rekening','atas_nama','instruksi','urutan']) + ['is_active'=>$request->boolean('is_active')]);
        return back()->with('success','Metode pembayaran diperbarui.');
    }
    public function destroy(MetodePembayaran $metode) { $metode->delete(); return back()->with('success','Metode dihapus.'); }
}

// =============================================
class PengaturanController extends Controller
{
    public function index()
    {
        $setting = PengaturanWeb::first() ?? new PengaturanWeb();
        return view('admin.pengaturan.index', compact('setting'));
    }
    public function update(Request $request)
    {
        $setting = PengaturanWeb::first() ?? new PengaturanWeb();
        $data = $request->validate([
            'nama_yayasan'=>'required|string|max:100',
            'singkatan_yayasan'=>'nullable|string|max:20',
            'alamat_yayasan'=>'nullable|string',
            'email_yayasan'=>'nullable|email',
            'phone_yayasan'=>'nullable|string|max:20',
            'website_yayasan'=>'nullable|url',
            'deskripsi_yayasan'=>'nullable|string',
            'tagline'=>'nullable|string|max:200',
            'ppdb_aktif'=>'nullable|boolean',
            'pengumuman'=>'nullable|string',
            'logo_yayasan'=>'nullable|image|max:2048',
            'favicon_yayasan'=>'nullable|image|max:512',
        ]);
        if ($request->hasFile('logo_yayasan')) {
            if ($setting->logo_yayasan) Storage::disk('public')->delete($setting->logo_yayasan);
            $data['logo_yayasan'] = $request->file('logo_yayasan')->store('yayasan','public');
        }
        if ($request->hasFile('favicon_yayasan')) {
            if ($setting->favicon_yayasan) Storage::disk('public')->delete($setting->favicon_yayasan);
            $data['favicon_yayasan'] = $request->file('favicon_yayasan')->store('yayasan','public');
        }
        $data['ppdb_aktif'] = $request->boolean('ppdb_aktif');
        if ($setting->exists) { $setting->update($data); } else { PengaturanWeb::create($data); }
        return back()->with('success','Pengaturan web berhasil disimpan.');
    }
}

// =============================================
class TestimoniController extends Controller
{
    public function index() { return view('admin.master.testimoni.index', ['testimonis' => Testimoni::orderBy('urutan')->get()]); }
    public function store(Request $request)
    {
        $request->validate(['nama'=>'required|string|max:100','isi_testimoni'=>'required|string','rating'=>'required|integer|min:1|max:5','asal_sekolah'=>'nullable|string|max:100','tahun_masuk'=>'nullable|string|max:10','urutan'=>'nullable|integer']);
        Testimoni::create($request->only(['nama','isi_testimoni','rating','asal_sekolah','tahun_masuk','urutan'])+['is_active'=>$request->boolean('is_active',true)]);
        return back()->with('success','Testimoni ditambahkan.');
    }
    public function destroy(Testimoni $testimoni) { $testimoni->delete(); return back()->with('success','Testimoni dihapus.'); }
}
