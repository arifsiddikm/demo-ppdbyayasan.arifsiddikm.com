{{-- resources/views/admin/master/sekolah/form.blade.php --}}
@extends('admin.layouts.app')
@section('title', $sekolah ? 'Edit Sekolah' : 'Tambah Sekolah')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-school text-blue-600"></i> {{ $sekolah ? 'Edit' : 'Tambah' }} Sekolah</h1>
  <a href="{{ route('admin.sekolah.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>
<div class="max-w-2xl">
  <form action="{{ $sekolah ? route('admin.sekolah.update',$sekolah) : route('admin.sekolah.store') }}" method="POST" enctype="multipart/form-data">
    @csrf @if($sekolah) @method('PUT') @endif
    <div class="card p-6 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2"><label class="form-label">Nama Sekolah <span class="req">*</span></label><input type="text" name="nama_sekolah" class="form-input" value="{{ old('nama_sekolah',$sekolah?->nama_sekolah) }}" required></div>
        <div><label class="form-label">Singkatan</label><input type="text" name="singkatan" class="form-input" value="{{ old('singkatan',$sekolah?->singkatan) }}"></div>
        <div><label class="form-label">Tingkatan <span class="req">*</span></label>
          <select name="tingkatan" class="form-input" required>
            @foreach(['SMP','SMA','SMK'] as $t)<option value="{{ $t }}" {{ old('tingkatan',$sekolah?->tingkatan)===$t?'selected':'' }}>{{ $t }}</option>@endforeach
          </select>
        </div>
        <div><label class="form-label">NPSN</label><input type="text" name="npsn" class="form-input" value="{{ old('npsn',$sekolah?->npsn) }}"></div>
        <div><label class="form-label">Akreditasi</label><input type="text" name="akreditasi" class="form-input" value="{{ old('akreditasi',$sekolah?->akreditasi,'A') }}" maxlength="5"></div>
        <div><label class="form-label">Kota</label><input type="text" name="kota" class="form-input" value="{{ old('kota',$sekolah?->kota) }}"></div>
        <div><label class="form-label">Kuota <span class="req">*</span></label><input type="number" name="kuota" class="form-input" value="{{ old('kuota',$sekolah?->kuota,0) }}" min="0" required></div>
        <div><label class="form-label">Phone</label><input type="text" name="phone" class="form-input" value="{{ old('phone',$sekolah?->phone) }}"></div>
        <div><label class="form-label">Email</label><input type="email" name="email" class="form-input" value="{{ old('email',$sekolah?->email) }}"></div>
        <div><label class="form-label">Tahun Berdiri</label><input type="number" name="tahun_berdiri" class="form-input" value="{{ old('tahun_berdiri',$sekolah?->tahun_berdiri) }}" min="1900" max="{{ date('Y') }}"></div>
        <div><label class="form-label">Urutan</label><input type="number" name="urutan" class="form-input" value="{{ old('urutan',$sekolah?->urutan,0) }}"></div>
        <div class="md:col-span-2"><label class="form-label">Alamat</label><textarea name="alamat" class="form-input" rows="2">{{ old('alamat',$sekolah?->alamat) }}</textarea></div>
        <div class="md:col-span-2"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-input" rows="3">{{ old('deskripsi',$sekolah?->deskripsi) }}</textarea></div>
        <div class="md:col-span-2">
          <label class="form-label">Logo Sekolah</label>
          <div class="file-input-wrapper">
            <input type="file" name="logo" accept="image/*">
            <div class="file-input-display"><i class="fa fa-image text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">Upload logo (PNG/JPG, maks 2MB)</div></div>
          </div>
          @if($sekolah?->logo)<div class="mt-2"><img src="{{ asset('storage/'.$sekolah->logo) }}" class="h-12 rounded object-contain"></div>@endif
        </div>
        <div class="md:col-span-2 flex items-center gap-2">
          <input type="checkbox" name="is_active" value="1" id="is_active" class="accent-blue-700" {{ old('is_active',$sekolah?->is_active ?? true) ? 'checked' : '' }}>
          <label for="is_active" class="text-sm font-medium text-gray-700">Sekolah Aktif (tampil di PPDB)</label>
        </div>
      </div>
      <div class="pt-4 border-t flex gap-3">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
        <a href="{{ route('admin.sekolah.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </div>
  </form>
</div>
@endsection
