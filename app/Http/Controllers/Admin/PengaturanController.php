<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $request->validate([
            'nama_yayasan'      => 'required|string|max:100',
            'singkatan_yayasan' => 'nullable|string|max:20',
            'alamat_yayasan'    => 'nullable|string',
            'email_yayasan'     => 'nullable|email',
            'phone_yayasan'     => 'nullable|string|max:20',
            'website_yayasan'   => 'nullable|url',
            'deskripsi_yayasan' => 'nullable|string',
            'tagline'           => 'nullable|string|max:200',
            'ppdb_aktif'        => 'nullable|boolean',
            'pengumuman'        => 'nullable|string',
            'logo_yayasan'      => 'nullable|image|max:2048',
            'favicon_yayasan'   => 'nullable|image|max:512',
        ]);

        $data = $request->only([
            'nama_yayasan','singkatan_yayasan','alamat_yayasan','email_yayasan',
            'phone_yayasan','website_yayasan','deskripsi_yayasan','tagline','pengumuman',
        ]);
        $data['ppdb_aktif'] = $request->boolean('ppdb_aktif');

        if ($request->hasFile('logo_yayasan')) {
            if ($setting->logo_yayasan) Storage::disk('public')->delete($setting->logo_yayasan);
            $data['logo_yayasan'] = $request->file('logo_yayasan')->store('yayasan', 'public');
        }
        if ($request->hasFile('favicon_yayasan')) {
            if ($setting->favicon_yayasan) Storage::disk('public')->delete($setting->favicon_yayasan);
            $data['favicon_yayasan'] = $request->file('favicon_yayasan')->store('yayasan', 'public');
        }

        if ($setting->exists) {
            $setting->update($data);
        } else {
            PengaturanWeb::create($data);
        }

        return back()->with('success', 'Pengaturan web berhasil disimpan.');
    }
}
