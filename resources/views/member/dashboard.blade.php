@extends('layouts.app')

@section('content')
<div class="space-y-4">

    <!-- Profile Header Card -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-800 text-white p-5 rounded-2xl shadow-sm">
        <div class="flex items-center space-x-3.5">
            <div class="w-14 h-14 rounded-full bg-white/10 border-2 border-white/20 flex items-center justify-center text-xl font-bold overflow-hidden shrink-0">
                @if($user->avatar_path)
                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
            </div>
            <div class="min-w-0">
                <h2 class="text-base font-bold truncate">{{ $user->name }}</h2>
                <p class="text-xs text-blue-200">NIM: {{ $user->nim }}</p>
                <p class="text-xs text-blue-200 truncate">{{ $user->university }}</p>
            </div>
        </div>

        <!-- Kehadiran Rate Bar -->
        <div class="mt-4 pt-3.5 border-t border-white/10 flex items-center justify-between text-xs">
            <div>
                <span class="text-blue-200">Kehadiran:</span>
                <span class="font-bold text-white ml-1">{{ $attendedCount }} / {{ $totalSessions }} Sesi</span>
            </div>
            <div class="font-bold text-emerald-300 text-sm">
                {{ $percentage }}%
            </div>
        </div>
    </div>

    <!-- Active Session Card / Info -->
    @if($activeSession)
        <div class="bg-white p-4 rounded-2xl border-2 border-emerald-500 shadow-sm relative overflow-hidden">
            <div class="flex items-start justify-between">
                <div>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase tracking-wider">
                        Sesi Sedang Dibuka
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 mt-1.5">{{ $activeSession->title }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">📍 {{ $activeSession->venue }}</p>
                    <p class="text-xs text-slate-500">⏰ {{ substr($activeSession->start_time, 0, 5) }} - {{ substr($activeSession->end_time, 0, 5) }} WIB</p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                @if($alreadyAttended)
                    <div class="flex items-center space-x-1.5 text-xs font-semibold text-emerald-600">
                        <span>✅</span>
                        <span>Anda sudah tercatat hadir</span>
                    </div>
                @else
                    <div class="flex items-center space-x-1.5 text-xs text-amber-600 font-medium">
                        <span>⏳</span>
                        <span>Silakan tunjukkan QR Badge Anda ke sekretaris</span>
                    </div>
                @endif

                <a href="{{ route('member.badge') }}"
                   class="px-3.5 py-1.5 bg-primary hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                    Buka QR
                </a>
            </div>
        </div>
    @else
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-center py-6">
            <span class="text-3xl">🏸</span>
            <h4 class="text-sm font-semibold text-slate-800 mt-2">Tidak Ada Sesi Latihan Aktif</h4>
            <p class="text-xs text-slate-500 mt-0.5">Sesi presensi akan muncul saat dibuka oleh pengurus di lapangan.</p>
        </div>
    @endif

    <!-- Quick Action / QR Badge Shortcut -->
    <a href="{{ route('member.badge') }}" class="block p-4 bg-white rounded-2xl border border-slate-200 hover:border-primary shadow-sm transition flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-primary flex items-center justify-center text-xl">
                🎟️
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900">QR Badge Kehadiran</p>
                <p class="text-[11px] text-slate-500">Tunjukkan untuk di-scan oleh sekretaris</p>
            </div>
        </div>
        <span class="text-slate-400 text-sm">➔</span>
    </a>

    <!-- Recent Attendance History -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Riwayat Terakhir</h4>
            <a href="{{ route('member.history') }}" class="text-xs text-primary font-semibold hover:underline">Lihat Semua</a>
        </div>

        @if($recentAttendances->isEmpty())
            <p class="text-xs text-slate-400 py-3 text-center">Belum ada catatan presensi latihan.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($recentAttendances as $att)
                    <div class="py-2.5 flex items-center justify-between text-xs">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $att->session->title ?? 'Sesi Latihan' }}</p>
                            <p class="text-[11px] text-slate-400">
                                {{ \Carbon\Carbon::parse($att->check_in_time)->translatedFormat('d M Y, H:i') }} WIB
                            </p>
                        </div>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-semibold rounded-md text-[10px]">
                            {{ strtoupper($att->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
