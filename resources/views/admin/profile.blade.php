@extends('admin.layouts.app')
@section('title','Profil Saya')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5"><h1><i class="fa fa-user-circle text-blue-600"></i> Profil Saya</h1></div>
<div class="max-w-lg">
  <div class="card p-6 mb-5 text-center">
    <img src="{{ $user->foto_url }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-3 border-4 border-blue-100" alt="">
    <div class="font-bold text-gray-900">{{ $user->name }}</div>
    <div class="text-sm text-gray-500 capitalize">{{ $user->role }}</div>
    <div class="text-xs text-gray-400 mt-1">{{ $user->email }}</div>
  </div>
  <div class="card p-6">
    <div class="text-sm font-bold text-gray-700 mb-4">Update Profil</div>
    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <div><label class="form-label">Nama <span class="req">*</span></label><input type="text" name="name" class="form-input" value="{{ old('name',$user->name) }}" required></div>
      <div><label class="form-label">WhatsApp</label><input type="text" name="whatsapp" class="form-input" value="{{ old('whatsapp',$user->whatsapp) }}" placeholder="08xxxxxxxxxx"></div>
      <div>
        <label class="form-label">Foto Profil</label>
        <div class="file-input-wrapper"><input type="file" name="foto" accept="image/*"><div class="file-input-display"><i class="fa fa-camera text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">Upload foto profil (maks 2MB)</div></div></div>
      </div>
      <div class="pt-3 border-t">
        <div class="text-xs font-bold text-gray-500 uppercase mb-3">Ubah Password</div>
        <div class="space-y-3">
          <div><label class="form-label">Password Baru (kosongkan jika tidak diubah)</label><input type="password" name="password" class="form-input" minlength="8"></div>
          <div><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-input"></div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>
@endsection
