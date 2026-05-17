@extends('admin.layouts.app')
@section('title','Testimoni')
@section('breadcrumb','Testimoni')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-star text-blue-600"></i> Kelola Testimoni</h1>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

  <!-- Kiri: Daftar + pending -->
  <div class="space-y-5">

    <!-- Pending Approval dari Siswa -->
    @if($pending->count() > 0)
    <div class="card overflow-hidden border-yellow-200">
      <div class="px-5 py-3 bg-yellow-50 border-b border-yellow-100 flex items-center justify-between">
        <h3 class="text-sm font-bold text-yellow-800 flex items-center gap-2"><i class="fa fa-clock"></i> Menunggu Persetujuan</h3>
        <span class="badge bg-yellow-100 text-yellow-800">{{ $pending->count() }}</span>
      </div>
      <div class="divide-y divide-gray-100">
        @foreach($pending as $t)
        <div class="p-4">
          <div class="flex items-start justify-between gap-3 mb-2">
            <div>
              <div class="font-semibold text-sm text-gray-800">{{ $t->nama }}</div>
              <div class="text-xs text-gray-500">{{ $t->asal_sekolah }} · {{ $t->tahun_masuk }}</div>
              <div class="flex mt-1">@for($i=0;$i<5;$i++)<i class="fa fa-star text-xs {{ $i<$t->rating?'text-yellow-400':'text-gray-200' }}"></i>@endfor</div>
            </div>
            <div class="flex gap-1 flex-shrink-0">
              <form action="{{ route('admin.testimoni.approve',$t) }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-success" title="Setujui"><i class="fa fa-check"></i> Setujui</button>
              </form>
              <form id="del-pt-{{ $t->id }}" action="{{ route('admin.testimoni.destroy',$t) }}" method="POST">@csrf @method('DELETE')</form>
              <button onclick="confirmDelete('del-pt-{{ $t->id }}')" class="btn btn-sm btn-danger" title="Tolak"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <p class="text-xs text-gray-600 bg-gray-50 rounded-lg p-3 leading-relaxed">"{{ $t->isi_testimoni }}"</p>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    <!-- Semua Testimoni -->
    <div class="card overflow-hidden">
      <div class="px-5 py-3 bg-gray-50 border-b text-sm font-bold text-gray-700">
        Semua Testimoni ({{ $testimonis->count() }})
      </div>
      <table class="table">
        <thead><tr><th>Nama</th><th>Rating</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($testimonis as $t)
          <tr>
            <td>
              <div class="font-semibold text-sm">{{ $t->nama }}</div>
              <div class="text-xs text-gray-400 truncate max-w-[140px]">{{ Str::limit($t->isi_testimoni, 50) }}</div>
            </td>
            <td>
              <div class="flex">@for($i=0;$i<5;$i++)<i class="fa fa-star text-xs {{ $i<$t->rating?'text-yellow-400':'text-gray-200' }}"></i>@endfor</div>
            </td>
            <td>
              <form action="{{ route('admin.testimoni.toggle',$t) }}" method="POST">
                @csrf
                <button type="submit" class="badge cursor-pointer transition hover:opacity-80 {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}"
                  title="{{ $t->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                  {{ $t->is_active ? 'Aktif' : 'Nonaktif' }}
                </button>
              </form>
            </td>
            <td>
              <form id="del-t-{{ $t->id }}" action="{{ route('admin.testimoni.destroy',$t) }}" method="POST">@csrf @method('DELETE')</form>
              <button onclick="confirmDelete('del-t-{{ $t->id }}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-gray-400 py-8 text-sm">Belum ada testimoni.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Kanan: Form tambah manual -->
  <div class="card p-5 h-fit">
    <div class="text-sm font-bold text-gray-700 mb-4">Tambah Testimoni Manual</div>
    <form action="{{ route('admin.testimoni.store') }}" method="POST" class="space-y-3">
      @csrf
      <div><label class="form-label">Nama <span class="req">*</span></label><input type="text" name="nama" class="form-input" required></div>
      <div><label class="form-label">Asal Sekolah</label><input type="text" name="asal_sekolah" class="form-input"></div>
      <div><label class="form-label">Tahun Masuk</label><input type="text" name="tahun_masuk" class="form-input" maxlength="10" placeholder="{{ date('Y') }}"></div>
      <div>
        <label class="form-label">Rating <span class="req">*</span></label>
        <select name="rating" class="form-input" required>
          @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} Bintang</option>@endfor
        </select>
      </div>
      <div><label class="form-label">Isi Testimoni <span class="req">*</span></label><textarea name="isi_testimoni" class="form-input" rows="4" required></textarea></div>
      <div><label class="form-label">Urutan</label><input type="number" name="urutan" class="form-input" value="0"></div>
      <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked id="ts_aktif" class="accent-blue-700"><label for="ts_aktif" class="text-sm">Langsung tampilkan di website</label></div>
      <button type="submit" class="btn btn-primary w-full justify-center"><i class="fa fa-plus"></i> Tambah Testimoni</button>
    </form>
  </div>

</div>
@endsection
