@extends('layouts.app')
@section('title','Pendaftaran Berhasil')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-16 text-center">
  <div class="card p-10">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
      <i class="fa fa-check-circle text-5xl text-green-500"></i>
    </div>
    <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Pendaftaran Berhasil Dikirim! 🎉</h1>
    <p class="text-gray-500 mb-6">Formulir pendaftaran Anda telah kami terima dan sedang diproses oleh admin.</p>

    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-6">
      <div class="text-sm text-blue-600 font-semibold mb-1">Kode Pendaftaran Anda</div>
      <div class="flex items-center justify-center gap-3 mb-2">
        <div class="text-3xl font-extrabold text-blue-800 tracking-widest" id="kode-text">{{ $pendaftaran->kode_regis }}</div>
        <button onclick="copyKode('{{ $pendaftaran->kode_regis }}')" title="Salin Kode"
          class="w-9 h-9 bg-blue-100 hover:bg-blue-200 rounded-lg flex items-center justify-center transition text-blue-600 flex-shrink-0">
          <i class="fa fa-copy text-sm" id="copy-icon"></i>
        </button>
      </div>
      <div class="text-xs text-blue-500">Simpan kode ini untuk memantau status pendaftaran dan pembayaran.</div>
    </div>

    <div class="text-left bg-gray-50 rounded-xl p-4 mb-6 space-y-2 text-sm">
      <div class="flex justify-between"><span class="text-gray-500">Nama Siswa</span><span class="font-semibold">{{ $pendaftaran->siswa?->nama_siswa }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Sekolah Tujuan</span><span class="font-semibold">{{ $pendaftaran->sekolah?->nama_sekolah }}</span></div>
      @if($pendaftaran->jurusan)<div class="flex justify-between"><span class="text-gray-500">Jurusan</span><span class="font-semibold">{{ $pendaftaran->jurusan->nama_jurusan }}</span></div>@endif
      <div class="flex justify-between"><span class="text-gray-500">Jalur</span><span class="font-semibold capitalize">{{ $pendaftaran->jalur_pendaftaran }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="badge {{ $pendaftaran->badge_color }}">{{ $pendaftaran->label_status }}</span></div>
    </div>

    <div class="alert alert-info text-left mb-6 text-sm">
      <i class="fa fa-envelope flex-shrink-0"></i>
      <div>Email konfirmasi dikirim ke <strong>{{ $pendaftaran->siswa?->email }}</strong>. Cek inbox atau folder spam.</div>
    </div>

    <div class="text-sm text-gray-500 mb-6 leading-relaxed bg-yellow-50 rounded-xl p-4 border border-yellow-200 text-left">
      <strong>Langkah selanjutnya:</strong> Tunggu admin memverifikasi berkas Anda. Setelah berkas diterima, Anda akan mendapat notifikasi email dan dapat melanjutkan proses <strong>pembayaran</strong>.
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ route('cek.status', ['kode'=>$pendaftaran->kode_regis]) }}" class="btn btn-primary">
        <i class="fa fa-search"></i> Cek Status Pendaftaran
      </a>
      <a href="{{ route('home') }}" class="btn btn-secondary">
        <i class="fa fa-home"></i> Kembali ke Beranda
      </a>
    </div>
  </div>
</div>

@push('scripts')
<script>
function copyKode(kode) {
  navigator.clipboard.writeText(kode).then(() => {
    const icon = document.getElementById('copy-icon');
    icon.className = 'fa fa-check text-sm text-green-600';
    Swal.fire({icon:'success',title:'Disalin!',text:'Kode pendaftaran berhasil disalin.',timer:2000,showConfirmButton:false,toast:true,position:'top-end'});
    setTimeout(() => icon.className = 'fa fa-copy text-sm', 2000);
  });
}
// Clear localStorage after successful submission
try { localStorage.removeItem('ppdb_v3'); } catch(e) {}
</script>
@endpush
