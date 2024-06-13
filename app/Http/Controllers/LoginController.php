<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Tidak ada middleware 'auth' di sini
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required',
            'noUser' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'name.required' => 'Form tidak boleh kosong',
            'noUser.required' => 'Form tidak boleh kosong',
            'email.required' => 'Form tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Form tidak boleh kosong',
        ]);
        // Cek kredensial dan autentikasi
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // Autentikasi berhasil, periksa role pengguna
            $user = Auth::user();
            if ($user->role == 1) {
                return redirect()->route('admin');
            } elseif ($user->role == 2 || $user->role == 3) {
                return redirect()->route('user');
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors(['role' => 'Role tidak dikenali.']);
            }
        }

        // Autentikasi gagal, kembali ke form login dengan pesan error
        return redirect()->route('login')->withErrors(['email' => 'Email atau password salah.']);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
