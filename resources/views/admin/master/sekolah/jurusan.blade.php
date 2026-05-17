{{-- resources/views/admin/master/sekolah/jurusan.blade.php --}}
@extends('admin.layouts.app')
@section('title','Jurusan — '.$sekolah->nama_sekolah)
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-list text-blue-600"></i> Jurusan — {{ $sekolah->nama_sekolah }}</h1>
  <a href="{{ route('admin.sekolah.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
  <div>
    <div class="card overflow-hidden">
      <div class="px-5 py-3 bg-gray-50 border-b text-sm font-bold text-gray-700">Daftar Jurusan</div>
      <table class="table">
        <thead><tr><th>Jurusan</th><th>Kode</th><th>Kuota</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($sekolah->jurusans as $j)
          <tr>
            <td class="font-medium text-sm">{{ $j->nama_jurusan }}</td>
            <td class="text-xs font-mono">{{ $j->kode_jurusan }}</td>
            <td class="text-sm">{{ $j->kuota }}</td>
            <td><span class="badge {{ $j->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $j->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
            <td>
              <form id="del-j-{{ $j->id }}" action="{{ route('admin.sekolah.jurusan.destroy',$j) }}" method="POST">@csrf @method('DELETE')</form>
              <button onclick="confirmDelete('del-j-{{ $j->id }}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-gray-400 py-6">Belum ada jurusan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div>
    <div class="card p-5">
      <div class="text-sm font-bold text-gray-700 mb-4">Tambah Jurusan</div>
      <form action="{{ route('admin.sekolah.jurusan.store',$sekolah) }}" method="POST" class="space-y-3">
        @csrf
        <div><label class="form-label">Nama Jurusan <span class="req">*</span></label><input type="text" name="nama_jurusan" class="form-input" required></div>
        <div><label class="form-label">Kode Jurusan</label><input type="text" name="kode_jurusan" class="form-input" maxlength="10"></div>
        <div><label class="form-label">Kuota <span class="req">*</span></label><input type="number" name="kuota" class="form-input" value="0" min="0" required></div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
      </form>
    </div>
  </div>
</div>
@endsection
