@extends('layouts.app')
@section('title','Formulir Pendaftaran')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
  <div class="text-center mb-8">
    <h1 class="text-2xl font-extrabold text-gray-900">Formulir Pendaftaran PPDB</h1>
    <p class="text-gray-500 text-sm mt-1">Lengkapi semua langkah dengan data yang benar.</p>
  </div>

  <div class="step-bar mb-8 px-2">
    @foreach(['Sekolah','Data Diri','Jalur','Orang Tua','Dokumen','Review'] as $i=>$l)
    <div class="step-item" id="sb-{{ $i }}"><div class="step-circle">{{ $i+1 }}</div><div class="step-label">{{ $l }}</div></div>
    @endforeach
  </div>

  @if($errors->any())
  <div class="alert alert-danger mb-6"><i class="fa fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
    <div><strong>Terdapat kesalahan:</strong><ul class="mt-1 list-disc list-inside text-xs space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  </div>
  @endif

  <form id="ppdb-form" action="{{ route('daftar.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    <!-- STEP 1: SEKOLAH -->
    <div class="step-pane active" id="pane-0">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-school text-blue-600"></i> Pilih Sekolah & Jurusan</h2></div>
      <div class="card-body space-y-5">
        <div>
          <label class="form-label">Pilih Sekolah <span class="req">*</span></label>
          <p class="text-xs text-gray-400 mb-3">Pilih satu sekolah tujuan Anda.</p>
          <div class="space-y-2">
            @foreach($sekolahs as $s)
            @php $bg=['SMP'=>'#eff6ff','SMA'=>'#f0fdf4','SMK'=>'#fff7ed'][$s->tingkatan]??'#eff6ff';
                 $cl=['SMP'=>'#1e40af','SMA'=>'#065f46','SMK'=>'#9a3412'][$s->tingkatan]??'#1e40af';
                 $ac=['SMP'=>'#3b82f6','SMA'=>'#059669','SMK'=>'#ea580c'][$s->tingkatan]??'#3b82f6'; @endphp
            <label class="sk-item flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all hover:shadow-md border-gray-200 bg-white"
                   data-id="{{ $s->id }}" data-t="{{ $s->tingkatan }}" onclick="pickSekolah({{ $s->id }},'{{ $s->tingkatan }}',this)">
              <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold text-xs flex-shrink-0" style="background:{{ $bg }};color:{{ $cl }};">{{ $s->singkatan }}</div>
              <div class="flex-1 min-w-0">
                <div class="font-bold text-gray-800 text-sm">{{ $s->nama_sekolah }}</div>
                <div class="text-xs text-gray-500 flex gap-2 mt-0.5 flex-wrap">
                  <span class="px-2 py-0.5 rounded-full font-bold" style="background:{{ $bg }};color:{{ $ac }};">{{ $s->tingkatan }}</span>
                  <span><i class="fa fa-map-marker-alt"></i> {{ $s->kota }}</span>
                  <span><i class="fa fa-users"></i> {{ $s->kuota }} kuota</span>
                </div>
              </div>
              <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center flex-shrink-0 transition-all" id="rdt-{{ $s->id }}">
                <div class="w-2.5 h-2.5 rounded-full bg-blue-600 hidden" id="rin-{{ $s->id }}"></div>
              </div>
              <input type="radio" name="sekolah_id" value="{{ $s->id }}" class="hidden" id="rr-{{ $s->id }}">
            </label>
            @endforeach
          </div>
        </div>
        <div id="jur-wrap" class="hidden">
          <label class="form-label">Jurusan <span id="jur-req" class="req hidden">*</span><span id="jur-opt" class="text-xs text-gray-400"></span></label>
          <select name="jurusan_id" id="jurusan_id" class="form-input"><option value="">— Pilih Jurusan —</option></select>
        </div>
      </div></div>
    </div>

    <!-- STEP 2: DATA DIRI -->
    <div class="step-pane" id="pane-1">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-user text-blue-600"></i> Data Diri Siswa</h2></div>
      <div class="card-body space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2"><label class="form-label">Nama Lengkap <span class="req">*</span></label><input type="text" name="nama_lengkap" id="fn" class="form-input" value="{{ old('nama_lengkap') }}" required></div>
          <div><label class="form-label">NISN <span class="req">*</span></label><input type="text" name="nisn" id="fi" class="form-input" value="{{ old('nisn') }}" maxlength="10" required></div>
          <div><label class="form-label">Jenis Kelamin <span class="req">*</span></label>
            <div class="radio-group mt-1">
              <label class="radio-item"><input type="radio" name="jenis_kelamin" id="fjkl" value="laki_laki" {{ old('jenis_kelamin')=='laki_laki'?'checked':'' }}><i class="fa fa-mars text-blue-500"></i> Laki-Laki</label>
              <label class="radio-item"><input type="radio" name="jenis_kelamin" id="fjkp" value="perempuan" {{ old('jenis_kelamin')=='perempuan'?'checked':'' }}><i class="fa fa-venus text-pink-500"></i> Perempuan</label>
            </div>
          </div>
          <div><label class="form-label">Tempat Lahir <span class="req">*</span></label><input type="text" name="tempat_lahir" id="ftl" class="form-input" value="{{ old('tempat_lahir') }}" required></div>
          <div><label class="form-label">Tanggal Lahir <span class="req">*</span></label><input type="date" name="tanggal_lahir" id="ftg" class="form-input" value="{{ old('tanggal_lahir') }}" required></div>
          <div><label class="form-label">Agama <span class="req">*</span></label>
            <select name="agama" id="fag" class="form-input" required><option value="">— Pilih —</option>
              @foreach(['islam'=>'Islam','protestan'=>'Kristen Protestan','katolik'=>'Katolik','hindu'=>'Hindu','budha'=>'Budha','khonghucu'=>'Konghucu'] as $v=>$l)
              <option value="{{ $v }}" {{ old('agama')==$v?'selected':'' }}>{{ $l }}</option>@endforeach
            </select>
          </div>
          <div><label class="form-label">No. HP <span class="req">*</span></label><input type="text" name="phone" id="fph" class="form-input" value="{{ old('phone') }}" required></div>
          <div><label class="form-label">Email <span class="req">*</span></label><input type="email" name="email" id="fem" class="form-input" value="{{ old('email') }}" required></div>
          <div class="md:col-span-2"><label class="form-label">Alamat <span class="req">*</span></label><textarea name="alamat" id="fal" class="form-input" rows="2" required>{{ old('alamat') }}</textarea></div>
          <div><label class="form-label">Asal Sekolah <span class="req">*</span></label><input type="text" name="asal_sekolah" id="fas" class="form-input" value="{{ old('asal_sekolah') }}" required></div>
          <div><label class="form-label">Tahun Lulus <span class="req">*</span></label><input type="number" name="tahun_lulus" id="fyr" class="form-input" value="{{ old('tahun_lulus',date('Y')) }}" min="{{ date('Y')-10 }}" max="{{ date('Y') }}" required></div>
          <div class="md:col-span-2"><label class="form-label">No. Ijazah <span class="req">*</span></label><input type="text" name="nomor_ijazah" id="fij" class="form-input" value="{{ old('nomor_ijazah') }}" required></div>
        </div>
      </div></div>
    </div>

    <!-- STEP 3: JALUR — default kosong (tidak ada yang terpilih) -->
    <div class="step-pane" id="pane-2">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-route text-blue-600"></i> Jalur Pendaftaran</h2></div>
      <div class="card-body space-y-4">
        <label class="form-label">Pilih Jalur <span class="req">*</span></label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          @foreach(['reguler'=>['fa-users','Reguler','Jalur umum, terbuka untuk semua calon siswa.','#3b82f6','#eff6ff'],'prestasi'=>['fa-trophy','Prestasi','Memiliki prestasi akademik/non-akademik. Lampirkan sertifikat.','#d97706','#fef3c7'],'afirmasi'=>['fa-hand-holding-heart','Afirmasi','Bagi siswa kurang mampu. Lampirkan SKTM.','#059669','#d1fae5'],'pindahan'=>['fa-arrows-rotate','Pindahan','Pindah dari sekolah lain. Lampirkan surat pindah.','#7c3aed','#ede9fe']] as $val=>$j)
          <label class="jalur-card border-2 border-gray-200 rounded-xl p-4 cursor-pointer transition-all hover:border-blue-400 hover:shadow-sm bg-white" id="jc-{{ $val }}" onclick="pickJalur('{{ $val }}')">
            <input type="radio" name="jalur_pendaftaran" value="{{ $val }}" class="hidden" id="jr-{{ $val }}">
            <div class="flex items-start gap-3">
              <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $j[4] }};"><i class="fa {{ $j[0] }} text-sm" style="color:{{ $j[3] }};"></i></div>
              <div><div class="font-bold text-gray-800 text-sm">{{ $j[1] }}</div><div class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $j[2] }}</div></div>
            </div>
          </label>
          @endforeach
        </div>
        <div id="ket-wrap" class="hidden space-y-3">
          <div><label class="form-label">Keterangan <span class="req">*</span></label><textarea name="ket_jalur" id="fkt" class="form-input" rows="3">{{ old('ket_jalur') }}</textarea></div>
          <div id="lamp-wrap" class="hidden"><label class="form-label">Lampiran Bukti <span class="req">*</span></label>
            <div class="file-input-wrapper"><input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png"><div class="file-input-display"><i class="fa fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i><div class="file-label text-sm text-gray-500">PDF/JPG/PNG — Maks 20MB</div></div></div>
          </div>
        </div>
      </div></div>
    </div>

    <!-- STEP 4: ORANG TUA — localStorage -->
    <div class="step-pane" id="pane-3">
      <div class="card"><div class="card-header flex items-center justify-between">
        <h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-users text-blue-600"></i> Data Orang Tua / Wali</h2>
        <button type="button" onclick="addWali()" class="btn btn-sm btn-outline"><i class="fa fa-plus"></i> Tambah</button>
      </div>
      <div class="card-body"><div id="wali-list" class="space-y-4"></div><p class="text-xs text-gray-400 mt-3"><i class="fa fa-info-circle"></i> Minimal 1 data orang tua/wali.</p></div></div>
    </div>

    <!-- STEP 5: DOKUMEN — localStorage -->
    <div class="step-pane" id="pane-4">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-folder-open text-blue-600"></i> Upload Dokumen</h2></div>
      <div class="card-body">
        <div class="alert alert-info mb-5 text-xs"><i class="fa fa-info-circle flex-shrink-0"></i> Format JPG/PNG/PDF, maks <strong>20MB</strong>/file. File bisa tersimpan sementara di browser.</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          @foreach([['pas_foto','Pas Foto','fa-portrait',true,'Latar merah/biru, ukuran 3×4'],['kk','Kartu Keluarga','fa-id-card',true,'Scan KK yang masih berlaku'],['akta','Akta Kelahiran','fa-file-alt',true,'Scan akta kelahiran'],['ijazah','Ijazah / STTB','fa-graduation-cap',false,'Jika sudah ada (opsional)'],['skhun','SKHUN','fa-scroll',false,'Opsional'],['stl','Surat Tanda Lulus','fa-certificate',false,'Opsional']] as $d)
          <div><label class="form-label">{{ $d[1] }} @if($d[3])<span class="req">*</span>@else<span class="text-xs text-gray-400">(opsional)</span>@endif</label>
            <div class="file-input-wrapper"><input type="file" name="{{ $d[0] }}" id="fd_{{ $d[0] }}" accept=".pdf,.jpg,.jpeg,.png" {{ $d[3]?'required':'' }}><div class="file-input-display"><i class="fa {{ $d[2] }} text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">{{ $d[4] }}</div></div></div>
          </div>
          @endforeach
        </div>
      </div></div>
    </div>

    <!-- STEP 6: REVIEW -->
    <div class="step-pane" id="pane-5">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-clipboard-check text-blue-600"></i> Review Pendaftaran</h2></div>
      <div class="card-body space-y-4">
        <div id="rev-box"></div>
        <div class="alert alert-warning text-sm"><i class="fa fa-triangle-exclamation flex-shrink-0"></i><div>Pastikan semua data <strong>benar</strong> sebelum mengirim. Data tidak dapat diubah setelah dikirim.</div></div>
        <div class="flex items-start gap-2"><input type="checkbox" id="setuju" class="w-4 h-4 accent-blue-700 mt-0.5 flex-shrink-0" required><label for="setuju" class="text-sm text-gray-600">Saya menyatakan bahwa data yang diisi adalah <strong>benar dan dapat dipertanggungjawabkan</strong>.</label></div>
        <div id="email-badge" class="email-sending"><i class="fa fa-circle-notch spin"></i> Mengirim pendaftaran & email konfirmasi...</div>
      </div></div>
    </div>

    <div class="flex items-center justify-between mt-6">
      <button type="button" id="btn-prev" onclick="prevS()" class="btn btn-secondary hidden"><i class="fa fa-arrow-left"></i> Sebelumnya</button>
      <div class="ml-auto flex gap-3">
        <button type="button" id="btn-next" onclick="nextS()" class="btn btn-primary">Selanjutnya <i class="fa fa-arrow-right"></i></button>
        <button type="submit" id="btn-sub" class="btn btn-success hidden" onclick="onSub()"><i class="fa fa-paper-plane"></i> Kirim Pendaftaran</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
const SK='ppdb_v3';
let cs=0,tot=6,wc=0,skId=null,skT=null;

// ===== STEP =====
function upUI(){
  document.querySelectorAll('.step-pane').forEach((p,i)=>p.classList.toggle('active',i===cs));
  document.querySelectorAll('.step-item').forEach((it,i)=>{it.classList.toggle('active',i===cs);it.classList.toggle('done',i<cs);});
  document.getElementById('btn-prev').classList.toggle('hidden',cs===0);
  document.getElementById('btn-next').classList.toggle('hidden',cs===tot-1);
  document.getElementById('btn-sub').classList.toggle('hidden',cs!==tot-1);
  if(cs===tot-1)buildRev();
  window.scrollTo({top:0,behavior:'smooth'});
}
function nextS(){if(!valS(cs))return;save();cs=Math.min(cs+1,tot-1);upUI();}
function prevS(){save();cs=Math.max(cs-1,0);upUI();}

// ===== VALIDATE =====
function valS(s){
  if(s===0&&!skId){Swal.fire({icon:'warning',title:'Pilih Sekolah',confirmButtonColor:'#1e40af'});return false;}
  if(s===1){
    let ok=true;
    ['fn','fi','ftl','ftg','fph','fem','fas','fyr','fij'].forEach(id=>{const el=document.getElementById(id);if(el&&!el.value.trim()){el.classList.add('error');ok=false;}else el?.classList.remove('error');});
    if(!document.querySelector('input[name="jenis_kelamin"]:checked'))ok=false;
    if(!document.getElementById('fag').value)ok=false;
    if(!ok){Swal.fire({icon:'warning',title:'Data Belum Lengkap',text:'Mohon lengkapi semua field wajib.',confirmButtonColor:'#1e40af'});return false;}
  }
  if(s===2&&!document.querySelector('input[name="jalur_pendaftaran"]:checked')){Swal.fire({icon:'warning',title:'Pilih Jalur Pendaftaran',confirmButtonColor:'#1e40af'});return false;}
  if(s===3&&document.querySelectorAll('.wali-row').length===0){Swal.fire({icon:'warning',title:'Data Wali Kosong',text:'Tambahkan minimal 1 data orang tua/wali.',confirmButtonColor:'#1e40af'});return false;}
  if(s===4){let ok=true;['pas_foto','kk','akta'].forEach(n=>{const el=document.getElementById('fd_'+n);if(el&&el.files.length===0){ok=false;}});if(!ok){Swal.fire({icon:'warning',title:'Dokumen Belum Lengkap',text:'Upload pas foto, KK, dan akta kelahiran.',confirmButtonColor:'#1e40af'});return false;}}
  if(s===5&&!document.getElementById('setuju').checked){Swal.fire({icon:'warning',title:'Centang Persetujuan',confirmButtonColor:'#1e40af'});return false;}
  return true;
}

// ===== SEKOLAH =====
function pickSekolah(id,t,el){
  skId=id;skT=t;
  document.querySelectorAll('.sk-item').forEach(x=>{x.classList.remove('border-blue-500','bg-blue-50','shadow-md');x.classList.add('border-gray-200');});
  document.querySelectorAll('[id^="rdt-"]').forEach(x=>x.classList.remove('border-blue-600'));
  document.querySelectorAll('[id^="rin-"]').forEach(x=>x.classList.add('hidden'));
  el.classList.add('border-blue-500','bg-blue-50','shadow-md');el.classList.remove('border-gray-200');
  document.getElementById('rdt-'+id).classList.add('border-blue-600');
  document.getElementById('rin-'+id).classList.remove('hidden');
  document.getElementById('rr-'+id).checked=true;
  loadJurusan(id,t);
  save();
}
function loadJurusan(id,t){
  const w=document.getElementById('jur-wrap'),s=document.getElementById('jurusan_id');
  if(!id){w.classList.add('hidden');return;}
  fetch(`{{ route('api.jurusan') }}?sekolah_id=${id}`).then(r=>r.json()).then(d=>{
    s.innerHTML='<option value="">— Pilih Jurusan —</option>';
    d.forEach(j=>s.insertAdjacentHTML('beforeend',`<option value="${j.id}">${j.nama_jurusan}</option>`));
    w.classList.toggle('hidden',d.length===0);
    const need=['SMA','SMK'].includes(t)&&d.length>0;
    document.getElementById('jur-req').classList.toggle('hidden',!need);
    document.getElementById('jur-opt').textContent=(!need&&d.length>0)?' (opsional)':'';
    const sv=getSaved();if(sv.jurusan_id)s.value=sv.jurusan_id;
  });
}

// ===== JALUR — default kosong =====
function pickJalur(v){
  document.querySelectorAll('.jalur-card').forEach(c=>{
    const on=c.id==='jc-'+v;
    c.style.borderColor=on?'#3b82f6':'';c.classList.toggle('border-blue-500',on);c.classList.toggle('bg-blue-50',on);c.classList.toggle('border-gray-200',!on);c.classList.toggle('bg-white',!on);
  });
  document.getElementById('jr-'+v).checked=true;
  document.getElementById('ket-wrap').classList.toggle('hidden',v==='reguler');
  document.getElementById('lamp-wrap').classList.toggle('hidden',!['prestasi','afirmasi'].includes(v));
  save();
}

// ===== WALI =====
function addWali(data={}){
  const i=wc++;
  document.getElementById('wali-list').insertAdjacentHTML('beforeend',`
  <div class="wali-row" id="wr-${i}">
    <div class="flex items-center justify-between mb-3">
      <span class="text-sm font-bold text-gray-700"><i class="fa fa-user-tie text-blue-500 mr-1"></i>Wali ${i+1}</span>
      ${i>0?`<button type="button" onclick="rmWali(${i})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>`:''}
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="md:col-span-2"><label class="form-label">Nama Lengkap <span class="req">*</span></label><input type="text" name="wali[${i}][nama_wali]" class="form-input wi" data-i="${i}" data-k="nama_wali" value="${esc(data.nama_wali||'')}" required></div>
      <div><label class="form-label">Status <span class="req">*</span></label><div class="radio-group mt-1">
        <label class="radio-item"><input type="radio" name="wali[${i}][jenis_wali]" class="wi" data-i="${i}" data-k="jenis_wali" value="ayah" ${(data.jenis_wali||'ayah')==='ayah'?'checked':''}> Ayah</label>
        <label class="radio-item"><input type="radio" name="wali[${i}][jenis_wali]" class="wi" data-i="${i}" data-k="jenis_wali" value="ibu" ${data.jenis_wali==='ibu'?'checked':''}> Ibu</label>
        <label class="radio-item"><input type="radio" name="wali[${i}][jenis_wali]" class="wi" data-i="${i}" data-k="jenis_wali" value="wali" ${data.jenis_wali==='wali'?'checked':''}> Wali</label>
      </div></div>
      <div><label class="form-label">Pekerjaan <span class="req">*</span></label><input type="text" name="wali[${i}][pekerjaan]" class="form-input wi" data-i="${i}" data-k="pekerjaan" value="${esc(data.pekerjaan||'')}" required></div>
      <div><label class="form-label">No. HP</label><input type="text" name="wali[${i}][notelp_wali]" class="form-input wi" data-i="${i}" data-k="notelp_wali" value="${esc(data.notelp_wali||'')}" placeholder="08xxxxxxxxxx"></div>
      <div><label class="form-label">Email</label><input type="email" name="wali[${i}][email_wali]" class="form-input wi" data-i="${i}" data-k="email_wali" value="${esc(data.email_wali||'')}" placeholder="email@domain.com"></div>
      <div><label class="form-label">NIK</label><input type="text" name="wali[${i}][nik]" class="form-input wi" data-i="${i}" data-k="nik" value="${esc(data.nik||'')}" maxlength="16"></div>
    </div>
  </div>`);
  document.querySelectorAll(`#wr-${i} .radio-item input`).forEach(r=>{r.addEventListener('change',()=>{r.closest('.radio-group')?.querySelectorAll('.radio-item').forEach(x=>x.classList.remove('selected'));if(r.checked)r.closest('.radio-item')?.classList.add('selected');});if(r.checked)r.closest('.radio-item')?.classList.add('selected');});
  document.querySelectorAll(`#wr-${i} .wi`).forEach(el=>el.addEventListener('input',save));
}
function rmWali(i){document.getElementById('wr-'+i)?.remove();save();}
function esc(s){return String(s).replace(/"/g,'&quot;').replace(/</g,'&lt;');}

// ===== REVIEW =====
function buildRev(){
  const g=id=>document.getElementById(id)?.value||'—';
  const gr=n=>document.querySelector(`input[name="${n}"]:checked`)?.value||'—';
  const skName=document.querySelector('.sk-item.border-blue-500 .font-bold')?.textContent||'—';
  const jrName=document.getElementById('jurusan_id')?.selectedOptions[0]?.text||'—';
  const jkLbl={laki_laki:'Laki-Laki',perempuan:'Perempuan'}[gr('jenis_kelamin')]||'—';
  const agLbl={islam:'Islam',protestan:'Kristen Protestan',katolik:'Katolik',hindu:'Hindu',budha:'Budha',khonghucu:'Konghucu'}[g('fag')]||'—';
  const jalurLbl={reguler:'Reguler',prestasi:'Prestasi',afirmasi:'Afirmasi',pindahan:'Pindahan'}[gr('jalur_pendaftaran')]||'—';

  let waliHTML='';
  document.querySelectorAll('.wali-row').forEach((row,idx)=>{
    const nm=row.querySelector('[data-k="nama_wali"]')?.value||'—';
    const st=row.querySelector('input[data-k="jenis_wali"]:checked')?.value||'—';
    const pj=row.querySelector('[data-k="pekerjaan"]')?.value||'—';
    const hp=row.querySelector('[data-k="notelp_wali"]')?.value||'—';
    waliHTML+=`<div class="text-xs grid grid-cols-2 gap-x-4 gap-y-1 pb-2 mb-2 border-b border-gray-100 last:border-0"><span class="text-gray-400">Nama Wali ${idx+1}</span><span class="font-semibold">${nm}</span><span class="text-gray-400">Status</span><span class="font-semibold capitalize">${st}</span><span class="text-gray-400">Pekerjaan</span><span class="font-semibold">${pj}</span><span class="text-gray-400">No. HP</span><span class="font-semibold">${hp}</span></div>`;
  });

  const docs=[];['pas_foto','kk','akta','ijazah','skhun','stl'].forEach(n=>{const el=document.getElementById('fd_'+n);if(el&&el.files.length>0)docs.push(el.files[0].name);});
  const ket=document.getElementById('fkt')?.value;

  document.getElementById('rev-box').innerHTML=`<div class="space-y-4 text-sm">
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
      <div class="text-xs font-bold text-blue-600 uppercase mb-2 flex items-center gap-2"><i class="fa fa-school"></i> Sekolah Tujuan</div>
      <div class="font-bold text-gray-900">${skName}</div>
      ${jrName!=='—'&&jrName?`<div class="text-xs text-gray-500 mt-0.5">Jurusan: ${jrName}</div>`:''}
      <div class="text-xs mt-1.5"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">Jalur ${jalurLbl}</span></div>
      ${ket?`<div class="text-xs text-gray-500 mt-2 bg-white rounded-lg p-2">${ket}</div>`:''}
    </div>
    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
      <div class="text-xs font-bold text-gray-500 uppercase mb-2 flex items-center gap-2"><i class="fa fa-user"></i> Data Diri Siswa</div>
      <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-xs">
        <span class="text-gray-400">Nama Lengkap</span><span class="font-semibold text-gray-800">${g('fn')}</span>
        <span class="text-gray-400">NISN</span><span class="font-semibold">${g('fi')}</span>
        <span class="text-gray-400">Jenis Kelamin</span><span class="font-semibold">${jkLbl}</span>
        <span class="text-gray-400">Tempat, Tgl Lahir</span><span class="font-semibold">${g('ftl')}, ${g('ftg')}</span>
        <span class="text-gray-400">Agama</span><span class="font-semibold">${agLbl}</span>
        <span class="text-gray-400">No. HP</span><span class="font-semibold">${g('fph')}</span>
        <span class="text-gray-400">Email</span><span class="font-semibold">${g('fem')}</span>
        <span class="text-gray-400">Alamat</span><span class="font-semibold">${g('fal')}</span>
        <span class="text-gray-400">Asal Sekolah</span><span class="font-semibold">${g('fas')}</span>
        <span class="text-gray-400">Tahun Lulus</span><span class="font-semibold">${g('fyr')}</span>
        <span class="text-gray-400">No. Ijazah</span><span class="font-semibold">${g('fij')}</span>
      </div>
    </div>
    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
      <div class="text-xs font-bold text-gray-500 uppercase mb-2 flex items-center gap-2"><i class="fa fa-users"></i> Data Orang Tua / Wali</div>
      ${waliHTML||'<div class="text-xs text-gray-400">Belum ada data wali.</div>'}
    </div>
    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
      <div class="text-xs font-bold text-gray-500 uppercase mb-2 flex items-center gap-2"><i class="fa fa-folder-open"></i> Dokumen Diupload</div>
      ${docs.length>0?`<div class="flex flex-wrap gap-1">${docs.map(d=>`<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full"><i class="fa fa-check mr-1"></i>${d}</span>`).join('')}</div>`:'<div class="text-xs text-gray-400">Belum ada dokumen dipilih.</div>'}
    </div>
  </div>`;
}

// ===== LOCALSTORAGE =====
function getSaved(){try{return JSON.parse(localStorage.getItem(SK)||'{}');}catch(e){return {};}}
function save(){
  const d=getSaved();
  // text fields
  const ids={fn:'nama_lengkap',fi:'nisn',ftl:'tempat_lahir',ftg:'tanggal_lahir',fag:'agama',fph:'phone',fem:'email',fal:'alamat',fas:'asal_sekolah',fyr:'tahun_lulus',fij:'nomor_ijazah',fkt:'ket_jalur'};
  Object.entries(ids).forEach(([id,k])=>{const el=document.getElementById(id);if(el)d[k]=el.value;});
  // radios
  const jk=document.querySelector('input[name="jenis_kelamin"]:checked');if(jk)d.jk=jk.value;
  const jl=document.querySelector('input[name="jalur_pendaftaran"]:checked');if(jl)d.jalur=jl.value;
  // sekolah + jurusan
  if(skId)d.sk=skId;if(skT)d.skt=skT;
  const jr=document.getElementById('jurusan_id');if(jr&&jr.value)d.jr=jr.value;
  // wali
  const walis=[];document.querySelectorAll('.wali-row').forEach(row=>{
    const w={};row.querySelectorAll('[data-k]').forEach(el=>{if(el.type!=='radio')w[el.dataset.k]=el.value;});
    const jr2=row.querySelector('input[data-k="jenis_wali"]:checked');w.jenis_wali=jr2?.value||'ayah';
    walis.push(w);
  });
  d._w=walis;
  try{localStorage.setItem(SK,JSON.stringify(d));}catch(e){}
}
function load(){
  const d=getSaved();
  const ids={fn:'nama_lengkap',fi:'nisn',ftl:'tempat_lahir',ftg:'tanggal_lahir',fag:'agama',fph:'phone',fem:'email',fal:'alamat',fas:'asal_sekolah',fyr:'tahun_lulus',fij:'nomor_ijazah',fkt:'ket_jalur'};
  Object.entries(ids).forEach(([id,k])=>{const el=document.getElementById(id);if(el&&d[k])el.value=d[k];});
  if(d.jk){const r=document.querySelector(`input[name="jenis_kelamin"][value="${d.jk}"]`);if(r){r.checked=true;r.closest('.radio-item')?.classList.add('selected');}}
  if(d.jalur)pickJalur(d.jalur);
  if(d.sk){const lbl=document.querySelector(`.sk-item[data-id="${d.sk}"]`);if(lbl)pickSekolah(parseInt(d.sk),d.skt||'SMP',lbl);}
  if(d._w&&d._w.length>0)d._w.forEach(w=>addWali(w));else addWali();
  if(d.fag){const el=document.getElementById('fag');if(el)el.value=d.fag;}
}
function clearSaved(){try{localStorage.removeItem(SK);}catch(e){}}
function onSub(){document.getElementById('email-badge').classList.add('show');clearSaved();}

// ===== INIT =====
document.addEventListener('DOMContentLoaded',()=>{
  load();upUI();
  // auto-save
  document.querySelectorAll('#ppdb-form input:not([type="file"]),#ppdb-form select,#ppdb-form textarea').forEach(el=>{el.addEventListener('change',save);el.addEventListener('input',save);});
  document.getElementById('jurusan_id')?.addEventListener('change',save);
  // file display
  document.querySelectorAll('.file-input-wrapper input[type="file"]').forEach(f=>{f.addEventListener('change',()=>{const d=f.closest('.file-input-wrapper')?.querySelector('.file-input-display');if(!d)return;if(f.files.length>0){d.classList.add('has-file');const l=d.querySelector('.file-label');if(l)l.textContent=f.files[0].name;}});});
  setInterval(save,15000);
  // URL param sekolah
  const u=new URLSearchParams(location.search).get('sekolah');
  if(u){const lbl=document.querySelector(`.sk-item[data-id="${u}"]`);if(lbl)pickSekolah(parseInt(u),lbl.dataset.t,lbl);}
});
</script>
@endpush
