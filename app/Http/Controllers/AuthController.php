<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'member' && $user->status !== 'approved') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $msg = $user->status === 'pending'
                    ? 'Akun Anda masih menunggu persetujuan sekretaris UKM.'
                    : 'Akun Anda tidak aktif atau ditolak oleh pengurus.';

                return back()->withErrors(['email' => $msg])->onlyInput('email');
            }

            return $this->redirectUser($user);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nim' => ['required', 'string', 'max:30'],
            'university' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:25'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nim' => $validated['nim'],
            'university' => $validated['university'],
            'phone_number' => $validated['phone_number'] ?? null,
            'qr_token' => 'urn:ukm-badminton:token:' . Str::uuid(),
            'role' => 'member',
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu verifikasi oleh Sekretaris UKM.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    private function redirectUser(User $user)
    {
        if ($user->isAdmin() || $user->isSecretary()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('member.dashboard');
    }
}
