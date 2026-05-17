@extends('layouts.app')
@section('title','Beranda')

@section('content')

<!-- HERO -->
<section class="relative min-h-screen flex items-center overflow-hidden" style="background:#0f172a;">
  <!-- Hero background image -->
  <div style="position:absolute;inset:0;background-image:url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1600&q=60');background-size:cover;background-position:center;opacity:0.13;"></div>
  <!-- Gradient overlay -->
  <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(15,23,42,.92) 0%,rgba(30,64,175,.82) 55%,rgba(29,78,216,.72) 100%);"></div>
  <!-- Grid pattern on top -->
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.045) 1px,transparent 1px);background-size:48px 48px;"></div>
  <!-- Animated blobs -->
  <div style="position:absolute;top:-80px;right:-80px;width:500px;height:500px;background:radial-gradient(circle,rgba(59,130,246,.28),transparent 70%);border-radius:50%;animation:blob 6s ease-in-out infinite;"></div>
  <div style="position:absolute;bottom:-100px;left:-50px;width:400px;height:400px;background:radial-gradient(circle,rgba(16,185,129,.18),transparent 70%);border-radius:50%;animation:blob 8s ease-in-out infinite reverse;"></div>

  <div class="relative max-w-7xl mx-auto px-4 py-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    <div class="text-white">
      @if($tahunAktif)
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold mb-6" style="background:rgba(59,130,246,.25);border:1px solid rgba(59,130,246,.4);color:#93c5fd;">
        <span style="width:8px;height:8px;background:#34d399;border-radius:50%;animation:dot 1.5s infinite;display:inline-block;"></span>
        PPDB {{ $tahunAktif->nama_tahun }} Sedang Dibuka
      </div>
      @endif
      <h1 class="text-4xl md:text-5xl xl:text-6xl font-extrabold leading-tight mb-6">
        Masa Depan Cerah<br>
        <span style="background:linear-gradient(90deg,#60a5fa,#34d399);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Dimulai di Sini</span>
      </h1>
      <p class="text-blue-200 text-lg leading-relaxed mb-8 max-w-lg">Daftarkan putra-putri Anda ke sekolah unggulan Yayasan Indonesia. 9 pilihan sekolah SMP, SMA, dan SMK terbaik.</p>
      <div class="flex flex-wrap gap-4 mb-10">
        <a href="{{ route('daftar') }}" class="btn btn-lg" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;box-shadow:0 8px 24px rgba(59,130,246,.4);"><i class="fa fa-pen-to-square"></i> Daftar Sekarang</a>
        <a href="{{ route('cek.status') }}" class="btn btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.3);"><i class="fa fa-search"></i> Cek Status</a>
      </div>
      <div class="flex gap-8">
        <div><div class="text-3xl font-extrabold">9</div><div class="text-blue-300 text-sm">Sekolah</div></div>
        <div class="border-l border-blue-700 pl-8"><div class="text-3xl font-extrabold">{{ number_format($totalDaftar) }}+</div><div class="text-blue-300 text-sm">Pendaftar</div></div>
        <div class="border-l border-blue-700 pl-8"><div class="text-3xl font-extrabold">{{ number_format($totalLunas) }}+</div><div class="text-blue-300 text-sm">Diterima</div></div>
      </div>
    </div>
    <div class="hidden lg:block">
      <div class="relative" style="animation:float 4s ease-in-out infinite;">
        <div style="background:rgba(255,255,255,.08);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.15);border-radius:24px;padding:28px;">
          <div class="text-white font-bold text-lg mb-4 flex items-center gap-2"><i class="fa fa-clipboard-list text-blue-400"></i> Formulir Pendaftaran Online</div>
          <div class="space-y-3">
            @php $steps=['Pilih Sekolah & Jurusan','Data Diri Siswa','Jalur Pendaftaran','Data Orang Tua','Upload Dokumen','Review & Kirim']; @endphp
            @foreach($steps as $i=>$s)
            <div class="flex items-center gap-3">
              <div style="width:28px;height:28px;border-radius:50%;background:{{ $i<3?'#1e40af':'rgba(255,255,255,.1)' }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">{{ $i+1 }}</div>
              <div style="flex:1;height:8px;border-radius:4px;background:{{ $i<3?'rgba(59,130,246,.6)':'rgba(255,255,255,.1)' }};"></div>
              <span style="font-size:11px;font-weight:500;color:{{ $i<3?'#93c5fd':'rgba(255,255,255,.4)' }};width:130px;">{{ $s }}</span>
            </div>
            @endforeach
          </div>
          <div class="mt-5 pt-4 border-t border-white border-opacity-10 text-xs text-blue-300 flex items-center gap-2"><i class="fa fa-shield-halved text-green-400"></i> Data terenkripsi dan aman</div>
        </div>
        <div style="position:absolute;top:-14px;right:-14px;background:#059669;color:#fff;border-radius:12px;padding:7px 13px;font-size:12px;font-weight:700;animation:badge 3s ease-in-out infinite;"><i class="fa fa-check"></i> Gratis & Mudah</div>
        <div style="position:absolute;bottom:-14px;left:-14px;background:#fff;color:#1e40af;border-radius:12px;padding:7px 13px;font-size:12px;font-weight:700;animation:badge 3.5s ease-in-out infinite reverse;"><i class="fa fa-bell"></i> Notif Email Otomatis</div>
      </div>
    </div>
  </div>
  <div style="position:absolute;bottom:-2px;left:0;right:0;"><svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display:block;width:100%;height:60px;"><path fill="#f9fafb" d="M0,40 C360,70 1080,10 1440,40 L1440,60 L0,60 Z"/></svg></div>
</section>

<!-- CARA DAFTAR -->
<section id="cara-daftar" class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-12">
      <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wide mb-3">Cara Mendaftar</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">Mudah & <span class="text-blue-600">Menyenangkan!</span></h2>
      <p class="text-gray-500 max-w-xl mx-auto">Dari isi formulir sampai konfirmasi — simpel, cepat, dan seru.</p>
    </div>
    @php
    $langkahs=[
      ['fa-school','LANGKAH 1','Pilih Sekolah & Jurusan','Pilih dari 9 sekolah unggulan Yayasan Indonesia.','#3b82f6'],
      ['fa-user-pen','LANGKAH 2','Isi Data Diri','Lengkapi biodata siswa dan data orang tua/wali.','#8b5cf6'],
      ['fa-route','LANGKAH 3','Pilih Jalur Pendaftaran','Reguler, Prestasi, Afirmasi, atau Pindahan.','#059669'],
      ['fa-file-upload','LANGKAH 4','Upload Dokumen','Pas foto, KK, akta kelahiran, dll (PDF/JPG maks 20MB).','#d97706'],
      ['fa-paper-plane','LANGKAH 5','Kirim & Tunggu Verifikasi','Admin verifikasi berkas, notifikasi via email.','#dc2626'],
      ['fa-credit-card','LANGKAH 6','Bayar Uang Pendaftaran','Transfer bank, tunai, atau Midtrans (GoPay, OVO, dll).','#1e40af'],
    ];
    @endphp
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
      <div class="space-y-1" id="tl-list">
        @foreach($langkahs as $i=>$l)
        <div class="tl-item flex gap-4 p-4 rounded-xl cursor-pointer transition-all hover:bg-white hover:shadow-md {{ $i===0?'bg-white shadow-md':'' }}"
             data-idx="{{ $i }}" data-color="{{ $l[4] }}" onclick="pickStep({{ $i }})">
          <div class="flex flex-col items-center gap-1">
            <div class="tl-icon w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 transition-all" style="background:{{ $i===0?$l[4]:'#f1f5f9' }};">
              <i class="fa {{ $l[0] }} text-sm" style="color:{{ $i===0?'#fff':'#9ca3af' }};"></i>
            </div>
            @if(!$loop->last)<div class="w-0.5 h-6 bg-gray-200 mx-auto"></div>@endif
          </div>
          <div class="flex-1 pb-2">
            <div class="tl-label text-xs font-bold mb-0.5" style="color:{{ $i===0?$l[4]:'#9ca3af' }};">{{ $l[1] }}</div>
            <div class="font-bold text-gray-800 text-sm">{{ $l[2] }}</div>
            <div class="text-gray-500 text-xs mt-1 leading-relaxed">{{ $l[3] }}</div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="sticky top-24">
        <div class="rounded-2xl overflow-hidden shadow-xl border border-gray-100" style="min-height:360px;" id="tl-preview">
          @foreach($langkahs as $i=>$l)
          <div class="tl-pane {{ $i>0?'hidden':'' }}" style="background:linear-gradient(135deg,{{ $l[4] }}18,{{ $l[4] }}06);padding:28px;min-height:360px;">
            <div class="text-xs font-bold uppercase tracking-widest mb-2" style="color:{{ $l[4] }};">{{ $l[1] }}</div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-3">{{ $l[2] }}</h3>
            <p class="text-gray-600 text-sm leading-relaxed mb-5">{{ $l[3] }}</p>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-xs">
              @if($i===0)<div class="font-semibold text-gray-500 mb-2">Contoh pilihan sekolah:</div><div class="grid grid-cols-2 gap-2">@foreach(['🏫 SMP Nusantara','🏫 SMK Teknologi','🏫 SMA Generasi','🏫 SMP Bina Insan'] as $s)<div class="border rounded-lg p-2 font-medium text-gray-700">{{ $s }}</div>@endforeach</div>
              @elseif($i===1)<div class="space-y-2">
                <div class="text-xs font-semibold text-gray-500 mb-2">Contoh isian data diri:</div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 flex items-center gap-2"><i class="fa fa-user text-blue-400 w-3"></i> Ahmad Fadhillah Pratama</div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 flex items-center gap-2"><i class="fa fa-id-card text-blue-400 w-3"></i> NISN: 0012345678</div>
                <div class="grid grid-cols-2 gap-2">
                  <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 flex items-center gap-1"><i class="fa fa-mars text-blue-400 w-3"></i> Laki-Laki</div>
                  <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 flex items-center gap-1"><i class="fa fa-cake-candles text-blue-400 w-3"></i> 15/06/2009</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 flex items-center gap-2"><i class="fa fa-envelope text-blue-400 w-3"></i> siswa@email.com</div>
                <div class="bg-blue-50 border border-blue-300 rounded-lg px-3 py-1.5 text-xs text-blue-600 font-semibold flex items-center gap-1 mt-1"><i class="fa fa-floppy-disk text-blue-500 w-3"></i> Data tersimpan otomatis</div>
              </div>
              @elseif($i===2)<div class="space-y-2">@foreach(['Reguler','Prestasi','Afirmasi','Pindahan'] as $j)<div class="flex items-center gap-2 p-2 rounded-lg border {{ $j==='Reguler'?'border-blue-500 bg-blue-50 text-blue-700':'border-gray-200 text-gray-700' }}"><div class="w-4 h-4 rounded-full border-2 {{ $j==='Reguler'?'border-blue-500 bg-blue-500':'border-gray-300' }}"></div>{{ $j }}</div>@endforeach</div>
              @elseif($i===3)<div class="space-y-2">@foreach(['Pas Foto','KK','Akta Kelahiran','Ijazah'] as $d)<div class="flex items-center gap-2 p-2 rounded-lg border border-dashed border-gray-300 text-gray-500"><i class="fa fa-cloud-upload-alt text-gray-400"></i>{{ $d }}</div>@endforeach</div>
              @elseif($i===4)<div class="text-center py-4"><div class="text-4xl mb-2">📧</div><div class="font-semibold text-gray-700">Email konfirmasi dikirim!</div><div class="text-gray-500 mt-1">Kode: <strong>PPDB25-XXXXXXXX</strong></div></div>
              @else<div class="space-y-2">@foreach(['Transfer BCA','Transfer Mandiri','Bayar Tunai','via Midtrans'] as $m)<div class="flex items-center gap-2 p-2 rounded-lg border {{ $m==='via Midtrans'?'border-blue-500 bg-blue-50 text-blue-700':'border-gray-200 text-gray-700' }}"><i class="fa fa-credit-card {{ $m==='via Midtrans'?'text-blue-500':'text-gray-400' }}"></i>{{ $m }}</div>@endforeach</div>
              @endif
            </div>
          </div>
          @endforeach
        </div>
        <div class="mt-4"><a href="{{ route('daftar') }}" class="btn btn-primary btn-lg w-full justify-center"><i class="fa fa-pen-to-square"></i> Mulai Pendaftaran Sekarang</a></div>
      </div>
    </div>
  </div>
</section>

<!-- SEKOLAH -->
<section id="sekolah" class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-12">
      <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wide mb-3">Sekolah Kami</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3">9 Sekolah Unggulan</h2>
      <p class="text-gray-500 max-w-xl mx-auto">Pilih sekolah yang sesuai dengan minat dan cita-cita putra-putri Anda.</p>
    </div>
    <div class="flex flex-wrap justify-center gap-2 mb-8">
      @foreach(['Semua','SMP','SMA','SMK'] as $t)
      <button onclick="filterSekolah('{{ $t }}')" class="skolah-filter btn btn-sm {{ $t==='Semua'?'btn-primary':'btn-secondary' }}" data-f="{{ $t }}">{{ $t }}</button>
      @endforeach
    </div>
    @php
    $imgs=['photo-1580582932707-520aed937b7b','photo-1497633762265-9d179a990aa6','photo-1541339907198-e08756dedf3f','photo-1571260899304-425eee4c7efc','photo-1509062522246-3755977927d7','photo-1588072432836-e10032774350','photo-1581092918056-0c4c3acd3789','photo-1554224155-6726b3ff858f','photo-1523050854058-8df90110c9f1'];
    $clrs=['SMP'=>['#eff6ff','#1e40af','#3b82f6'],'SMA'=>['#f0fdf4','#065f46','#059669'],'SMK'=>['#fff7ed','#9a3412','#ea580c']];
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="sekolah-grid">
      @foreach($sekolahs as $idx=>$s)
      @php $c=$clrs[$s->tingkatan]??$clrs['SMP']; $img=$imgs[$idx]??$imgs[0]; @endphp
      <div class="skolah-card card hover:shadow-xl transition-all hover:-translate-y-1 overflow-hidden" data-t="{{ $s->tingkatan }}">
        <div class="relative h-44 overflow-hidden">
          <img src="https://images.unsplash.com/{{ $img }}?auto=format&fit=crop&w=600&q=70"
               alt="{{ $s->nama_sekolah }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" loading="lazy">
          <div class="absolute inset-0" style="background:linear-gradient(to bottom,transparent 40%,rgba(0,0,0,.5));"></div>
          <span class="absolute top-3 right-3 badge text-xs font-bold" style="background:{{ $c[2] }};color:#fff;">{{ $s->tingkatan }}</span>
          <span class="absolute bottom-3 left-3 text-white font-bold text-sm">{{ $s->singkatan }}</span>
        </div>
        <div class="p-5">
          <h3 class="font-bold text-gray-900 mb-1">{{ $s->nama_sekolah }}</h3>
          <p class="text-xs text-gray-500 mb-3 flex items-center gap-1"><i class="fa fa-map-marker-alt" style="color:{{ $c[2] }};"></i> {{ $s->kota }}</p>
          @if($s->jurusans->count()>0)
          <div class="mb-4">
            <div class="text-xs font-semibold text-gray-500 mb-2">Jurusan:</div>
            <div class="flex flex-wrap gap-1">
              @foreach($s->jurusans->take(4) as $j)<span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background:{{ $c[0] }};color:{{ $c[1] }};">{{ $j->kode_jurusan??$j->nama_jurusan }}</span>@endforeach
              @if($s->jurusans->count()>4)<span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">+{{ $s->jurusans->count()-4 }}</span>@endif
            </div>
          </div>
          @endif
          <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <div class="text-xs text-gray-500"><i class="fa fa-user-graduate text-gray-400"></i> Kuota: <strong>{{ $s->kuota }}</strong></div>
            <a href="{{ route('daftar') }}?sekolah={{ $s->id }}" class="btn btn-sm btn-primary"><i class="fa fa-pen-to-square"></i> Daftar</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="relative overflow-hidden rounded-3xl" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 40%,#1d4ed8 70%,#2563eb 100%);">
      <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:40px 40px;"></div>
      <div style="position:absolute;top:-60px;right:-60px;width:300px;height:300px;background:radial-gradient(circle,rgba(99,102,241,.4),transparent 70%);border-radius:50%;"></div>
      <div style="position:absolute;bottom:-60px;left:-40px;width:280px;height:280px;background:radial-gradient(circle,rgba(16,185,129,.25),transparent 70%);border-radius:50%;"></div>
      <div class="relative px-8 py-14 md:px-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
          <div class="text-white">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-5" style="background:rgba(99,102,241,.3);border:1px solid rgba(99,102,241,.5);color:#a5b4fc;"><i class="fa fa-rocket"></i> Daftar Sekarang — Gratis!</div>
            <h2 class="text-3xl md:text-4xl font-extrabold mb-4 leading-tight">Siap Bergabung<br>dengan Kami? 🎓</h2>
            <p class="text-blue-200 text-base leading-relaxed mb-8 max-w-md">Ribuan siswa telah mempercayakan pendidikan mereka kepada Yayasan Indonesia. Giliran Anda!</p>
            <div class="flex flex-wrap gap-3">
              <a href="{{ route('daftar') }}" class="btn btn-lg" style="background:#fff;color:#1e40af;font-weight:800;box-shadow:0 8px 24px rgba(0,0,0,.25);"><i class="fa fa-pen-to-square"></i> Daftar Sekarang</a>
              <a href="{{ route('cek.status') }}" class="btn btn-lg" style="background:rgba(255,255,255,.1);color:#fff;border:1.5px solid rgba(255,255,255,.3);"><i class="fa fa-search"></i> Cek Status</a>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            @foreach([['9','Sekolah Unggulan','fa-school','#60a5fa'],['3','Jenjang Pendidikan','fa-graduation-cap','#34d399'],[number_format($totalDaftar).'+','Total Pendaftar','fa-users','#f59e0b'],[number_format($totalLunas).'+','Siswa Diterima','fa-trophy','#a78bfa']] as $st)
            <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:20px;backdrop-filter:blur(8px);">
              <div class="text-2xl mb-2" style="color:{{ $st[3] }};"><i class="fa {{ $st[2] }}"></i></div>
              <div class="text-2xl font-extrabold text-white">{{ $st[0] }}</div>
              <div class="text-xs text-blue-300 mt-1">{{ $st[1] }}</div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONI -->
@if($testimonis->count()>0)
<section class="py-20 bg-white overflow-hidden" style="margin-bottom:0;padding-bottom:0;">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center mb-12">
      <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wide mb-3">Testimoni</span>
      <h2 class="text-3xl font-extrabold text-gray-900 mb-3">Kata Mereka</h2>
    </div>
    <div class="overflow-hidden pb-20"><div id="testi-track" class="flex gap-6" style="transition:transform .7s cubic-bezier(.4,0,.2,1);">
      @foreach($testimonis->concat($testimonis) as $t)
      <div class="flex-shrink-0 w-80 card p-6">
        <div class="flex mb-3">@for($i=0;$i<5;$i++)<i class="fa fa-star text-sm {{ $i<$t->rating?'text-yellow-400':'text-gray-200' }}"></i>@endfor</div>
        <p class="text-gray-600 text-sm leading-relaxed mb-4">"{{ $t->isi_testimoni }}"</p>
        <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
          <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700 text-sm flex-shrink-0">{{ strtoupper(substr($t->nama,0,1)) }}</div>
          <div><div class="font-semibold text-gray-800 text-sm">{{ $t->nama }}</div><div class="text-xs text-gray-500">{{ $t->asal_sekolah }}</div></div>
        </div>
      </div>
      @endforeach
    </div></div>
  </div>
</section>
@endif
@endsection

@push('head')
<style>
@keyframes blob{0%,100%{transform:scale(1)}50%{transform:scale(1.1)}}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
@keyframes badge{0%,100%{transform:translateY(0) rotate(-2deg)}50%{transform:translateY(-5px) rotate(2deg)}}
@keyframes dot{75%,100%{transform:scale(2);opacity:0}}
</style>
@endpush

@push('scripts')
<script>
// Timeline
let tlStep=0,tlTimer=null;
function pickStep(i){
  tlStep=i;
  document.querySelectorAll('.tl-item').forEach((el,j)=>{
    const on=j===i,c=el.dataset.color;
    el.classList.toggle('bg-white',on);el.classList.toggle('shadow-md',on);
    el.querySelector('.tl-icon').style.background=on?c:'#f1f5f9';
    el.querySelector('.tl-icon i').style.color=on?'#fff':'#9ca3af';
    el.querySelector('.tl-label').style.color=on?c:'#9ca3af';
  });
  document.querySelectorAll('.tl-pane').forEach((el,j)=>el.classList.toggle('hidden',j!==i));
}
function startTl(){clearInterval(tlTimer);tlTimer=setInterval(()=>{tlStep=(tlStep+1)%6;pickStep(tlStep);},2000);}
document.querySelectorAll('.tl-item').forEach(el=>{el.addEventListener('mouseenter',()=>clearInterval(tlTimer));el.addEventListener('mouseleave',startTl);});
startTl();

// Filter sekolah
function filterSekolah(t){
  document.querySelectorAll('.skolah-filter').forEach(b=>{b.classList.toggle('btn-primary',b.dataset.f===t);b.classList.toggle('btn-secondary',b.dataset.f!==t);});
  document.querySelectorAll('.skolah-card').forEach(c=>{c.style.display=(t==='Semua'||c.dataset.t===t)?'':'none';});
}

// Testimoni auto slide
const tt=document.getElementById('testi-track');
if(tt){let p=0;const w=344,h=tt.children.length/2;setInterval(()=>{p+=w;if(p>=h*w)p=0;tt.style.transform=`translateX(-${p}px)`;},3000);}
</script>
@endpush
