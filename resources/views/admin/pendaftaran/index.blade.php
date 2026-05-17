@extends('admin.layouts.app')
@section('title','Data Pendaftaran')
@section('breadcrumb','Data Pendaftaran')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-clipboard-list text-blue-600"></i> Data Pendaftaran</h1>
  <div class="flex gap-2 flex-wrap">
    <a href="{{ route('admin.pendaftaran.export.pdf', request()->query()) }}" target="_blank" class="btn btn-sm btn-danger" title="Export PDF"><i class="fa fa-file-pdf"></i> PDF</a>
    <a href="{{ route('admin.pendaftaran.export.excel', request()->query()) }}" class="btn btn-sm btn-success" title="Export Excel/CSV"><i class="fa fa-file-excel"></i> Excel</a>
    <a href="{{ route('admin.pendaftaran.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Buat Pendaftaran</a>
  </div>
</div>

<!-- Filter -->
<div class="card p-4 mb-5">
  <form method="GET" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[180px]">
      <label class="form-label">Cari</label>
      <input type="text" name="search" class="form-input" placeholder="Nama / Kode..." value="{{ request('search') }}">
    </div>
    <div class="min-w-[140px]">
      <label class="form-label">Status</label>
      <select name="status" class="form-input">
        <option value="">Semua Status</option>
        @foreach(['diproses'=>'Diproses','diterima'=>'Diterima','ditolak'=>'Ditolak','menunggu_pembayaran'=>'Tunggu Bayar','lunas'=>'Lunas'] as $v=>$l)
        <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
      </select>
    </div>
    <div class="min-w-[160px]">
      <label class="form-label">Sekolah</label>
      <select name="sekolah_id" class="form-input">
        <option value="">Semua Sekolah</option>
        @foreach($sekolahs as $s)<option value="{{ $s->id }}" {{ request('sekolah_id')==$s->id?'selected':'' }}>{{ $s->singkatan }}</option>@endforeach
      </select>
    </div>
    <div class="min-w-[140px]">
      <label class="form-label">Jalur</label>
      <select name="jalur" class="form-input">
        <option value="">Semua Jalur</option>
        @foreach(['reguler'=>'Reguler','prestasi'=>'Prestasi','afirmasi'=>'Afirmasi','pindahan'=>'Pindahan'] as $v=>$l)
        <option value="{{ $v }}" {{ request('jalur')==$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
      <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-secondary"><i class="fa fa-rotate"></i></a>
    </div>
  </form>
</div>

<div class="card overflow-hidden">
  <table class="table">
    <thead>
      <tr>
        <th>Kode</th><th>Nama Siswa</th><th>Sekolah / Jurusan</th><th>Jalur</th>
        <th>Status Daftar</th><th>Status Bayar</th><th>Tgl Daftar</th><th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($pendaftarans as $p)
      @php $bayar = $p->pembayarans->sortByDesc('id')->first(); @endphp
      <tr>
        <td><a href="{{ route('admin.pendaftaran.show',$p) }}" class="text-blue-600 font-mono text-xs font-bold hover:underline">{{ $p->kode_regis }}</a></td>
        <td><div class="font-semibold text-sm">{{ $p->siswa?->nama_siswa??'—' }}</div><div class="text-xs text-gray-400">{{ $p->siswa?->email }}</div></td>
        <td><div class="text-xs font-medium">{{ $p->sekolah?->singkatan }}</div><div class="text-xs text-gray-400">{{ $p->jurusan?->kode_jurusan??'-' }}</div></td>
        <td><span class="text-xs capitalize text-gray-600">{{ $p->jalur_pendaftaran }}</span></td>
        <td><span class="badge {{ $p->badge_color }}">{{ $p->label_status }}</span></td>
        <td>@if($bayar)<span class="badge {{ $bayar->badge_color }}">{{ $bayar->label_status }}</span>@else<span class="text-xs text-gray-400">—</span>@endif</td>
        <td class="text-xs text-gray-500">{{ $p->tanggal_submit?->format('d/m/Y') }}</td>
        <td>
          <div class="flex gap-1">
            <a href="{{ route('admin.pendaftaran.show',$p) }}" class="btn btn-sm btn-primary" title="Detail"><i class="fa fa-eye"></i></a>
            <form id="del-{{ $p->id }}" action="{{ route('admin.pendaftaran.destroy',$p) }}" method="POST">@csrf @method('DELETE')</form>
            <button type="button" onclick="confirmDelete('del-{{ $p->id }}')" class="btn btn-sm btn-danger" title="Hapus"><i class="fa fa-trash"></i></button>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="text-center text-gray-400 py-10 text-sm">Tidak ada data pendaftaran.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($pendaftarans->hasPages())
  <div class="px-4 py-3 border-t border-gray-100">{{ $pendaftarans->links() }}</div>
  @endif
</div>
@endsection
