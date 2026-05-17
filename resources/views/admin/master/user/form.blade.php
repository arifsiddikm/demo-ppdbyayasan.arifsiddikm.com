{{-- resources/views/admin/master/user/form.blade.php --}}
@extends('admin.layouts.app')
@section('title', $user ? 'Edit Admin' : 'Tambah Admin')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-user-plus text-blue-600"></i> {{ $user ? 'Edit' : 'Tambah' }} Admin</h1>
  <a href="{{ route('admin.user.index') }}" class="btn btn-sm btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>
<div class="max-w-lg">
  <form action="{{ $user ? route('admin.user.update',$user) : route('admin.user.store') }}" method="POST">
    @csrf @if($user) @method('PUT') @endif
    <div class="card p-6 space-y-4">
      <div><label class="form-label">Nama <span class="req">*</span></label><input type="text" name="name" class="form-input" value="{{ old('name',$user?->name) }}" required></div>
      <div><label class="form-label">Email <span class="req">*</span></label><input type="email" name="email" class="form-input" value="{{ old('email',$user?->email) }}" required></div>
      <div><label class="form-label">Role <span class="req">*</span></label>
        <select name="role" class="form-input" required>
          <option value="admin" {{ old('role',$user?->role)==='admin'?'selected':'' }}>Admin</option>
          <option value="superadmin" {{ old('role',$user?->role)==='superadmin'?'selected':'' }}>Super Admin</option>
        </select>
      </div>
      <div><label class="form-label">Password {{ $user ? '(kosongkan jika tidak diubah)' : '' }} <span class="req">*</span></label><input type="password" name="password" class="form-input" {{ $user?'':' required' }} minlength="8"></div>
      <div><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-input"></div>
      <div class="pt-3 border-t flex gap-3">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Batal</a>
      </div>
    </div>
  </form>
</div>
@endsection
