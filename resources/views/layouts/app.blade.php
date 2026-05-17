<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'PPDB Yayasan Indonesia') — PPDB Yayasan Indonesia</title>
  <meta name="description" content="@yield('meta_desc', 'Penerimaan Peserta Didik Baru Yayasan Indonesia. Pendaftaran online mudah dan cepat.')">
  <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    *{font-family:'Inter',sans-serif}
    html{scroll-behavior:smooth}
    .navbar-glass{background:rgba(255,255,255,.96);backdrop-filter:blur(12px);border-bottom:1px solid rgba(30,64,175,.1)}
    /* Form */
    .form-input{display:block;width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:10px;font-size:14px;color:#111827;background:#fff;transition:border-color .2s,box-shadow .2s;outline:none}
    .form-input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.15)}
    .form-input.error{border-color:#dc2626}
    .form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px}
    .form-label .req{color:#dc2626;margin-left:2px}
    select.form-input{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;background-size:16px;padding-right:36px}
    textarea.form-input{resize:vertical;min-height:80px}
    /* Radio/Checkbox */
    .radio-group,.checkbox-group{display:flex;flex-wrap:wrap;gap:10px}
    .radio-item,.checkbox-item{display:flex;align-items:center;gap:8px;padding:9px 16px;border:1.5px solid #d1d5db;border-radius:8px;cursor:pointer;font-size:13px;font-weight:500;color:#374151;transition:all .2s;background:#fff}
    .radio-item:hover,.checkbox-item:hover{border-color:#3b82f6;background:#eff6ff}
    .radio-item input,.checkbox-item input{accent-color:#1e40af;width:16px;height:16px}
    .radio-item.selected,.checkbox-item.selected{border-color:#1e40af;background:#eff6ff;color:#1e40af}
    /* Buttons */
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s;border:none;text-decoration:none}
    .btn-primary{background:#1e40af;color:#fff}.btn-primary:hover{background:#1d4ed8;transform:translateY(-1px);box-shadow:0 4px 14px rgba(30,64,175,.3)}
    .btn-secondary{background:#f1f5f9;color:#374151;border:1.5px solid #d1d5db}.btn-secondary:hover{background:#e2e8f0}
    .btn-success{background:#059669;color:#fff}.btn-success:hover{background:#047857}
    .btn-danger{background:#dc2626;color:#fff}.btn-danger:hover{background:#b91c1c}
    .btn-outline{background:transparent;color:#1e40af;border:1.5px solid #1e40af}.btn-outline:hover{background:#eff6ff}
    .btn-lg{padding:14px 28px;font-size:15px;border-radius:12px}
    .btn-sm{padding:6px 14px;font-size:12px;border-radius:8px}
    .btn:disabled{opacity:.6;cursor:not-allowed;transform:none!important}
    /* Step */
    .step-bar{display:flex;align-items:center}
    .step-item{flex:1;display:flex;flex-direction:column;align-items:center;position:relative}
    .step-item:not(:last-child)::after{content:'';position:absolute;top:18px;left:calc(50% + 18px);right:calc(-50% + 18px);height:2px;background:#e2e8f0;z-index:0}
    .step-item.done::after{background:#1e40af}
    .step-circle{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;z-index:1;position:relative;border:2px solid #e2e8f0;background:#fff;color:#9ca3af;transition:all .3s}
    .step-item.active .step-circle{border-color:#1e40af;background:#1e40af;color:#fff}
    .step-item.done .step-circle{border-color:#1e40af;background:#1e40af;color:#fff}
    .step-label{font-size:11px;font-weight:600;color:#9ca3af;margin-top:6px;text-align:center}
    .step-item.active .step-label,.step-item.done .step-label{color:#1e40af}
    .step-pane{display:none}.step-pane.active{display:block}
    /* Cards */
    .card{background:#fff;border-radius:16px;border:1px solid #e5e7eb;box-shadow:0 1px 4px rgba(0,0,0,.06)}
    .card-header{padding:20px 24px;border-bottom:1px solid #f1f5f9}
    .card-body{padding:24px}
    /* Badge/Alert */
    .badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
    .alert{padding:12px 16px;border-radius:10px;font-size:13px;display:flex;align-items:flex-start;gap:10px}
    .alert-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
    .alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .alert-info{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe}
    .alert-warning{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
    /* File input */
    .file-input-wrapper{position:relative}
    .file-input-wrapper input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
    .file-input-display{border:2px dashed #d1d5db;border-radius:10px;padding:16px;text-align:center;cursor:pointer;transition:all .2s}
    .file-input-display:hover{border-color:#3b82f6;background:#eff6ff}
    .file-input-display.has-file{border-color:#059669;background:#d1fae5}
    /* Email sending badge */
    .email-sending{display:none;align-items:center;gap:8px;padding:8px 16px;background:#dbeafe;border-radius:8px;font-size:13px;color:#1e40af;font-weight:600}
    .email-sending.show{display:inline-flex}
    .spin{animation:spin .8s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}
    /* Wali row */
    .wali-row{background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:12px}
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
  @stack('head')
</head>
<body class="bg-gray-50 text-gray-800">

<!-- NAVBAR -->
<nav class="navbar-glass fixed top-0 left-0 right-0 z-50 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    <a href="{{ route('home') }}" class="flex items-center gap-3">
      <div class="w-9 h-9 bg-blue-800 rounded-xl flex items-center justify-center text-white font-bold text-sm">YI</div>
      <div>
        <div class="font-bold text-blue-900 text-sm leading-tight">PPDB Yayasan Indonesia</div>
        <div class="text-xs text-gray-500 leading-tight">Penerimaan Peserta Didik Baru</div>
      </div>
    </a>
    <div class="hidden md:flex items-center gap-6">
      <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 hover:text-blue-700 transition">Beranda</a>
      <a href="{{ route('home') }}#sekolah" class="text-sm font-medium text-gray-600 hover:text-blue-700 transition">Sekolah</a>
      <a href="{{ route('home') }}#cara-daftar" class="text-sm font-medium text-gray-600 hover:text-blue-700 transition">Cara Daftar</a>
      <a href="{{ route('cek.status') }}" class="text-sm font-medium text-gray-600 hover:text-blue-700 transition">Cek Status</a>
      <a href="{{ route('admin.login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 border border-gray-300 hover:border-gray-400 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
        <i class="fa fa-lock text-xs"></i> Admin
      </a>
      <a href="{{ route('daftar') }}" class="btn btn-primary btn-sm"><i class="fa fa-pen-to-square"></i> Daftar Sekarang</a>
    </div>
    <button id="mob-toggle" class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100"><i class="fa fa-bars text-xl"></i></button>
  </div>
  <div id="mob-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-2">
    <a href="{{ route('home') }}" class="block py-2 text-sm font-medium text-gray-700">Beranda</a>
    <a href="{{ route('home') }}#sekolah" class="block py-2 text-sm font-medium text-gray-700">Sekolah</a>
    <a href="{{ route('home') }}#cara-daftar" class="block py-2 text-sm font-medium text-gray-700">Cara Daftar</a>
    <a href="{{ route('cek.status') }}" class="block py-2 text-sm font-medium text-gray-700">Cek Status</a>
    <a href="{{ route('admin.login') }}" class="block py-2 text-sm font-medium text-gray-500 flex items-center gap-2"><i class="fa fa-lock text-xs"></i> Login Admin</a>
    <a href="{{ route('daftar') }}" class="btn btn-primary w-full justify-center">Daftar Sekarang</a>
  </div>
</nav>

<div class="pt-16">@yield('content')</div>

<!-- FOOTER -->
<footer class="bg-gray-900 text-gray-400">
  <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
    <div class="md:col-span-2">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-blue-700 rounded-xl flex items-center justify-center text-white font-bold">YI</div>
        <div><div class="text-white font-bold">PPDB Yayasan Indonesia</div><div class="text-xs text-gray-500">Penerimaan Peserta Didik Baru</div></div>
      </div>
      <p class="text-sm leading-relaxed mb-4">Yayasan Indonesia berkomitmen mencetak generasi penerus bangsa yang berilmu, berakhlak, dan berdaya saing global.</p>
      <div class="flex gap-3">
        <a href="https://www.instagram.com/arifsiddikm/" target="_blank" rel="noopener" class="w-8 h-8 bg-gray-800 hover:bg-pink-600 rounded-lg flex items-center justify-center transition"><i class="fab fa-instagram text-sm text-gray-400"></i></a>
        <a href="#" class="w-8 h-8 bg-gray-800 hover:bg-blue-700 rounded-lg flex items-center justify-center transition"><i class="fab fa-facebook text-sm text-gray-400"></i></a>
        <a href="https://wa.me/{{ env('ADMIN_WHATSAPP','6289514392694') }}" target="_blank" class="w-8 h-8 bg-gray-800 hover:bg-green-600 rounded-lg flex items-center justify-center transition"><i class="fab fa-whatsapp text-sm text-gray-400"></i></a>
      </div>
    </div>
    <div>
      <h4 class="text-white font-semibold mb-3 text-sm">Link Cepat</h4>
      <ul class="space-y-2 text-sm">
        <li><a href="{{ route('daftar') }}" class="hover:text-white transition">Daftar Sekarang</a></li>
        <li><a href="{{ route('cek.status') }}" class="hover:text-white transition">Cek Status</a></li>
        <li><a href="{{ route('home') }}#sekolah" class="hover:text-white transition">Daftar Sekolah</a></li>
        <li><a href="{{ route('home') }}#cara-daftar" class="hover:text-white transition">Cara Mendaftar</a></li>
        <li><a href="{{ route('admin.login') }}" class="hover:text-white transition">Login Admin</a></li>
      </ul>
    </div>
    <div>
      <h4 class="text-white font-semibold mb-3 text-sm">Kontak</h4>
      <ul class="space-y-2 text-sm">
        <li class="flex gap-2"><i class="fa fa-map-marker-alt mt-1 text-blue-500 flex-shrink-0"></i><span>Jl. Pendidikan No. 1, Jakarta Selatan</span></li>
        <li class="flex gap-2"><i class="fa fa-phone mt-1 text-blue-500 flex-shrink-0"></i><span>021-87654321</span></li>
        <li class="flex gap-2"><i class="fa fa-envelope mt-1 text-blue-500 flex-shrink-0"></i><span>info@yayasanindonesia.sch.id</span></li>
        <li class="flex gap-2"><i class="fa fa-clock mt-1 text-blue-500 flex-shrink-0"></i><span>Senin–Jumat: 08.00–15.00</span></li>
      </ul>
    </div>
  </div>
  <div class="border-t border-gray-800 py-4 text-center text-xs text-gray-600">© {{ date('Y') }} PPDB Yayasan Indonesia. Hak Cipta Dilindungi.</div>
</footer>

<!-- WA FLOATING BUTTON -->
<a href="https://wa.me/{{ env('ADMIN_WHATSAPP','6289514392694') }}?text={{ urlencode('Halo, saya ingin bertanya tentang PPDB Yayasan Indonesia.') }}"
   target="_blank" rel="noopener" title="Chat WhatsApp Admin"
   style="position:fixed;bottom:28px;right:28px;z-index:9999;width:56px;height:56px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,.5);text-decoration:none;transition:transform .2s,box-shadow .2s;"
   onmouseover="this.style.transform='scale(1.12)';this.style.boxShadow='0 8px 32px rgba(37,211,102,.7)'"
   onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 20px rgba(37,211,102,.5)'">
  <i class="fab fa-whatsapp" style="color:#fff;font-size:28px;"></i>
  <span style="position:absolute;inset:0;border-radius:50%;border:2px solid #25d366;animation:waPing 2s ease-out infinite;pointer-events:none;"></span>
</a>
<style>@keyframes waPing{0%{transform:scale(1);opacity:.7}100%{transform:scale(1.9);opacity:0}}</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('mob-toggle').addEventListener('click',()=>document.getElementById('mob-menu').classList.toggle('hidden'));
@if(session('success'))
Swal.fire({icon:'success',title:'Berhasil!',text:@json(session('success')),timer:4000,showConfirmButton:false,toast:true,position:'top-end'});
@endif
@if(session('error'))
Swal.fire({icon:'error',title:'Error!',text:@json(session('error')),timer:5000,showConfirmButton:false,toast:true,position:'top-end'});
@endif
// Radio/checkbox styling
document.querySelectorAll('.radio-item input[type="radio"]').forEach(r=>{
  r.addEventListener('change',()=>{r.closest('.radio-group')?.querySelectorAll('.radio-item').forEach(i=>i.classList.remove('selected'));if(r.checked)r.closest('.radio-item')?.classList.add('selected');});
  if(r.checked)r.closest('.radio-item')?.classList.add('selected');
});
// File inputs
document.querySelectorAll('.file-input-wrapper input[type="file"]').forEach(f=>{
  f.addEventListener('change',()=>{const d=f.closest('.file-input-wrapper')?.querySelector('.file-input-display');if(!d)return;if(f.files.length>0){d.classList.add('has-file');const l=d.querySelector('.file-label');if(l)l.textContent=f.files[0].name;}});
});
</script>
<script>
document.addEventListener('DOMContentLoaded',function(){
  flatpickr('input[type="date"]',{dateFormat:'Y-m-d',locale:'id',allowInput:true});
});
</script>
@stack('scripts')
</body>
</html>
