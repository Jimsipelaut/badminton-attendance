@extends('layouts.app')

@section('content')
<div class="pt-4">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Daftar Anggota</h1>
        <p class="text-xs text-slate-500 mt-1">Registrasi akun anggota baru UKM Bulutangkis</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="Contoh: Hendra Setiawan"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="nim" class="block text-xs font-semibold text-slate-700 mb-1">NIM (Nomor Induk Mahasiswa) *</label>
                <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required
                    placeholder="Contoh: 12210888"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="university" class="block text-xs font-semibold text-slate-700 mb-1">Asal Kampus / Fakultas *</label>
                <input type="text" name="university" id="university" value="{{ old('university') }}" required
                    placeholder="Contoh: Universitas Indonesia / FT"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="phone_number" class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                    placeholder="Contoh: 081234567890"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="nama@email.com"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password (min. 6 karakter) *</label>
                <input type="password" name="password" id="password" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Ulangi Password *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    placeholder="••••••••"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3 bg-primary hover:bg-blue-800 text-white font-semibold rounded-xl text-sm transition shadow-sm active:scale-[0.99]">
                    Kirim Pendaftaran
                </button>
            </div>
        </form>
    </div>

    <div class="text-center mt-6 text-xs text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Masuk di sini</a>
    </div>
</div>
@endsection
