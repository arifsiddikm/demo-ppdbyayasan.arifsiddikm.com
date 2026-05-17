{{-- resources/views/admin/master/user/index.blade.php --}}
@extends('admin.layouts.app')
@section('title','Kelola Admin')
@section('content')
<div class="page-header mb-5 -mx-5 -mt-5 px-6 pt-5">
  <h1><i class="fa fa-users-cog text-blue-600"></i> Kelola Admin</h1>
  <a href="{{ route('admin.user.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah Admin</a>
</div>
<div class="card overflow-hidden">
  <table class="table">
    <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr></thead>
    <tbody>
      @foreach($users as $u)
      <tr>
        <td>
          <div class="flex items-center gap-3">
            <img src="{{ $u->foto_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
            <div class="font-semibold text-sm">{{ $u->name }}</div>
          </div>
        </td>
        <td class="text-xs text-gray-600">{{ $u->email }}</td>
        <td><span class="badge {{ $u->role==='superadmin'?'bg-purple-100 text-purple-700':'bg-blue-100 text-blue-700' }}">{{ ucfirst($u->role) }}</span></td>
        <td class="text-xs text-gray-500">{{ $u->created_at->format('d/m/Y') }}</td>
        <td>
          <div class="flex gap-1">
            <a href="{{ route('admin.user.edit',$u) }}" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
            @if($u->id !== auth()->id())
            <form id="del-u-{{ $u->id }}" action="{{ route('admin.user.destroy',$u) }}" method="POST">@csrf @method('DELETE')</form>
            <button onclick="confirmDelete('del-u-{{ $u->id }}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
