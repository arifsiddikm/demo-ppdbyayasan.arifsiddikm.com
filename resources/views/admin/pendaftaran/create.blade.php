@extends('admin.layouts.app')
@section('title','Buat Pendaftaran')
@section('breadcrumb','Buat Pendaftaran')

@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-plus-circle text-blue-600"></i> Buat Pendaftaran (Admin)</h1>
  <a href="{{ route('admin.pendaftaran.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>

<div class="max-w-3xl">
  <!-- Step bar -->
  <div class="step-bar mb-8 px-2">
    @php $stepLabels=['Sekolah','Data Diri','Jalur','Orang Tua','Dokumen','Review']; @endphp
    @foreach($stepLabels as $i=>$l)
    <div class="step-item" id="sb-{{ $i }}"><div class="step-circle">{{ $i+1 }}</div><div class="step-label">{{ $l }}</div></div>
    @endforeach
  </div>

  @if($errors->any())
  <div class="alert alert-danger mb-5"><i class="fa fa-circle-exclamation flex-shrink-0"></i>
    <div><strong>Kesalahan:</strong><ul class="mt-1 list-disc list-inside text-xs">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  </div>
  @endif

  <form id="form-admin-daftar" action="{{ route('admin.pendaftaran.store') }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    <!-- STEP 1: SEKOLAH -->
    <div class="step-pane active" id="pane-0">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-school text-blue-600"></i> Pilih Sekolah & Jurusan</h2></div>
      <div class="card-body space-y-4">
        <div>
          <label class="form-label">Pilih Sekolah <span class="req">*</span></label>
          <div class="space-y-2" id="sekolah-list">
            @foreach($sekolahs as $s)
            @php $bg=['SMP'=>'#eff6ff','SMA'=>'#f0fdf4','SMK'=>'#fff7ed'][$s->tingkatan]??'#eff6ff';
                 $cl=['SMP'=>'#1e40af','SMA'=>'#065f46','SMK'=>'#9a3412'][$s->tingkatan]??'#1e40af'; @endphp
            <label class="sk-item flex items-center gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all hover:shadow-md border-gray-200 bg-white"
                   data-id="{{ $s->id }}" data-t="{{ $s->tingkatan }}" onclick="pickSekolah({{ $s->id }},'{{ $s->tingkatan }}',this)">
              <div class="flex-shrink-0"><div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs" style="background:{{ $bg }};color:{{ $cl }};">{{ $s->singkatan }}</div></div>
              <div class="flex-1"><div class="font-bold text-gray-800 text-sm">{{ $s->nama_sekolah }}</div>
                <div class="text-xs text-gray-500 flex gap-2 mt-0.5">
                  <span style="background:{{ $bg }};color:{{ $cl }};padding:1px 8px;border-radius:20px;font-weight:700;">{{ $s->tingkatan }}</span>
                  <span><i class="fa fa-map-marker-alt"></i> {{ $s->kota }}</span>
                </div>
              </div>
              <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center sk-dot-{{ $s->id }} transition-all flex-shrink-0">
                <div class="w-2.5 h-2.5 rounded-full bg-blue-600 hidden sk-inner-{{ $s->id }}"></div>
              </div>
              <input type="radio" name="sekolah_id" value="{{ $s->id }}" class="hidden sk-radio-{{ $s->id }}">
            </label>
            @endforeach
          </div>
        </div>
        <div id="jur-wrap" class="hidden">
          <label class="form-label">Jurusan <span id="jur-req" class="req hidden">*</span></label>
          <select name="jurusan_id" id="jurusan_id" class="form-input"><option value="">— Pilih Jurusan —</option></select>
        </div>
      </div></div>
    </div>

    <!-- STEP 2: DATA DIRI -->
    <div class="step-pane" id="pane-1">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-user text-blue-600"></i> Data Diri Siswa</h2></div>
      <div class="card-body space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2"><label class="form-label">Nama Lengkap <span class="req">*</span></label><input type="text" name="nama_lengkap" class="form-input" required></div>
          <div><label class="form-label">NISN <span class="req">*</span></label><input type="text" name="nisn" class="form-input" maxlength="10" required></div>
          <div><label class="form-label">Jenis Kelamin <span class="req">*</span></label>
            <div class="radio-group mt-1">
              <label class="radio-item"><input type="radio" name="jenis_kelamin" value="laki_laki"> Laki-Laki</label>
              <label class="radio-item"><input type="radio" name="jenis_kelamin" value="perempuan"> Perempuan</label>
            </div>
          </div>
          <div><label class="form-label">Tempat Lahir <span class="req">*</span></label><input type="text" name="tempat_lahir" class="form-input" required></div>
          <div><label class="form-label">Tanggal Lahir <span class="req">*</span></label><input type="date" name="tanggal_lahir" class="form-input" required></div>
          <div><label class="form-label">Agama <span class="req">*</span></label>
            <select name="agama" class="form-input" required><option value="">— Pilih —</option>
              @foreach(['islam'=>'Islam','protestan'=>'Kristen Protestan','katolik'=>'Katolik','hindu'=>'Hindu','budha'=>'Budha','khonghucu'=>'Konghucu'] as $v=>$l)
              <option value="{{ $v }}">{{ $l }}</option>@endforeach
            </select>
          </div>
          <div><label class="form-label">No. HP <span class="req">*</span></label><input type="text" name="phone" class="form-input" required></div>
          <div><label class="form-label">Email <span class="req">*</span></label><input type="email" name="email" class="form-input" required></div>
          <div class="md:col-span-2"><label class="form-label">Alamat <span class="req">*</span></label><textarea name="alamat" class="form-input" rows="2" required></textarea></div>
          <div><label class="form-label">Asal Sekolah <span class="req">*</span></label><input type="text" name="asal_sekolah" class="form-input" required></div>
          <div><label class="form-label">Tahun Lulus <span class="req">*</span></label><input type="number" name="tahun_lulus" class="form-input" value="{{ date('Y') }}" min="{{ date('Y')-10 }}" max="{{ date('Y') }}" required></div>
          <div class="md:col-span-2"><label class="form-label">No. Ijazah <span class="req">*</span></label><input type="text" name="nomor_ijazah" class="form-input" required></div>
        </div>
      </div></div>
    </div>

    <!-- STEP 3: JALUR -->
    <div class="step-pane" id="pane-2">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-route text-blue-600"></i> Jalur Pendaftaran</h2></div>
      <div class="card-body space-y-4">
        <label class="form-label">Pilih Jalur <span class="req">*</span></label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          @foreach(['reguler'=>['fa-users','Reguler','Jalur umum, terbuka untuk semua'],'prestasi'=>['fa-trophy','Prestasi','Memiliki prestasi akademik/non-akademik'],'afirmasi'=>['fa-hand-holding-heart','Afirmasi','Bagi siswa kurang mampu'],'pindahan'=>['fa-arrows-rotate','Pindahan','Pindah dari sekolah lain']] as $val=>$j)
          <label class="jalur-card" id="jc-{{ $val }}" onclick="pickJalur('{{ $val }}')">
            <input type="radio" name="jalur_pendaftaran" value="{{ $val }}" class="hidden" id="jr-{{ $val }}">
            <div class="flex items-start gap-3">
              <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0"><i class="fa {{ $j[0] }} text-blue-600 text-sm"></i></div>
              <div><div class="font-bold text-gray-800 text-sm">{{ $j[1] }}</div><div class="text-xs text-gray-500 mt-0.5">{{ $j[2] }}</div></div>
            </div>
          </label>
          @endforeach
        </div>
        <div id="ket-wrap" class="hidden space-y-3">
          <div><label class="form-label">Keterangan <span class="req">*</span></label><textarea name="ket_jalur" class="form-input" rows="2"></textarea></div>
          <div id="lamp-wrap" class="hidden"><label class="form-label">Lampiran Bukti</label>
            <div class="file-input-wrapper"><input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png"><div class="file-input-display"><i class="fa fa-cloud-upload-alt text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">PDF/JPG/PNG maks 20MB</div></div></div>
          </div>
        </div>
      </div></div>
    </div>

    <!-- STEP 4: WALI -->
    <div class="step-pane" id="pane-3">
      <div class="card"><div class="card-header flex items-center justify-between">
        <h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-users text-blue-600"></i> Data Orang Tua / Wali</h2>
        <button type="button" onclick="addWali()" class="btn btn-sm btn-outline"><i class="fa fa-plus"></i> Tambah</button>
      </div>
      <div class="card-body"><div id="wali-list" class="space-y-3"></div><p class="text-xs text-gray-400 mt-3"><i class="fa fa-info-circle"></i> Minimal 1 data wali.</p>
      </div></div>
    </div>

    <!-- STEP 5: DOKUMEN -->
    <div class="step-pane" id="pane-4">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-folder-open text-blue-600"></i> Upload Dokumen</h2></div>
      <div class="card-body">
        <div class="alert alert-info mb-4 text-xs"><i class="fa fa-info-circle flex-shrink-0"></i> Format JPG/PNG/PDF, maks 20MB per file.</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          @foreach([['pas_foto','Pas Foto','fa-portrait',true],['kk','Kartu Keluarga','fa-id-card',true],['akta','Akta Kelahiran','fa-file-alt',true],['ijazah','Ijazah','fa-graduation-cap',false],['skhun','SKHUN','fa-scroll',false],['stl','Surat Tanda Lulus','fa-certificate',false]] as $d)
          <div><label class="form-label">{{ $d[1] }} @if($d[3])<span class="req">*</span>@else<span class="text-xs text-gray-400">(opsional)</span>@endif</label>
            <div class="file-input-wrapper"><input type="file" name="{{ $d[0] }}" id="af_{{ $d[0] }}" accept=".pdf,.jpg,.jpeg,.png" {{ $d[3]?'required':'' }}><div class="file-input-display"><i class="fa {{ $d[2] }} text-xl text-gray-400 mb-1"></i><div class="file-label text-xs text-gray-500">Klik upload</div></div></div>
          </div>
          @endforeach
        </div>
      </div></div>
    </div>

    <!-- STEP 6: REVIEW -->
    <div class="step-pane" id="pane-5">
      <div class="card"><div class="card-header"><h2 class="font-bold text-gray-800 flex items-center gap-2"><i class="fa fa-clipboard-check text-blue-600"></i> Review & Kirim</h2></div>
      <div class="card-body space-y-4">
        <div id="review-box"></div>
        <div class="alert alert-warning text-xs"><i class="fa fa-triangle-exclamation flex-shrink-0"></i> Pastikan semua data benar sebelum mengirim.</div>
        <div class="flex items-center gap-2"><input type="checkbox" id="setuju" class="accent-blue-700 w-4 h-4" required><label for="setuju" class="text-sm text-gray-600">Data sudah benar dan dapat dipertanggungjawabkan.</label></div>
      </div></div>
    </div>

    <div class="flex items-center justify-between mt-5">
      <button type="button" id="btn-prev" onclick="prevS()" class="btn btn-secondary hidden"><i class="fa fa-arrow-left"></i> Sebelumnya</button>
      <div class="ml-auto flex gap-3">
        <button type="button" id="btn-next" onclick="nextS()" class="btn btn-primary">Selanjutnya <i class="fa fa-arrow-right"></i></button>
        <button type="submit" id="btn-submit" class="btn btn-success hidden"><i class="fa fa-paper-plane"></i> Kirim Pendaftaran</button>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
let cs=0,tot=6,wc=0,skId=null,skT=null;
function upUI(){
  document.querySelectorAll('.step-pane').forEach((p,i)=>p.classList.toggle('active',i===cs));
  document.querySelectorAll('.step-item').forEach((it,i)=>{it.classList.toggle('active',i===cs);it.classList.toggle('done',i<cs);});
  document.getElementById('btn-prev').classList.toggle('hidden',cs===0);
  document.getElementById('btn-next').classList.toggle('hidden',cs===tot-1);
  document.getElementById('btn-submit').classList.toggle('hidden',cs!==tot-1);
  if(cs===tot-1)buildRev();
  window.scrollTo({top:0,behavior:'smooth'});
}
function nextS(){if(!valS(cs))return;cs=Math.min(cs+1,tot-1);upUI();}
function prevS(){cs=Math.max(cs-1,0);upUI();}
function valS(s){
  if(s===0&&!skId){Swal.fire({icon:'warning',title:'Pilih Sekolah',confirmButtonColor:'#1e40af'});return false;}
  if(s===1){
    const req=['nama_lengkap','nisn','tempat_lahir','tanggal_lahir','phone','email','asal_sekolah','tahun_lulus','nomor_ijazah'];
    let ok=true;req.forEach(n=>{const el=document.querySelector(`[name="${n}"]`);if(el&&!el.value.trim())ok=false;});
    if(!document.querySelector('input[name="jenis_kelamin"]:checked'))ok=false;
    if(!document.querySelector('select[name="agama"]').value)ok=false;
    if(!ok){Swal.fire({icon:'warning',title:'Data Belum Lengkap',confirmButtonColor:'#1e40af'});return false;}
  }
  if(s===2&&!document.querySelector('input[name="jalur_pendaftaran"]:checked')){Swal.fire({icon:'warning',title:'Pilih Jalur',confirmButtonColor:'#1e40af'});return false;}
  if(s===3&&document.querySelectorAll('.wali-row').length===0){Swal.fire({icon:'warning',title:'Tambah Data Wali',confirmButtonColor:'#1e40af'});return false;}
  if(s===4){let ok=true;['pas_foto','kk','akta'].forEach(n=>{const el=document.getElementById('af_'+n);if(el&&el.files.length===0)ok=false;});if(!ok){Swal.fire({icon:'warning',title:'Dokumen Wajib',text:'Upload pas foto, KK, dan akta kelahiran.',confirmButtonColor:'#1e40af'});return false;}}
  if(s===5&&!document.getElementById('setuju').checked){Swal.fire({icon:'warning',title:'Centang Persetujuan',confirmButtonColor:'#1e40af'});return false;}
  return true;
}
function pickSekolah(id,t,el){
  skId=id;skT=t;
  document.querySelectorAll('.sk-item').forEach(x=>{x.classList.remove('border-blue-500','bg-blue-50','shadow-md');x.classList.add('border-gray-200');});
  document.querySelectorAll('[class*="sk-dot-"]').forEach(x=>x.classList.remove('border-blue-600'));
  document.querySelectorAll('[class*="sk-inner-"]').forEach(x=>x.classList.add('hidden'));
  el.classList.add('border-blue-500','bg-blue-50','shadow-md');el.classList.remove('border-gray-200');
  el.querySelector('.sk-dot-'+id).classList.add('border-blue-600');
  el.querySelector('.sk-inner-'+id).classList.remove('hidden');
  document.querySelector('.sk-radio-'+id).checked=true;
  fetch(`{{ route('api.jurusan') }}?sekolah_id=${id}`).then(r=>r.json()).then(d=>{
    const w=document.getElementById('jur-wrap'),s=document.getElementById('jurusan_id');
    s.innerHTML='<option value="">— Pilih Jurusan —</option>';
    d.forEach(j=>s.insertAdjacentHTML('beforeend',`<option value="${j.id}">${j.nama_jurusan}</option>`));
    w.classList.toggle('hidden',d.length===0);
    document.getElementById('jur-req').classList.toggle('hidden',!['SMA','SMK'].includes(t)||d.length===0);
  });
}
function pickJalur(v){
  document.querySelectorAll('.jalur-card').forEach(c=>{const on=c.id==='jc-'+v;c.style.borderColor=on?'#3b82f6':'#e5e7eb';c.style.background=on?'#eff6ff':'#fff';c.querySelector('input').checked=on&&true;});
  document.getElementById('jr-'+v).checked=true;
  document.getElementById('ket-wrap').classList.toggle('hidden',v==='reguler');
  document.getElementById('lamp-wrap').classList.toggle('hidden',!['prestasi','afirmasi'].includes(v));
}
function addWali(){
  const i=wc++;
  document.getElementById('wali-list').insertAdjacentHTML('beforeend',`
  <div class="wali-row" id="wr-${i}">
    <div class="flex items-center justify-between mb-3">
      <span class="text-sm font-bold text-gray-700"><i class="fa fa-user-tie text-blue-500 mr-1"></i> Wali ${i+1}</span>
      ${i>0?`<button type="button" onclick="document.getElementById('wr-${i}').remove()" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>`:''}
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div class="md:col-span-2"><label class="form-label">Nama <span class="req">*</span></label><input type="text" name="wali[${i}][nama_wali]" class="form-input" required></div>
      <div><label class="form-label">Status</label><div class="radio-group mt-1">
        <label class="radio-item"><input type="radio" name="wali[${i}][jenis_wali]" value="ayah" checked> Ayah</label>
        <label class="radio-item"><input type="radio" name="wali[${i}][jenis_wali]" value="ibu"> Ibu</label>
        <label class="radio-item"><input type="radio" name="wali[${i}][jenis_wali]" value="wali"> Wali</label>
      </div></div>
      <div><label class="form-label">Pekerjaan <span class="req">*</span></label><input type="text" name="wali[${i}][pekerjaan]" class="form-input" required></div>
      <div><label class="form-label">No. HP</label><input type="text" name="wali[${i}][notelp_wali]" class="form-input"></div>
      <div><label class="form-label">Email</label><input type="email" name="wali[${i}][email_wali]" class="form-input"></div>
    </div>
  </div>`);
  document.querySelectorAll(`#wr-${i} .radio-item input`).forEach(r=>{r.addEventListener('change',()=>{r.closest('.radio-group')?.querySelectorAll('.radio-item').forEach(x=>x.classList.remove('selected'));if(r.checked)r.closest('.radio-item')?.classList.add('selected');});if(r.checked)r.closest('.radio-item')?.classList.add('selected');});
}
function buildRev(){
  const g=n=>document.querySelector(`[name="${n}"]`)?.value||'—';
  const gr=n=>document.querySelector(`input[name="${n}"]:checked`)?.value||'—';
  const skName=document.querySelector('.sk-item.border-blue-500 .font-bold')?.textContent||'—';
  const jrName=document.getElementById('jurusan_id')?.selectedOptions[0]?.text||'—';
  const jk={laki_laki:'Laki-Laki',perempuan:'Perempuan'}[gr('jenis_kelamin')]||'—';
  const jalur={reguler:'Reguler',prestasi:'Prestasi',afirmasi:'Afirmasi',pindahan:'Pindahan'}[gr('jalur_pendaftaran')]||'—';
  document.getElementById('review-box').innerHTML=`
    <div class="space-y-3 text-sm">
      <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
        <div class="text-xs font-bold text-blue-600 uppercase mb-2">Sekolah</div>
        <div class="font-bold">${skName}</div>
        ${jrName!=='—'&&jrName?`<div class="text-xs text-gray-500 mt-1">Jurusan: ${jrName}</div>`:''}
        <div class="text-xs mt-1"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Jalur ${jalur}</span></div>
      </div>
      <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
        <div class="text-xs font-bold text-gray-500 uppercase mb-2">Data Siswa</div>
        <div class="grid grid-cols-2 gap-x-6 gap-y-1.5 text-xs">
          <span class="text-gray-400">Nama</span><span class="font-semibold">${g('nama_lengkap')}</span>
          <span class="text-gray-400">NISN</span><span class="font-semibold">${g('nisn')}</span>
          <span class="text-gray-400">JK</span><span class="font-semibold">${jk}</span>
          <span class="text-gray-400">Tgl Lahir</span><span class="font-semibold">${g('tempat_lahir')}, ${g('tanggal_lahir')}</span>
          <span class="text-gray-400">No. HP</span><span class="font-semibold">${g('phone')}</span>
          <span class="text-gray-400">Email</span><span class="font-semibold">${g('email')}</span>
          <span class="text-gray-400">Asal Sekolah</span><span class="font-semibold">${g('asal_sekolah')}</span>
        </div>
      </div>
    </div>`;
}
document.addEventListener('DOMContentLoaded',()=>{addWali();upUI();});
</script>
@endpush
