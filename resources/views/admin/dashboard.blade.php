@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-bold text-slate-900">Dashboard Pengurus</h1>
            <p class="text-xs text-slate-500">Panel kendali latihan & kehadiran UKM</p>
        </div>
        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[11px] font-bold rounded-full">
            {{ strtoupper(Auth::user()->role) }}
        </span>
    </div>

    <!-- Active Session Banner (if any) -->
    @php
        $activeSession = \App\Models\TrainingSession::where('status', 'active')->first();
    @endphp
    @if($activeSession)
        <div class="p-4 bg-emerald-600 text-white rounded-2xl shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="px-2 py-0.5 bg-white/20 text-white text-[10px] font-bold rounded-full uppercase">
                    ● Sesi Sedang Berlangsung
                </span>
                <span class="text-xs text-emerald-100">{{ $activeSession->attendances()->count() }} Hadir</span>
            </div>
            <h3 class="text-sm font-bold">{{ $activeSession->title }}</h3>
            <p class="text-xs text-emerald-100">📍 {{ $activeSession->venue }} • ⏰ {{ substr($activeSession->start_time, 0, 5) }} - {{ substr($activeSession->end_time, 0, 5) }} WIB</p>
            <div class="pt-2">
                <a href="{{ route('admin.sessions.scanner', $activeSession->id) }}" class="block w-full py-2 bg-white text-emerald-800 hover:bg-emerald-50 rounded-xl text-xs font-bold text-center transition">
                    Buka Scanner Kamera Presensi 📷
                </a>
            </div>
        </div>
    @endif

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('admin.members.pending') }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm block hover:border-primary">
            <span class="text-2xl">⏳</span>
            <p class="text-xl font-bold text-slate-900 mt-2">{{ \App\Models\User::where('role', 'member')->where('status', 'pending')->count() }}</p>
            <p class="text-xs text-slate-500">Pendaftar Pending</p>
        </a>
        <a href="{{ route('admin.members.index') }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm block hover:border-primary">
            <span class="text-2xl">👥</span>
            <p class="text-xl font-bold text-slate-900 mt-2">{{ \App\Models\User::where('role', 'member')->where('status', 'approved')->count() }}</p>
            <p class="text-xs text-slate-500">Anggota Aktif</p>
        </a>
    </div>

    <!-- Quick Shortcuts -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-2 text-xs">
        <p class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Menu Cepat</p>
        <a href="{{ route('admin.sessions.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 border border-slate-100 font-semibold text-slate-700">
            <span>🏸 Kelola Sesi Latihan</span>
            <span>➔</span>
        </a>
        <a href="{{ route('admin.members.pending') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 border border-slate-100 font-semibold text-slate-700">
            <span>👥 Verifikasi Akun Baru</span>
            <span>➔</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 border border-slate-100 font-semibold text-slate-700">
            <span>📑 Rekapitulasi Presensi</span>
            <span>➔</span>
        </a>
    </div>
</div>
@endsection
