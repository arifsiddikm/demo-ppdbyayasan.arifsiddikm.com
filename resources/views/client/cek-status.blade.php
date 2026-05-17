@extends('layouts.app')
@section('title','Cek Status Pendaftaran & Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
  <div class="text-center mb-8">
    <h1 class="text-2xl font-extrabold text-gray-900">Cek Status Pendaftaran</h1>
    <p class="text-gray-500 text-sm mt-1">Masukkan kode pendaftaran untuk melihat status dan melakukan pembayaran.</p>
  </div>

  <!-- Search -->
  <div class="card p-6 mb-6">
    <form method="GET" action="{{ route('cek.status') }}" class="flex gap-3">
      <input type="text" name="kode" id="kode-input" class="form-input flex-1 uppercase" placeholder="Contoh: PPDB25-XXXXXXXX"
             value="{{ request('kode') }}" style="letter-spacing:1px;" required>
      <button type="submit" class="btn btn-primary flex-shrink-0"><i class="fa fa-search"></i> Cek</button>
    </form>
  </div>

  @if($error)
  <div class="alert alert-danger mb-6"><i class="fa fa-circle-exclamation flex-shrink-0"></i> {{ $error }}</div>
  @endif
  @if(session('success'))
  <div class="alert alert-success mb-6"><i class="fa fa-check-circle flex-shrink-0"></i> {{ session('success') }}</div>
  @endif
  @if(session('success_testimoni'))
  <div class="alert alert-success mb-6"><i class="fa fa-star flex-shrink-0"></i> {{ session('success_testimoni') }}</div>
  @endif
  @if(session('info'))
  <div class="alert alert-info mb-6"><i class="fa fa-info-circle flex-shrink-0"></i> {{ session('info') }}</div>
  @endif

  @if($pendaftaran)
  @php
    $siswa      = $pendaftaran->siswa;
    $sudahLunas = $pendaftaran->pembayarans->where('status_pembayaran','sukses')->count() > 0;
    $pembayaran = $pendaftaran->pembayarans->sortByDesc('id')->first();
    $testi = \App\Models\Testimoni::where('pendaftaran_id',$pendaftaran->id)->first();
    $sudahTestimoni = $testi !== null;
    $testiApproved  = $testi && $testi->is_active;
  @endphp

  <!-- Status Card -->
  <div class="card mb-6 overflow-hidden">
    <div class="h-1.5" style="background:linear-gradient(90deg,#1e40af,#3b82f6)"></div>
    <div class="p-6">
      <div class="flex items-start justify-between flex-wrap gap-3 mb-4">
        <div>
          <div class="text-xs text-gray-500 mb-1">Kode Pendaftaran</div>
          <div class="flex items-center gap-2">
            <div class="text-xl font-extrabold text-blue-800 tracking-wider font-mono" id="kode-val">{{ $pendaftaran->kode_regis }}</div>
            <button onclick="copyKode('{{ $pendaftaran->kode_regis }}')" title="Salin"
              class="w-8 h-8 bg-blue-100 hover:bg-blue-200 rounded-lg flex items-center justify-center transition text-blue-600">
              <i class="fa fa-copy text-xs" id="ci"></i>
            </button>
          </div>
        </div>
        <span class="badge text-sm px-4 py-1.5 {{ $pendaftaran->badge_color }}">{{ $pendaftaran->label_status }}</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm mb-5">
        <div class="flex justify-between bg-gray-50 rounded-lg p-3"><span class="text-gray-500">Nama</span><span class="font-semibold">{{ $siswa?->nama_siswa }}</span></div>
        <div class="flex justify-between bg-gray-50 rounded-lg p-3"><span class="text-gray-500">Sekolah</span><span class="font-semibold">{{ $pendaftaran->sekolah?->nama_sekolah }}</span></div>
        @if($pendaftaran->jurusan)<div class="flex justify-between bg-gray-50 rounded-lg p-3"><span class="text-gray-500">Jurusan</span><span class="font-semibold">{{ $pendaftaran->jurusan->nama_jurusan }}</span></div>@endif
        <div class="flex justify-between bg-gray-50 rounded-lg p-3"><span class="text-gray-500">Jalur</span><span class="font-semibold capitalize">{{ $pendaftaran->jalur_pendaftaran }}</span></div>
        <div class="flex justify-between bg-gray-50 rounded-lg p-3"><span class="text-gray-500">Tgl Daftar</span><span class="font-semibold">{{ $pendaftaran->tanggal_submit?->format('d/m/Y') }}</span></div>
      </div>

      <!-- Progress Timeline -->
      <div class="relative pl-10">
        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
        @php
        $statuses = [
          ['diproses','Formulir Dikirim','Berkas sedang ditinjau oleh admin','fa-inbox'],
          ['diterima','Berkas Diterima','Admin telah memverifikasi berkas Anda','fa-check-circle'],
          ['menunggu_pembayaran','Menunggu Pembayaran','Silakan lakukan pembayaran uang pendaftaran','fa-credit-card'],
          ['lunas','Selesai & Lunas','Pendaftaran dan pembayaran telah selesai','fa-trophy'],
        ];
        $order = ['diproses'=>0,'diterima'=>1,'ditolak'=>1,'menunggu_pembayaran'=>2,'lunas'=>3];
        $cur = $order[$pendaftaran->status] ?? 0;
        @endphp
        <div class="space-y-4">
          @foreach($statuses as $st)
          @php $stOrd = $order[$st[0]] ?? 0; $isLunas = $pendaftaran->status === 'lunas'; $done = $cur > $stOrd || ($isLunas && $cur === $stOrd); $active = !$isLunas && $cur === $stOrd; @endphp
          <div class="flex items-start gap-3">
            <div class="absolute -left-0.5 mt-0.5 w-8 h-8 rounded-full flex items-center justify-center border-2
              {{ $done ? 'bg-blue-600 border-blue-600' : ($active ? 'bg-white border-blue-500' : 'bg-white border-gray-300') }}"
              style="margin-left:-4px">
              @if($done)<i class="fa fa-check text-white text-xs"></i>
              @elseif($active)<div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              @else<div class="w-3 h-3 bg-gray-300 rounded-full"></div>@endif
            </div>
            <div class="{{ $active ? '' : 'opacity-50' }} pl-2">
              <div class="font-semibold text-sm {{ $active ? 'text-blue-700' : 'text-gray-700' }}">{{ $st[1] }}</div>
              <div class="text-xs text-gray-500">{{ $st[2] }}</div>
            </div>
          </div>
          @endforeach
        </div>
        @if($pendaftaran->status === 'ditolak')
        <div class="alert alert-danger text-sm mt-3"><i class="fa fa-times-circle flex-shrink-0"></i><div><strong>Ditolak:</strong> {{ $pendaftaran->catatan_admin }}</div></div>
        @endif
      </div>
    </div>
  </div>

  <!-- PEMBAYARAN -->
  @if($sudahLunas)
  <div class="card p-6 mb-6 border-green-200" style="background:linear-gradient(135deg,#d1fae5,#ecfdf5);">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center"><i class="fa fa-check text-white text-xl"></i></div>
      <div><div class="font-bold text-green-800 text-lg">Pembayaran Lunas ✓</div><div class="text-green-600 text-sm">Pendaftaran Anda telah selesai.</div></div>
    </div>
    @php $bayarSukses = $pendaftaran->pembayarans->where('status_pembayaran','sukses')->first(); @endphp
    @if($bayarSukses)
    <div class="text-sm text-green-700 space-y-1 mb-4">
      <div>Metode: <strong>{{ $bayarSukses->metodePembayaran?->nama_metode }}</strong></div>
      <div>Nominal: <strong>{{ $bayarSukses->nominal_formatted }}</strong></div>
      <div>Tanggal: <strong>{{ $bayarSukses->tanggal_pembayaran?->format('d/m/Y') }}</strong></div>
    </div>
    @endif
    <a href="{{ route('bayar.pdf', ['kode'=>$pendaftaran->kode_regis]) }}" target="_blank" class="btn btn-success w-full justify-center mb-3">
      <i class="fa fa-file-pdf"></i> Unduh Formulir PDF (Cetak & Serahkan ke Sekolah)
    </a>

    {{-- FORM TESTIMONI --}}
    @if(!$sudahTestimoni)
    <div class="mt-5 pt-5 border-t border-green-200">
      <h3 class="font-bold text-gray-800 mb-1 flex items-center gap-2"><i class="fa fa-star text-yellow-500"></i> Bagikan Pengalaman Anda</h3>
      <p class="text-xs text-gray-500 mb-4">Ceritakan pengalaman mendaftar PPDB — testimoni Anda akan membantu calon siswa lainnya.</p>
      <form action="{{ route('testimoni.submit') }}" method="POST" class="space-y-3">
        @csrf
        <input type="hidden" name="kode_regis" value="{{ $pendaftaran->kode_regis }}">
        <div>
          <label class="form-label">Rating <span class="req">*</span></label>
          <div class="flex gap-2" id="star-group">
            @for($s=1;$s<=5;$s++)
            <button type="button" onclick="setRating({{ $s }})" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition" data-r="{{ $s }}">★</button>
            @endfor
          </div>
          <input type="hidden" name="rating" id="rating-val" value="">
        </div>
        <div>
          <label class="form-label">Cerita / Testimoni <span class="req">*</span></label>
          <textarea name="isi_testimoni" class="form-input" rows="3" placeholder="Ceritakan pengalaman mendaftar PPDB di sini... (min. 20 karakter)" required minlength="20" maxlength="500"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-paper-plane"></i> Kirim Testimoni</button>
      </form>
    </div>
    @else
    <div class="mt-4 pt-4 border-t border-green-200 text-sm text-green-700 flex items-center gap-2">
      <i class="fa fa-check-circle"></i> @if($testiApproved) Testimoni Anda sudah <strong>disetujui</strong> dan tampil di website! @else Testimoni dikirim &mdash; menunggu persetujuan admin. @endif
    </div>
    @endif
  </div>

  @elseif($pendaftaran->canPay())
  <!-- FORM BAYAR -->
  <div class="card mb-6">
    <div class="card-header bg-blue-50 border-blue-100">
      <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa fa-credit-card"></i> Lakukan Pembayaran</h3>
      <p class="text-xs text-blue-600 mt-1">Biaya pendaftaran: <strong>Rp 200.000</strong></p>
    </div>
    <div class="card-body">
      @if($pembayaran && $pembayaran->isMenungguVerifikasi())
      <div class="alert alert-info mb-4 text-sm"><i class="fa fa-clock flex-shrink-0"></i><div>Bukti transfer Anda sedang diverifikasi admin. Harap tunggu konfirmasi.</div></div>
      @if($pembayaran->proof_path)
      <div class="text-xs text-gray-500"><a href="{{ asset('storage/'.$pembayaran->proof_path) }}" target="_blank" class="text-blue-600 underline"><i class="fa fa-image mr-1"></i>Lihat bukti yang diupload</a></div>
      @endif
      @else
      <div class="flex gap-2 mb-5 flex-wrap" id="metode-tabs">
        @foreach($metodePembayaran as $m)
        <button type="button" onclick="selectMetode({{ $m->id }},'{{ $m->tipe }}')" class="metode-tab btn btn-sm btn-secondary" data-metode="{{ $m->id }}">
          @if($m->tipe==='otomatis')<i class="fa fa-bolt"></i>@elseif($m->tipe==='cash')<i class="fa fa-money-bill"></i>@else<i class="fa fa-university"></i>@endif
          {{ $m->nama_metode }}
        </button>
        @endforeach
      </div>

      @foreach($metodePembayaran as $m)
      <div class="metode-panel hidden" id="panel-{{ $m->id }}">
        @if($m->tipe==='otomatis')
        <div class="text-center py-6">
          <div class="text-4xl mb-3">⚡</div>
          <h4 class="font-bold text-gray-800 mb-1">Bayar via Midtrans</h4>
          <p class="text-gray-500 text-sm mb-4">GoPay, OVO, DANA, Transfer Bank, Kartu Kredit, dll.</p>
          <button type="button" onclick="bayarMidtrans('{{ $pendaftaran->kode_regis }}')" id="btn-midtrans" class="btn btn-primary btn-lg">
            <i class="fa fa-bolt"></i> Bayar Sekarang — Rp 200.000
          </button>
          <div id="midtrans-loading" class="hidden email-sending mt-3 justify-center"><i class="fa fa-circle-notch spin"></i> Mempersiapkan pembayaran...</div>
        </div>
        @elseif($m->tipe==='bank_transfer')
        <div class="bg-gray-50 rounded-xl p-4 mb-4 text-sm">
          <div class="font-bold text-gray-700 mb-2">Rekening Tujuan</div>
          <div class="flex items-center justify-between mb-1"><span class="text-gray-500">Bank</span><span class="font-semibold">{{ $m->nama_bank }}</span></div>
          <div class="flex items-center justify-between mb-1">
            <span class="text-gray-500">No. Rekening</span>
            <div class="flex items-center gap-2">
              <span class="font-bold text-blue-700 text-lg tracking-wider">{{ $m->no_rekening }}</span>
              <button type="button" onclick="copyKode('{{ $m->no_rekening }}')" class="w-7 h-7 bg-blue-100 hover:bg-blue-200 rounded flex items-center justify-center text-blue-600"><i class="fa fa-copy text-xs"></i></button>
            </div>
          </div>
          <div class="flex items-center justify-between"><span class="text-gray-500">Atas Nama</span><span class="font-semibold">{{ $m->atas_nama }}</span></div>
        </div>
        @if($m->instruksi)<div class="text-xs text-gray-500 mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">{!! nl2br(e($m->instruksi)) !!}</div>@endif
        <form action="{{ route('bayar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
          @csrf
          <input type="hidden" name="kode_regis" value="{{ $pendaftaran->kode_regis }}">
          <input type="hidden" name="metode_pembayaran_id" value="{{ $m->id }}">
          <div><label class="form-label">Tanggal Transfer <span class="req">*</span></label><input type="date" name="tanggal_bayar" class="form-input" value="{{ date('Y-m-d') }}" required></div>
          <div><label class="form-label">Upload Bukti Transfer <span class="req">*</span></label>
            <div class="file-input-wrapper"><input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required><div class="file-input-display"><i class="fa fa-upload text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">Foto/scan bukti transfer (JPG/PNG/PDF, maks 10MB)</div></div></div>
          </div>
          <button type="submit" class="btn btn-primary w-full justify-center"><i class="fa fa-upload"></i> Kirim Bukti Transfer</button>
        </form>
        @elseif($m->tipe==='cash')
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4 text-sm">@if($m->instruksi)<p>{!! nl2br(e($m->instruksi)) !!}</p>@endif</div>
        <form action="{{ route('bayar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
          @csrf
          <input type="hidden" name="kode_regis" value="{{ $pendaftaran->kode_regis }}">
          <input type="hidden" name="metode_pembayaran_id" value="{{ $m->id }}">
          <div><label class="form-label">Tanggal Bayar <span class="req">*</span></label><input type="date" name="tanggal_bayar" class="form-input" value="{{ date('Y-m-d') }}" required></div>
          <div><label class="form-label">Upload Bukti (opsional)</label><div class="file-input-wrapper"><input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf"><div class="file-input-display"><i class="fa fa-image text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">Foto kwitansi (opsional)</div></div></div></div>
          <button type="submit" class="btn btn-success w-full justify-center"><i class="fa fa-check"></i> Konfirmasi Pembayaran Tunai</button>
        </form>
        @endif
      </div>
      @endforeach
      @endif
    </div>
  </div>

  @elseif(in_array($pendaftaran->status,['diproses']))
  <div class="card p-6 border-yellow-200 bg-yellow-50 mb-6">
    <div class="flex items-center gap-3 text-yellow-800"><i class="fa fa-hourglass-half text-2xl text-yellow-500"></i><div><div class="font-bold">Menunggu Verifikasi Berkas</div><div class="text-sm">Admin sedang meninjau berkas Anda. Anda akan mendapat email setelah berkas diverifikasi.</div></div></div>
  </div>

  @elseif($pendaftaran->status === 'ditolak')
  <div class="card p-6 border-red-200 bg-red-50 mb-6">
    <div class="flex items-center gap-3 text-red-800"><i class="fa fa-times-circle text-2xl text-red-500"></i><div><div class="font-bold">Berkas Ditolak</div><div class="text-sm">{{ $pendaftaran->catatan_admin }}</div></div></div>
  </div>
  @endif

  @else
  {{-- TIDAK ADA PENDAFTARAN: tampilkan panduan --}}
  <div class="space-y-6">
    <!-- Panduan cara cek -->
    <div class="card p-6">
      <h2 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2"><i class="fa fa-circle-question text-blue-500"></i> Cara Cek Status Pendaftaran</h2>
      <div class="space-y-4">
        @foreach([
          ['1','Masukkan Kode Pendaftaran','Ketik kode pendaftaran Anda di kolom pencarian di atas. Format kode: PPDB25-XXXXXXXX (8 karakter setelah tanda strip).','fa-keyboard','#3b82f6'],
          ['2','Klik Tombol Cek','Tekan tombol "Cek" untuk mencari data pendaftaran Anda berdasarkan kode.','fa-search','#8b5cf6'],
          ['3','Lihat Status','Status pendaftaran akan tampil beserta informasi detail, instruksi, dan tombol pembayaran jika sudah bisa bayar.','fa-eye','#059669'],
          ['4','Lakukan Pembayaran','Jika status sudah "Berkas Diterima", lakukan pembayaran via transfer bank, tunai, atau Midtrans.','fa-credit-card','#1e40af'],
        ] as $p)
        <div class="flex gap-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-white font-bold text-sm" style="background:{{ $p[4] }};">{{ $p[0] }}</div>
          <div>
            <div class="font-semibold text-gray-800 text-sm">{{ $p[1] }}</div>
            <div class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $p[2] }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <!-- FAQ -->
    <div class="card p-6">
      <h2 class="font-bold text-gray-800 text-lg mb-4 flex items-center gap-2"><i class="fa fa-comments text-blue-500"></i> Pertanyaan Umum</h2>
      <div class="space-y-3">
        @foreach([
          ['Di mana kode pendaftaran saya?','Kode pendaftaran dikirim via email setelah Anda berhasil mengisi formulir. Cek inbox atau folder spam email Anda.'],
          ['Kapan pembayaran bisa dilakukan?','Pembayaran baru bisa dilakukan setelah admin memverifikasi berkas dan status berubah menjadi "Berkas Diterima".'],
          ['Berapa lama proses verifikasi?','Proses verifikasi biasanya 1-3 hari kerja. Anda akan mendapat notifikasi email setelah berkas diverifikasi.'],
          ['Bagaimana jika berkas ditolak?','Admin akan memberikan alasan penolakan. Anda bisa menghubungi sekolah untuk informasi lebih lanjut.'],
        ] as $faq)
        <details class="group border border-gray-200 rounded-xl overflow-hidden">
          <summary class="flex items-center justify-between p-4 cursor-pointer bg-gray-50 hover:bg-gray-100 transition text-sm font-semibold text-gray-700">
            {{ $faq[0] }} <i class="fa fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
          </summary>
          <div class="p-4 text-sm text-gray-600">{{ $faq[1] }}</div>
        </details>
        @endforeach
      </div>
    </div>

    <!-- Kode contoh untuk testing -->
    <div class="card p-6">
      <h2 class="font-bold text-gray-800 text-lg mb-1 flex items-center gap-2"><i class="fa fa-flask text-blue-500"></i> Coba Kode Contoh</h2>
      <p class="text-xs text-gray-500 mb-4">Klik salah satu kode di bawah untuk mencobanya:</p>
      @php
      $dummyCodes = \App\Models\Pendaftaran::with('siswa')->latest()->limit(6)->get();
      @endphp
      @if($dummyCodes->count())
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        @foreach($dummyCodes as $d)
        <button onclick="fillKode('{{ $d->kode_regis }}')"
          class="flex items-center justify-between p-3 border border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition text-left group">
          <div>
            <div class="font-mono font-bold text-blue-700 text-sm">{{ $d->kode_regis }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $d->siswa?->nama_siswa }} — <span class="badge {{ $d->badge_color }} text-xs">{{ $d->label_status }}</span></div>
          </div>
          <i class="fa fa-arrow-right text-gray-300 group-hover:text-blue-500 transition text-xs"></i>
        </button>
        @endforeach
      </div>
      @endif
    </div>

    <!-- Butuh bantuan -->
    <div class="card p-6" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
          <i class="fa fa-headset text-white text-xl"></i>
        </div>
        <div class="flex-1">
          <div class="font-bold text-blue-900">Butuh Bantuan?</div>
          <div class="text-sm text-blue-700 mt-0.5">Hubungi admin via WhatsApp untuk bantuan lebih lanjut.</div>
        </div>
        <a href="https://wa.me/{{ env('ADMIN_WHATSAPP','6289514392694') }}?text={{ urlencode('Halo, saya butuh bantuan cek status PPDB.') }}"
           target="_blank" class="btn btn-sm" style="background:#25d366;color:#fff;">
          <i class="fab fa-whatsapp"></i> WhatsApp Admin
        </a>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection

@push('head')
<script src="{{ config('midtrans.snap_js_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@push('scripts')
<script>
function selectMetode(id) {
  document.querySelectorAll('.metode-tab').forEach(t => {
    t.classList.toggle('btn-primary', parseInt(t.dataset.metode) === id);
    t.classList.toggle('btn-secondary', parseInt(t.dataset.metode) !== id);
  });
  document.querySelectorAll('.metode-panel').forEach(p => p.classList.add('hidden'));
  document.getElementById('panel-' + id)?.classList.remove('hidden');
}
document.addEventListener('DOMContentLoaded', () => {
  const first = document.querySelector('.metode-tab');
  if (first) first.click();
});

function copyKode(val) {
  navigator.clipboard.writeText(val).then(() => {
    const ci = document.getElementById('ci');
    if (ci) { ci.className = 'fa fa-check text-xs text-green-600'; setTimeout(() => ci.className = 'fa fa-copy text-xs', 2000); }
    Swal.fire({icon:'success',title:'Disalin!',text:val,timer:2000,showConfirmButton:false,toast:true,position:'top-end'});
  });
}

function fillKode(kode) {
  const input = document.getElementById('kode-input');
  if (input) { input.value = kode; input.form.submit(); }
}

function bayarMidtrans(kode) {
  const btn = document.getElementById('btn-midtrans');
  const loading = document.getElementById('midtrans-loading');
  btn.disabled = true; loading.classList.remove('hidden');

  fetch('{{ route("bayar.snapToken") }}', {
    method: 'POST',
    headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
    body: JSON.stringify({kode_regis: kode})
  })
  .then(r => r.json())
  .then(data => {
    loading.classList.add('hidden'); btn.disabled = false;
    if (!data.status) { Swal.fire({icon:'error',title:'Gagal',text:data.message}); return; }
    window.snap.pay(data.snap_token, {
      onSuccess: result => {
        fetch('{{ route("bayar.paymentSuccess") }}', {
          method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
          body: JSON.stringify({order_id: result.order_id})
        }).then(r=>r.json()).then(d => { if(d.redirect) location.href=d.redirect; else location.reload(); });
      },
      onPending: () => Swal.fire({icon:'info',title:'Pembayaran Tertunda',text:'Selesaikan pembayaran sesuai instruksi.'}).then(()=>location.reload()),
      onError:   () => Swal.fire({icon:'error',title:'Gagal',text:'Pembayaran gagal. Coba lagi.'}),
      onClose:   () => { btn.disabled=false; loading.classList.add('hidden'); }
    });
  })
  .catch(() => { loading.classList.add('hidden'); btn.disabled=false; Swal.fire({icon:'error',title:'Koneksi Gagal'}); });
}

// Star rating
function setRating(r) {
  document.getElementById('rating-val').value = r;
  document.querySelectorAll('.star-btn').forEach((b, i) => {
    b.style.color = i < r ? '#f59e0b' : '#d1d5db';
  });
}
</script>
@endpush
