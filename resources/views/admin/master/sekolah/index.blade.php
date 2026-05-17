{{-- resources/views/admin/master/sekolah/index.blade.php --}}
@extends('admin.layouts.app')
@section('title','Data Sekolah')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-school text-blue-600"></i> Sekolah & Jurusan</h1>
  <a href="{{ route('admin.sekolah.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah Sekolah</a>
</div>
<div class="card overflow-hidden">
  <table class="table">
    <thead><tr><th>Sekolah</th><th>Tingkatan</th><th>Kota</th><th>Kuota</th><th>Pendaftar</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
      @foreach($sekolahs as $s)
      <tr>
        <td>
          <div class="font-semibold text-sm">{{ $s->nama_sekolah }}</div>
          <div class="text-xs text-gray-400">{{ $s->singkatan }} · {{ $s->akreditasi }}</div>
        </td>
        <td><span class="badge {{ $s->tingkatan==='SMP'?'bg-blue-100 text-blue-800':($s->tingkatan==='SMA'?'bg-green-100 text-green-800':'bg-orange-100 text-orange-800') }}">{{ $s->tingkatan }}</span></td>
        <td class="text-xs text-gray-600">{{ $s->kota }}</td>
        <td class="text-sm font-medium">{{ $s->kuota }}</td>
        <td class="text-sm">{{ $s->pendaftarans_count }}</td>
        <td><span class="badge {{ $s->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
        <td>
          <div class="flex gap-1">
            <a href="{{ route('admin.sekolah.jurusan',$s) }}" class="btn btn-sm btn-secondary" title="Jurusan"><i class="fa fa-list"></i></a>
            <a href="{{ route('admin.sekolah.edit',$s) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fa fa-pen"></i></a>
            <form id="del-s-{{ $s->id }}" action="{{ route('admin.sekolah.destroy',$s) }}" method="POST">@csrf @method('DELETE')</form>
            <button onclick="confirmDelete('del-s-{{ $s->id }}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
