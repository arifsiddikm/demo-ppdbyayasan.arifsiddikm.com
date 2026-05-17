<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Login Admin — PPDB Yayasan Indonesia</title>
  <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family:'Inter',sans-serif; }
    .form-input { display:block;width:100%;padding:10px 14px;border:1.5px solid #d1d5db;border-radius:10px;font-size:14px;color:#111827;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s; }
    .form-input:focus { border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.15); }
    .form-input.error { border-color:#dc2626; }
    .btn-primary { background:#1e40af;color:#fff;padding:11px 20px;border-radius:10px;font-weight:700;font-size:14px;width:100%;border:none;cursor:pointer;transition:background .2s;display:flex;align-items:center;justify-content:center;gap:8px; }
    .btn-primary:hover { background:#1d4ed8; }
  </style>
</head>
<body style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#1e40af 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;">

<div style="width:100%;max-width:420px;">
  <!-- Logo -->
  <div class="text-center mb-8">
    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
      <span class="text-blue-800 font-extrabold text-xl">YI</span>
    </div>
    <h1 class="text-2xl font-extrabold text-white">Panel Admin</h1>
    <p class="text-blue-300 text-sm mt-1">PPDB Yayasan Indonesia</p>
  </div>

  <div style="background:rgba(255,255,255,.97);border-radius:20px;padding:32px;box-shadow:0 20px 60px rgba(0,0,0,.3);">

    @if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;margin-bottom:20px;font-size:13px;color:#991b1b;display:flex;align-items:center;gap:8px;">
      <i class="fa fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('admin.login.post') }}" method="POST" id="login-form">
      @csrf
      <div class="mb-4">
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;">Email Admin</label>
        <input type="email" name="email" id="email-field" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
          placeholder="admin@ppdb.id" value="{{ old('email') }}" required autocomplete="email">
      </div>
      <div class="mb-6">
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;">Password</label>
        <div style="position:relative;">
          <input type="password" name="password" id="pass-field" class="form-input" placeholder="••••••••" required autocomplete="current-password" style="padding-right:44px;">
          <button type="button" onclick="togglePass()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;">
            <i class="fa fa-eye" id="eye-icon"></i>
          </button>
        </div>
        <div class="flex items-center mt-2">
          <input type="checkbox" name="remember" id="remember" style="accent-color:#1e40af;width:14px;height:14px;margin-right:6px;">
          <label for="remember" style="font-size:12px;color:#6b7280;cursor:pointer;">Ingat saya</label>
        </div>
      </div>

      <button type="submit" class="btn-primary">
        <i class="fa fa-right-to-bracket"></i> Masuk ke Panel Admin
      </button>
    </form>

    <!-- Autofill testing button -->
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid #f1f5f9;text-align:center;">
      <div style="font-size:11px;color:#9ca3af;margin-bottom:8px;">— Testing —</div>
      <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
        <button type="button" onclick="autofill('superadmin@ppdb.id','Admin123!!')"
          style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;">
          <i class="fa fa-wand-magic-sparkles text-xs"></i> Autofill Superadmin
        </button>
        <button type="button" onclick="autofill('admin@ppdb.id','Admin123!!')"
          style="background:#f0fdf4;color:#065f46;border:1px solid #a7f3d0;padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;">
          <i class="fa fa-wand-magic-sparkles text-xs"></i> Autofill Admin
        </button>
      </div>
    </div>
  </div>

  <div class="text-center mt-6">
    <a href="{{ route('home') }}" style="color:rgba(255,255,255,.6);font-size:13px;text-decoration:none;">
      <i class="fa fa-arrow-left text-xs"></i> Kembali ke Website
    </a>
  </div>
</div>

<script>
function autofill(email, pass) {
  document.getElementById('email-field').value = email;
  document.getElementById('pass-field').value = pass;
  document.getElementById('email-field').focus();
}
function togglePass() {
  const f = document.getElementById('pass-field');
  const i = document.getElementById('eye-icon');
  f.type = f.type==='password' ? 'text' : 'password';
  i.className = f.type==='password' ? 'fa fa-eye' : 'fa fa-eye-slash';
}
</script>
</body>
</html>
