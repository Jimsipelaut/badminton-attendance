@extends('layouts.app')

@section('content')
<div class="pt-6">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-100 text-blue-700 rounded-2xl flex items-center justify-center mx-auto text-3xl mb-3 shadow-sm">
            🏸
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Masuk Akun</h1>
        <p class="text-xs text-slate-500 mt-1">Presensi Digital & Rekapitulasi UKM Bulutangkis</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    placeholder="nama@email.com"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-slate-600">
                    <input type="checkbox" name="remember" class="rounded text-primary focus:ring-primary">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit"
                class="w-full py-3 bg-primary hover:bg-blue-800 text-white font-semibold rounded-xl text-sm transition shadow-sm active:scale-[0.99]">
                Masuk
            </button>
        </form>
    </div>

    <div class="text-center mt-6 text-xs text-slate-500">
        Belum punya akun anggota?
        <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Daftar Sekarang</a>
    </div>

    <!-- Info Akun Default Cepat -->
    <div class="mt-8 p-3.5 bg-slate-200/60 rounded-xl text-xs text-slate-600 space-y-1">
        <p class="font-semibold text-slate-800">Akun Pengurus / Demo:</p>
        <p>• Admin: <span class="font-mono text-slate-700">admin@ukmbadminton.org</span> / password</p>
        <p>• Sekre: <span class="font-mono text-slate-700">sekre@ukmbadminton.org</span> / password</p>
        <p>• Anggota: <span class="font-mono text-slate-700">member@ukmbadminton.org</span> / password</p>
    </div>
</div>
@endsection
