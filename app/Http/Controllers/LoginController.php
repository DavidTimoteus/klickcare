<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau NIM / NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('login')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('login');
        }

        // Tentukan apakah login menggunakan email atau nomor identitas (NIM/NIP)
        $loginInput = $credentials['login'];
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'noId';

        $attemptCredentials = [
            $fieldType => $loginInput,
            'password' => $credentials['password'],
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($attemptCredentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin'));
            }

            return redirect()->intended(route('user'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'login' => 'Email/NIM atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('login');
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'noId' => ['required', 'string', 'max:30', 'unique:tbl_users,noId'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:tbl_users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(6)],
            'role' => ['required', 'in:2,3'], // 2 = Dosen/Pegawai, 3 = Mahasiswa
            'gender' => ['nullable', 'in:Laki-Laki,Perempuan'],
            'phoneNumber' => ['nullable', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'noId.required' => 'NIM / NIP wajib diisi.',
            'noId.unique' => 'NIM / NIP sudah terdaftar dalam sistem.',
            'email.required' => 'Email kampus wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'role.in' => 'Kategori pengguna tidak valid.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'noId' => $validated['noId'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => (int) $validated['role'],
            'gender' => $validated['gender'] ?? null,
            'phoneNumber' => $validated['phoneNumber'] ?? null,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user')->with('success', 'Akun berhasil didaftarkan. Selamat datang di KlickCare Polinema!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
