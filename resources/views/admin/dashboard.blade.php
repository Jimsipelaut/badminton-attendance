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
