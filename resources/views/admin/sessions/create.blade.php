@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.sessions.index') }}" class="p-2 bg-white rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
            ←
        </a>
        <div>
            <h1 class="text-base font-bold text-slate-900">Jadwalkan Sesi Baru</h1>
            <p class="text-xs text-slate-500">Buat jadwal latihan UKM Bulutangkis</p>
        </div>
    </div>

    <form action="{{ route('admin.sessions.store') }}" method="POST" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        @csrf

        <!-- Judul Sesi -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama / Judul Sesi</label>
            <input type="text" name="title" value="{{ old('title', 'Latihan Rutin Mingguan') }}" required
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                placeholder="cth: Latihan Rutin Sabtu Sore">
            @error('title')
                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal Sesi -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Sesi</label>
            <input type="date" name="session_date" value="{{ old('session_date', date('Y-m-d')) }}" required
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition">
            @error('session_date')
                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Jam Mulai & Selesai -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai</label>
                <input type="time" name="start_time" value="{{ old('start_time', '16:00') }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition">
                @error('start_time')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai</label>
                <input type="time" name="end_time" value="{{ old('end_time', '19:00') }}" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition">
                @error('end_time')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Lokasi / Venue -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi / Lapangan</label>
            <input type="text" name="venue" value="{{ old('venue', 'GOR Bulutangkis Kampus') }}" required
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                placeholder="cth: GOR Bulutangkis Lapangan 1 & 2">
            @error('venue')
                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Catatan Tambahan -->
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
            <textarea name="notes" rows="2"
                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                placeholder="Bawa shuttlecock cadangan, pakaian olahraga lengkap...">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full py-3 bg-primary hover:bg-blue-800 text-white rounded-xl text-sm font-semibold shadow-sm transition">
            Simpan Jadwal Sesi
        </button>
    </form>
</div>
@endsection
