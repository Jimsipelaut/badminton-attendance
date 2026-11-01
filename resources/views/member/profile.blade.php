@extends('layouts.app')

@section('content')
<div class="space-y-4 pt-2">
    <div class="text-center mb-4">
        <h1 class="text-xl font-bold text-slate-900">Profil Saya</h1>
        <p class="text-xs text-slate-500">Kelola informasi data diri anggota</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Avatar Preview & Input -->
            <div class="flex flex-col items-center pb-4 border-b border-slate-100">
                <div class="w-20 h-20 rounded-full bg-slate-100 border-2 border-primary/20 flex items-center justify-center text-2xl font-bold overflow-hidden shadow-inner mb-3">
                    @if($user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    @endif
                </div>
                <label class="cursor-pointer px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-300">
                    <span>Ganti Foto Wajah</span>
                    <input type="file" name="avatar" accept="image/*" class="hidden">
                </label>
                <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG maks. 2 MB</p>
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="nim" class="block text-xs font-semibold text-slate-700 mb-1">NIM</label>
                <input type="text" name="nim" id="nim" value="{{ old('nim', $user->nim) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="university" class="block text-xs font-semibold text-slate-700 mb-1">Asal Kampus / Fakultas</label>
                <input type="text" name="university" id="university" value="{{ old('university', $user->university) }}" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="phone_number" class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                <input type="text" value="{{ $user->email }}" disabled
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-400 text-sm">
                <p class="text-[10px] text-slate-400 mt-0.5">Email akun tidak dapat diubah sendiri.</p>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-2.5 bg-primary hover:bg-blue-800 text-white font-semibold rounded-xl text-sm transition shadow-sm active:scale-[0.99]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
