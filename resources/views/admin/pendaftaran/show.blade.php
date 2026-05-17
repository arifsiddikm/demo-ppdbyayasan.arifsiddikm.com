@extends('admin.layouts.app')
@section('title','Detail Pendaftaran — '.$pendaftaran->kode_regis)
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-file-lines text-blue-600"></i> Detail Pendaftaran</h1>
  <div class="flex gap-2 flex-wrap">
    <a href="{{ route('admin.pendaftaran.pdf',$pendaftaran) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="fa fa-file-pdf"></i> PDF</a>
    <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
  <!-- Left: Info Utama -->
  <div class="xl:col-span-2 space-y-5">

    <!-- Kode & Status -->
    <div class="card p-5">
      <div class="flex items-start justify-between flex-wrap gap-3 mb-4">
        <div>
          <div class="text-xs text-gray-400 mb-1">Kode Pendaftaran</div>
          <div class="text-2xl font-extrabold text-blue-800 tracking-wider font-mono">{{ $pendaftaran->kode_regis }}</div>
        </div>
        <span class="badge text-sm px-4 py-1.5 {{ $pendaftaran->badge_color }}">{{ $pendaftaran->label_status }}</span>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
        <div class="bg-gray-50 rounded-lg p-3"><div class="text-gray-400 mb-0.5">Sekolah</div><div class="font-semibold">{{ $pendaftaran->sekolah?->nama_sekolah }}</div></div>
        <div class="bg-gray-50 rounded-lg p-3"><div class="text-gray-400 mb-0.5">Jurusan</div><div class="font-semibold">{{ $pendaftaran->jurusan?->nama_jurusan ?? '—' }}</div></div>
        <div class="bg-gray-50 rounded-lg p-3"><div class="text-gray-400 mb-0.5">Jalur</div><div class="font-semibold capitalize">{{ $pendaftaran->jalur_pendaftaran }}</div></div>
        <div class="bg-gray-50 rounded-lg p-3"><div class="text-gray-400 mb-0.5">Tgl Daftar</div><div class="font-semibold">{{ $pendaftaran->tanggal_submit?->format('d/m/Y') }}</div></div>
      </div>
    </div>

    <!-- Data Siswa -->
    <div class="card overflow-hidden">
      <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 text-sm font-bold text-gray-700 flex items-center gap-2"><i class="fa fa-user text-blue-500"></i> Data Diri Siswa</div>
      <div class="p-5">
        @php $s = $pendaftaran->siswa; @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-sm">
          @foreach([['Nama Lengkap',$s?->nama_siswa],['NISN',$s?->nisn],['Jenis Kelamin',$s?->jenis_kelamin_label],['Tempat Lahir',$s?->tempat_lahir],['Tanggal Lahir',$s?->tanggal_lahir?->format('d/m/Y')],['Agama',$s?->agama_label],['No. HP',$s?->phone],['Email',$s?->email],['Alamat',$s?->alamat],['Asal Sekolah',$s?->asal_sekolah],['Tahun Lulus',$s?->tahun_lulus],['No. Ijazah',$s?->nomor_ijazah]] as [$l,$v])
          <div class="flex gap-2"><span class="w-32 text-gray-400 flex-shrink-0">{{ $l }}</span><span class="font-medium text-gray-800 flex-1">{{ $v ?? '—' }}</span></div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Wali -->
    <div class="card overflow-hidden">
      <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 text-sm font-bold text-gray-700 flex items-center gap-2"><i class="fa fa-users text-blue-500"></i> Data Orang Tua / Wali</div>
      <div class="divide-y divide-gray-100">
        @foreach($pendaftaran->waliSiswas as $w)
        <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
          <div><div class="text-xs text-gray-400">Nama</div><div class="font-semibold">{{ $w->nama_wali }}</div></div>
          <div><div class="text-xs text-gray-400">Status</div><div class="font-semibold capitalize">{{ $w->jenis_wali ?? $w->hubungan_label }}</div></div>
          <div><div class="text-xs text-gray-400">Pekerjaan</div><div class="font-semibold">{{ $w->pekerjaan }}</div></div>
          <div><div class="text-xs text-gray-400">No. HP</div><div class="font-semibold">{{ $w->notelp_wali ?? '—' }}</div></div>
        </div>
        @endforeach
      </div>
    </div>

    <!-- Dokumen -->
    <div class="card overflow-hidden">
      <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 text-sm font-bold text-gray-700 flex items-center gap-2"><i class="fa fa-folder-open text-blue-500"></i> Dokumen</div>
      <div class="p-5">
        @if($pendaftaran->dokumens->count())
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
          @foreach($pendaftaran->dokumens as $d)
          <a href="{{ asset('storage/'.$d->file_path) }}" target="_blank"
            class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition text-sm text-gray-700">
            <i class="fa fa-file text-blue-400 flex-shrink-0"></i>
            <span class="flex-1 truncate">{{ $d->label }}</span>
            <i class="fa fa-external-link text-xs text-gray-400"></i>
          </a>
          @endforeach
        </div>
        @else<div class="text-gray-400 text-sm">Belum ada dokumen.</div>@endif
      </div>
    </div>
  </div>

  <!-- Right: Actions -->
  <div class="space-y-5">

    <!-- Konfirmasi Pendaftaran -->
    @if($pendaftaran->status === 'diproses')
    <div class="card p-5 border-blue-200 bg-blue-50">
      <div class="font-bold text-blue-800 mb-3 flex items-center gap-2"><i class="fa fa-clipboard-check"></i> Konfirmasi Berkas</div>
      <p class="text-xs text-blue-600 mb-4">Verifikasi berkas siswa dan tentukan apakah berkas diterima atau ditolak.</p>
      <form action="{{ route('admin.pendaftaran.terima',$pendaftaran) }}" method="POST" id="form-terima">
        @csrf
        <div class="mb-3">
          <label class="form-label">Catatan (opsional)</label>
          <textarea name="catatan" class="form-input text-xs" rows="2" placeholder="Catatan verifikasi..."></textarea>
        </div>
        <button type="button" onclick="konfirm('form-terima','Terima Berkas?','Berkas akan dikonfirmasi diterima dan email akan dikirim ke siswa.','success','Ya, Terima')"
          class="btn btn-success w-full justify-center mb-2"><i class="fa fa-check"></i> Terima Berkas</button>
      </form>
      <form action="{{ route('admin.pendaftaran.tolak',$pendaftaran) }}" method="POST" id="form-tolak">
        @csrf
        <div class="mb-3">
          <label class="form-label">Alasan Penolakan <span class="req">*</span></label>
          <textarea name="catatan" class="form-input text-xs" rows="2" placeholder="Jelaskan alasan penolakan..." required></textarea>
        </div>
        <button type="button" onclick="konfirm('form-tolak','Tolak Berkas?','Berkas akan ditolak.',danger,'Ya, Tolak')"
          class="btn btn-danger w-full justify-center"><i class="fa fa-times"></i> Tolak Berkas</button>
      </form>
    </div>
    @else
    <div class="card p-4 text-xs text-gray-500 border-gray-200">
      <div class="font-semibold text-gray-700 mb-1">Info Verifikasi</div>
      <div>Diverifikasi oleh: <strong>{{ $pendaftaran->userVerifikator?->name ?? '—' }}</strong></div>
      @if($pendaftaran->tanggal_verifikasi)<div>Tanggal: {{ $pendaftaran->tanggal_verifikasi->format('d/m/Y H:i') }}</div>@endif
      @if($pendaftaran->catatan_admin)<div class="mt-2 p-2 bg-gray-50 rounded">{{ $pendaftaran->catatan_admin }}</div>@endif
    </div>
    @endif

    <!-- Pembayaran -->
    <div class="card overflow-hidden">
      <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 text-sm font-bold text-gray-700 flex items-center gap-2"><i class="fa fa-credit-card text-green-500"></i> Pembayaran</div>
      <div class="p-5">
        @forelse($pendaftaran->pembayarans as $b)
        <div class="border border-gray-200 rounded-xl p-4 mb-3 text-xs space-y-2">
          <div class="flex items-center justify-between">
            <span class="font-mono text-gray-500">{{ $b->order_id }}</span>
            <span class="badge {{ $b->badge_color }}">{{ $b->label_status }}</span>
          </div>
          <div class="grid grid-cols-2 gap-1">
            <span class="text-gray-400">Metode</span><span class="font-medium">{{ $b->metodePembayaran?->nama_metode ?? '—' }}</span>
            <span class="text-gray-400">Nominal</span><span class="font-bold text-green-700">{{ $b->nominal_formatted }}</span>
            <span class="text-gray-400">Tgl Bayar</span><span>{{ $b->tanggal_pembayaran?->format('d/m/Y') ?? '—' }}</span>
          </div>
          @if($b->proof_path)
          <a href="{{ asset('storage/'.$b->proof_path) }}" target="_blank" class="flex items-center gap-1 text-blue-600 hover:underline"><i class="fa fa-image"></i> Lihat Bukti TF</a>
          @endif
          @if($b->isMenungguVerifikasi())
          <form action="{{ route('admin.pembayaran.konfirmasi',$b) }}" method="POST" id="form-bayar-{{ $b->id }}">
            @csrf
            <div class="mb-2">
              <label class="form-label">Catatan verifikasi</label>
              <input type="text" name="catatan" class="form-input" placeholder="Opsional...">
            </div>
            <div class="flex items-center gap-2 mb-2">
              <input type="checkbox" name="selesaikan_pendaftaran" value="1" checked id="selesai-{{ $b->id }}" class="accent-blue-700">
              <label for="selesai-{{ $b->id }}" class="text-xs text-gray-600">Sekalian selesaikan pendaftaran (status → Lunas)</label>
            </div>
            <button type="button" onclick="konfirm('form-bayar-{{ $b->id }}','Konfirmasi Pembayaran?','Pembayaran akan dikonfirmasi dan email dikirim ke siswa.','success','Ya, Konfirmasi')"
              class="btn btn-success btn-sm w-full justify-center"><i class="fa fa-check-double"></i> Konfirmasi Pembayaran</button>
          </form>
          @endif
        </div>
        @empty
        <div class="text-gray-400 text-xs">Belum ada data pembayaran.</div>
        @endforelse

        <!-- Upload bukti TF by admin -->
        @if($pendaftaran->canPay())
        <div class="border-t border-gray-100 pt-3 mt-3">
          <div class="text-xs font-semibold text-gray-600 mb-2">Upload Bukti TF (oleh admin)</div>
          <form action="{{ route('admin.pendaftaran.upload.bukti',$pendaftaran) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
            @csrf
            <div class="file-input-wrapper">
              <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required>
              <div class="file-input-display"><div class="file-label text-xs text-gray-500">Pilih file bukti TF</div></div>
            </div>
            <button type="submit" class="btn btn-warning btn-sm w-full justify-center"><i class="fa fa-upload"></i> Upload Bukti TF</button>
          </form>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function konfirm(formId, title, text, type, confirmText) {
  Swal.fire({title,text,icon:type==='success'?'question':'warning',showCancelButton:true,confirmButtonColor:type==='success'?'#059669':'#dc2626',cancelButtonText:'Batal',confirmButtonText:confirmText})
    .then(r => { if(r.isConfirmed) document.getElementById(formId).submit(); });
}
</script>
@endpush
@endsection
