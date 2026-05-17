<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Dashboard') — Admin PPDB</title>
  <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    *{font-family:'Inter',sans-serif}
    :root{--sw:256px}
    .sidebar{width:var(--sw);min-height:100vh;background:linear-gradient(180deg,#0f172a,#1e293b);position:fixed;left:0;top:0;bottom:0;z-index:40;display:flex;flex-direction:column;transition:transform .3s}
    .main-wrap{margin-left:var(--sw);min-height:100vh;background:#f8fafc}
    @media(max-width:1023px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}.main-wrap{margin-left:0}}
    .nav-item{display:flex;align-items:center;gap:10px;padding:9px 16px;border-radius:8px;font-size:13px;font-weight:500;color:#94a3b8;text-decoration:none;transition:all .15s;margin:1px 8px}
    .nav-item:hover{background:rgba(255,255,255,.07);color:#f1f5f9}
    .nav-item.active{background:rgba(59,130,246,.2);color:#93c5fd;border-left:3px solid #3b82f6}
    .nav-item i{width:16px;text-align:center;flex-shrink:0}
    .nav-sec{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#475569;padding:12px 24px 4px}
    .form-input{display:block;width:100%;padding:9px 13px;border:1.5px solid #d1d5db;border-radius:8px;font-size:13px;color:#111827;background:#fff;transition:border-color .2s,box-shadow .2s;outline:none}
    .form-input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.12)}
    select.form-input{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;padding-right:32px}
    textarea.form-input{resize:vertical;min-height:80px}
    .form-label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px}
    .form-label .req{color:#dc2626;margin-left:2px}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;border:none;text-decoration:none}
    .btn-primary{background:#1e40af;color:#fff}.btn-primary:hover{background:#1d4ed8}
    .btn-secondary{background:#f1f5f9;color:#374151;border:1px solid #e2e8f0}.btn-secondary:hover{background:#e2e8f0}
    .btn-success{background:#059669;color:#fff}.btn-success:hover{background:#047857}
    .btn-danger{background:#dc2626;color:#fff}.btn-danger:hover{background:#b91c1c}
    .btn-warning{background:#d97706;color:#fff}.btn-warning:hover{background:#b45309}
    .btn-outline{background:transparent;color:#1e40af;border:1.5px solid #1e40af}.btn-outline:hover{background:#eff6ff}
    .btn-sm{padding:5px 12px;font-size:12px;border-radius:6px}
    .btn-lg{padding:11px 22px;font-size:14px;border-radius:10px}
    .card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.06)}
    .card-header{padding:16px 20px;border-bottom:1px solid #f1f5f9;}
    .card-body{padding:20px;}
    .badge{display:inline-flex;align-items:center;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700}
    .alert{padding:11px 15px;border-radius:8px;font-size:13px;display:flex;align-items:flex-start;gap:9px}
    .alert-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
    .alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .alert-info{background:#dbeafe;color:#1e40af;border:1px solid #bfdbfe}
    .alert-warning{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
    .table{width:100%;border-collapse:collapse;font-size:13px}
    .table th{background:#f8fafc;padding:10px 14px;text-align:left;font-weight:600;color:#6b7280;border-bottom:1px solid #e5e7eb;font-size:11px;text-transform:uppercase;letter-spacing:.5px}
    .table td{padding:11px 14px;border-bottom:1px solid #f1f5f9;color:#374151;vertical-align:middle}
    .table tr:hover td{background:#f9fafb}
    .radio-group,.checkbox-group{display:flex;flex-wrap:wrap;gap:8px}
    .radio-item,.checkbox-item{display:flex;align-items:center;gap:6px;padding:7px 13px;border:1.5px solid #d1d5db;border-radius:7px;cursor:pointer;font-size:12px;font-weight:500;color:#374151;transition:all .15s;background:#fff}
    .radio-item:hover,.checkbox-item:hover{border-color:#3b82f6;background:#eff6ff}
    .radio-item input,.checkbox-item input{accent-color:#1e40af;width:14px;height:14px}
    .radio-item.selected,.checkbox-item.selected{border-color:#1e40af;background:#eff6ff;color:#1e40af}
    .file-input-wrapper{position:relative}
    .file-input-wrapper input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
    .file-input-display{border:2px dashed #d1d5db;border-radius:8px;padding:12px;text-align:center;cursor:pointer;transition:all .2s}
    .file-input-display:hover{border-color:#3b82f6;background:#eff6ff}
    .file-input-display.has-file{border-color:#059669;background:#d1fae5}
    .page-header{padding:20px 24px;border-bottom:1px solid #e5e7eb;background:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
    .page-header h1{font-size:17px;font-weight:700;color:#111827;display:flex;align-items:center;gap:8px}
    .stat-card{background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:20px;transition:box-shadow .2s}
    .stat-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08)}
    .spin{animation:spin .8s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}
    /* Step pane for admin create form */
    .step-bar{display:flex;align-items:center}
    .step-item{flex:1;display:flex;flex-direction:column;align-items:center;position:relative}
    .step-item:not(:last-child)::after{content:'';position:absolute;top:18px;left:calc(50% + 18px);right:calc(-50% + 18px);height:2px;background:#e2e8f0;z-index:0}
    .step-item.done::after{background:#1e40af}
    .step-circle{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;z-index:1;position:relative;border:2px solid #e2e8f0;background:#fff;color:#9ca3af;transition:all .3s}
    .step-item.active .step-circle{border-color:#1e40af;background:#1e40af;color:#fff}
    .step-item.done .step-circle{border-color:#1e40af;background:#1e40af;color:#fff}
    .step-label{font-size:10px;font-weight:600;color:#9ca3af;margin-top:5px;text-align:center}
    .step-item.active .step-label,.step-item.done .step-label{color:#1e40af}
    .step-pane{display:none}.step-pane.active{display:block}
    .jalur-card{border:2px solid #e5e7eb;border-radius:12px;padding:14px;cursor:pointer;transition:all .2s;background:#fff}
    .wali-row{background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;padding:14px;margin-bottom:10px}
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
  @stack('head')
</head>
<body class="bg-gray-50">

<aside class="sidebar" id="sidebar">
  <div class="p-5 border-b border-slate-700">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0">YI</div>
      <div><div class="text-white font-bold text-sm leading-tight">PPDB Admin</div><div class="text-slate-400 text-xs">Yayasan Indonesia</div></div>
    </div>
  </div>

  <nav class="flex-1 py-4 overflow-y-auto">
    <div class="nav-sec">Utama</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa fa-gauge"></i> Dashboard</a>

    <div class="nav-sec mt-2">Pendaftaran</div>
    <a href="{{ route('admin.pendaftaran.index') }}" class="nav-item {{ request()->routeIs('admin.pendaftaran.index') || request()->routeIs('admin.pendaftaran.show') ? 'active' : '' }}">
      <i class="fa fa-clipboard-list"></i> Data Pendaftaran
      @php $wp=\App\Models\Pendaftaran::where('status','diproses')->count(); @endphp
      @if($wp>0)<span class="ml-auto bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $wp }}</span>@endif
    </a>
    <a href="{{ route('admin.pendaftaran.create') }}" class="nav-item {{ request()->routeIs('admin.pendaftaran.create') ? 'active' : '' }}"><i class="fa fa-plus-circle"></i> Buat Pendaftaran</a>
    @php $wbayar=\App\Models\Pembayaran::where('status_pembayaran','menunggu_verifikasi')->count(); @endphp
    @if($wbayar>0)
    <a href="{{ route('admin.pendaftaran.index',['status'=>'menunggu_pembayaran']) }}" class="nav-item">
      <i class="fa fa-credit-card text-yellow-400"></i> Verifikasi Bayar
      <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $wbayar }}</span>
    </a>
    @endif

    <div class="nav-sec mt-2">Master Data</div>
    <a href="{{ route('admin.sekolah.index') }}" class="nav-item {{ request()->routeIs('admin.sekolah.*') ? 'active' : '' }}"><i class="fa fa-school"></i> Sekolah & Jurusan</a>
    <a href="{{ route('admin.tahun.index') }}" class="nav-item {{ request()->routeIs('admin.tahun.*') ? 'active' : '' }}"><i class="fa fa-calendar-alt"></i> Tahun Akademik</a>
    <a href="{{ route('admin.metode.index') }}" class="nav-item {{ request()->routeIs('admin.metode.*') ? 'active' : '' }}"><i class="fa fa-wallet"></i> Metode Pembayaran</a>
    <a href="{{ route('admin.testimoni.index') }}" class="nav-item {{ request()->routeIs('admin.testimoni.*') ? 'active' : '' }}"><i class="fa fa-star"></i> Testimoni</a>

    @if(auth()->user()?->isSuperAdmin())
    <div class="nav-sec mt-2">Pengaturan</div>
    <a href="{{ route('admin.user.index') }}" class="nav-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}"><i class="fa fa-users-cog"></i> Kelola Admin</a>
    <a href="{{ route('admin.pengaturan') }}" class="nav-item {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}"><i class="fa fa-sliders"></i> Pengaturan Web</a>
    @endif

    <div class="nav-sec mt-2">Akun</div>
    <a href="{{ route('admin.profile') }}" class="nav-item {{ request()->routeIs('admin.profile') ? 'active' : '' }}"><i class="fa fa-user-circle"></i> Profil Saya</a>
    <a href="{{ route('home') }}" target="_blank" class="nav-item"><i class="fa fa-external-link"></i> Lihat Website</a>
  </nav>

  <div class="p-4 border-t border-slate-700">
    <div class="flex items-center gap-3 mb-3">
      <img src="{{ auth()->user()?->foto_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
      <div class="flex-1 min-w-0"><div class="text-white text-sm font-semibold truncate">{{ auth()->user()?->name }}</div><div class="text-slate-400 text-xs capitalize">{{ auth()->user()?->role }}</div></div>
    </div>
    <form action="{{ route('admin.logout') }}" method="POST" id="logout-form">@csrf</form>
    <button type="button" onclick="confirmLogout()" class="btn btn-sm w-full justify-center" style="background:rgba(220,38,38,.15);color:#fca5a5;border:1px solid rgba(220,38,38,.3);">
      <i class="fa fa-right-from-bracket"></i> Logout
    </button>
  </div>
</aside>

<div class="lg:hidden fixed inset-0 bg-black/50 z-30 hidden" id="sb-overlay" onclick="toggleSB()"></div>

<div class="main-wrap">
  <div class="sticky top-0 z-20 bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3">
    <button onclick="toggleSB()" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100"><i class="fa fa-bars"></i></button>
    <div class="flex-1 text-sm text-gray-500 hidden md:block">
      <i class="fa fa-home text-gray-400 mr-1"></i>@yield('breadcrumb','Dashboard')
    </div>
    <div class="flex items-center gap-3 ml-auto">
      @if(isset($wbayar) && $wbayar>0)
      <a href="{{ route('admin.pendaftaran.index') }}" class="relative">
        <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center"><i class="fa fa-bell text-yellow-600 text-sm"></i></span>
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ min($wbayar,9) }}</span>
      </a>
      @endif
      <img src="{{ auth()->user()?->foto_url }}" class="w-8 h-8 rounded-full object-cover" alt="">
      <span class="text-sm font-medium text-gray-700 hidden md:inline">{{ auth()->user()?->name }}</span>
    </div>
  </div>

  <div class="p-5">
    @if(session('success'))<div class="alert alert-success mb-4"><i class="fa fa-check-circle flex-shrink-0"></i>{{ session('success') }}</div>@endif
    @if(session('error') || $errors->any())
    <div class="alert alert-danger mb-4"><i class="fa fa-circle-exclamation flex-shrink-0"></i>
      {{ session('error') }}
      @if($errors->any())<ul class="mt-1 list-disc list-inside text-xs">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
    </div>
    @endif
    @yield('content')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleSB(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('sb-overlay').classList.toggle('hidden');}
function confirmLogout(){Swal.fire({title:'Logout?',text:'Anda akan keluar dari panel admin.',icon:'question',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonText:'Batal',confirmButtonText:'Ya, Logout'}).then(r=>{if(r.isConfirmed)document.getElementById('logout-form').submit();});}
function confirmDelete(f){Swal.fire({title:'Hapus Data?',text:'Data yang dihapus tidak dapat dikembalikan.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonText:'Batal',confirmButtonText:'Ya, Hapus'}).then(r=>{if(r.isConfirmed)document.getElementById(f).submit();});}
// Radio/checkbox styling
document.querySelectorAll('.radio-item input[type="radio"]').forEach(r=>{r.addEventListener('change',()=>{r.closest('.radio-group')?.querySelectorAll('.radio-item').forEach(i=>i.classList.remove('selected'));if(r.checked)r.closest('.radio-item')?.classList.add('selected');});if(r.checked)r.closest('.radio-item')?.classList.add('selected');});
// File inputs
document.querySelectorAll('.file-input-wrapper input[type="file"]').forEach(f=>{f.addEventListener('change',()=>{const d=f.closest('.file-input-wrapper')?.querySelector('.file-input-display');if(!d)return;if(f.files.length>0){d.classList.add('has-file');d.querySelector('.file-label')&&(d.querySelector('.file-label').textContent=f.files[0].name);}});});
</script>
<script>
document.addEventListener("DOMContentLoaded",function(){
  flatpickr("input[type=date]",{dateFormat:"Y-m-d",locale:"id",allowInput:true});
});
</script>
@stack('scripts')
</body>
</html>
