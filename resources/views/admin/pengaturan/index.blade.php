{{-- resources/views/admin/pengaturan/index.blade.php --}}
@extends('admin.layouts.app')
@section('title','Pengaturan Web')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5"><h1><i class="fa fa-sliders text-blue-600"></i> Pengaturan Web Yayasan</h1></div>
<div class="max-w-2xl">
  <form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card p-6 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2"><label class="form-label">Nama Yayasan <span class="req">*</span></label><input type="text" name="nama_yayasan" class="form-input" value="{{ old('nama_yayasan',$setting->nama_yayasan) }}" required></div>
        <div><label class="form-label">Singkatan</label><input type="text" name="singkatan_yayasan" class="form-input" value="{{ old('singkatan_yayasan',$setting->singkatan_yayasan) }}"></div>
        <div><label class="form-label">Tagline</label><input type="text" name="tagline" class="form-input" value="{{ old('tagline',$setting->tagline) }}"></div>
        <div><label class="form-label">Email</label><input type="email" name="email_yayasan" class="form-input" value="{{ old('email_yayasan',$setting->email_yayasan) }}"></div>
        <div><label class="form-label">Phone</label><input type="text" name="phone_yayasan" class="form-input" value="{{ old('phone_yayasan',$setting->phone_yayasan) }}"></div>
        <div class="md:col-span-2"><label class="form-label">Alamat</label><textarea name="alamat_yayasan" class="form-input" rows="2">{{ old('alamat_yayasan',$setting->alamat_yayasan) }}</textarea></div>
        <div class="md:col-span-2"><label class="form-label">Deskripsi</label><textarea name="deskripsi_yayasan" class="form-input" rows="3">{{ old('deskripsi_yayasan',$setting->deskripsi_yayasan) }}</textarea></div>
        <div class="md:col-span-2"><label class="form-label">Pengumuman (ditampilkan di homepage)</label><textarea name="pengumuman" class="form-input" rows="2">{{ old('pengumuman',$setting->pengumuman) }}</textarea></div>
        <div>
          <label class="form-label">Logo Yayasan</label>
          <div class="file-input-wrapper"><input type="file" name="logo_yayasan" accept="image/*"><div class="file-input-display"><i class="fa fa-image text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">Upload logo</div></div></div>
          @if($setting->logo_yayasan)<img src="{{ asset('storage/'.$setting->logo_yayasan) }}" class="h-10 mt-2 rounded">@endif
        </div>
        <div>
          <label class="form-label">Favicon</label>
          <div class="file-input-wrapper"><input type="file" name="favicon_yayasan" accept="image/*"><div class="file-input-display"><i class="fa fa-image text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">Upload favicon (32x32)</div></div></div>
        </div>
        <div class="md:col-span-2 flex items-center gap-2 pt-2">
          <input type="checkbox" name="ppdb_aktif" value="1" id="ppdb_aktif" class="accent-blue-700" {{ old('ppdb_aktif',$setting->ppdb_aktif) ? 'checked' : '' }}>
          <label for="ppdb_aktif" class="text-sm font-medium text-gray-700">PPDB Sedang Dibuka (pendaftaran aktif)</label>
        </div>
      </div>
      <div class="pt-4 border-t">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Pengaturan</button>
      </div>
    </div>
  </form>
</div>
@endsection
