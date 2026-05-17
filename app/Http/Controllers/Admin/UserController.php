<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.master.user.index', ['users' => User::latest()->get()]);
    }

    public function create()
    {
        return view('admin.master.user.form', ['user' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,superadmin',
        ]);
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);
        return redirect()->route('admin.user.index')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.master.user.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|in:admin,superadmin',
            'password' => 'nullable|min:8|confirmed',
        ]);
        $data = ['name' => $request->name, 'email' => $request->email, 'role' => $request->role];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('admin.user.index')->with('success', 'Admin diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa hapus akun sendiri.']);
        }
        $user->delete();
        return back()->with('success', 'Admin dihapus.');
    }
}
