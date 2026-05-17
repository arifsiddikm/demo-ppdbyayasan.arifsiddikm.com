{{-- resources/views/admin/master/metode/index.blade.php --}}
@extends('admin.layouts.app')
@section('title','Metode Pembayaran')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5"><h1><i class="fa fa-wallet text-blue-600"></i> Metode Pembayaran</h1></div>
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
  <div class="card overflow-hidden">
    <table class="table">
      <thead><tr><th>Nama</th><th>Tipe</th><th>No. Rek</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($metodes as $m)
        <tr>
          <td class="font-semibold text-sm">{{ $m->nama_metode }}</td>
          <td><span class="badge {{ $m->tipe==='otomatis'?'bg-blue-100 text-blue-700':($m->tipe==='cash'?'bg-green-100 text-green-700':'bg-gray-100 text-gray-600') }}">{{ ucfirst($m->tipe) }}</span></td>
          <td class="text-xs font-mono">{{ $m->no_rekening ?? '—' }}</td>
          <td><span class="badge {{ $m->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $m->is_active ? 'Aktif' : 'Off' }}</span></td>
          <td>
            <form id="del-m-{{ $m->id }}" action="{{ route('admin.metode.destroy',$m) }}" method="POST">@csrf @method('DELETE')</form>
            <button onclick="confirmDelete('del-m-{{ $m->id }}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card p-5">
    <div class="text-sm font-bold text-gray-700 mb-4">Tambah Metode</div>
    <form action="{{ route('admin.metode.store') }}" method="POST" class="space-y-3">
      @csrf
      <div><label class="form-label">Nama <span class="req">*</span></label><input type="text" name="nama_metode" class="form-input" required></div>
      <div><label class="form-label">Tipe <span class="req">*</span></label>
        <select name="tipe" class="form-input" required>
          <option value="bank_transfer">Bank Transfer</option><option value="cash">Cash / Tunai</option><option value="otomatis">Otomatis (Midtrans)</option>
        </select>
      </div>
      <div><label class="form-label">Nama Bank</label><input type="text" name="nama_bank" class="form-input"></div>
      <div><label class="form-label">No. Rekening</label><input type="text" name="no_rekening" class="form-input"></div>
      <div><label class="form-label">Atas Nama</label><input type="text" name="atas_nama" class="form-input"></div>
      <div><label class="form-label">Instruksi</label><textarea name="instruksi" class="form-input" rows="2"></textarea></div>
      <div><label class="form-label">Urutan</label><input type="number" name="urutan" class="form-input" value="0"></div>
      <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="accent-blue-700"><label class="text-sm">Aktif</label></div>
      <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
    </form>
  </div>
</div>
@endsection
