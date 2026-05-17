<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('admin.dashboard');
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email','password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'Berhasil logout.');
    }

    public function profile()
    {
        return view('admin.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'      => 'required|string|max:100',
            'whatsapp'  => 'nullable|string|max:20',
            'foto'      => 'nullable|image|max:2048',
            'password'  => 'nullable|min:8|confirmed',
        ]);

        $data = ['name' => $request->name, 'whatsapp' => $request->whatsapp];

        if ($request->hasFile('foto')) {
            if ($user->foto_profil) \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto_profil);
            $data['foto_profil'] = $request->file('foto')->store('profil', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
