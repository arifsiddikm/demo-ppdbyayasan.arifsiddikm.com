{{-- resources/views/admin/master/tahun/index.blade.php --}}
@extends('admin.layouts.app')
@section('title','Tahun Akademik')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5"><h1><i class="fa fa-calendar-alt text-blue-600"></i> Tahun Akademik</h1></div>
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
  <div class="card overflow-hidden">
    <table class="table">
      <thead><tr><th>Tahun</th><th>Mulai</th><th>Tutup</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($tahuns as $t)
        <tr>
          <td class="font-bold">{{ $t->nama_tahun }}</td>
          <td class="text-xs">{{ $t->tanggal_mulai_daftar?->format('d/m/Y') ?? '—' }}</td>
          <td class="text-xs">{{ $t->tanggal_tutup_daftar?->format('d/m/Y') ?? '—' }}</td>
          <td><span class="badge {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $t->is_active ? 'Aktif' : '' }}</span></td>
          <td>
            <div class="flex gap-1">
              @if(!$t->is_active)<form action="{{ route('admin.tahun.aktif',$t) }}" method="POST">@csrf<button class="btn btn-sm btn-success"><i class="fa fa-check"></i> Aktifkan</button></form>@endif
              <form id="del-ta-{{ $t->id }}" action="{{ route('admin.tahun.destroy',$t) }}" method="POST">@csrf @method('DELETE')</form>
              <button onclick="confirmDelete('del-ta-{{ $t->id }}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card p-5">
    <div class="text-sm font-bold text-gray-700 mb-4">Tambah Tahun Akademik</div>
    <form action="{{ route('admin.tahun.store') }}" method="POST" class="space-y-3">
      @csrf
      <div><label class="form-label">Nama Tahun <span class="req">*</span></label><input type="text" name="nama_tahun" class="form-input" placeholder="2026/2027" required></div>
      <div><label class="form-label">Tanggal Mulai Daftar</label><input type="date" name="tanggal_mulai_daftar" class="form-input"></div>
      <div><label class="form-label">Tanggal Tutup Daftar</label><input type="date" name="tanggal_tutup_daftar" class="form-input"></div>
      <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" id="ta_aktif" class="accent-blue-700"><label for="ta_aktif" class="text-sm">Jadikan aktif</label></div>
      <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
    </form>
  </div>
</div>
@endsection
