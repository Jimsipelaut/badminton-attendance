@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.reports.index', ['tab' => 'session']) }}" class="p-2 bg-white rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
            ←
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-bold text-slate-900 truncate">Detail Rekap Presensi</h1>
            <p class="text-xs text-slate-500 truncate">{{ $session->title }}</p>
        </div>
        <a href="{{ route('admin.reports.export.session', $session->id) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center space-x-1 shrink-0">
            <span>📥</span>
            <span>Unduh CSV</span>
        </a>
    </div>

    <!-- Session Info Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-2 text-xs">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $session->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                {{ strtoupper($session->status) }}
            </span>
            <span class="text-primary font-bold">Total: {{ $session->attendances->count() }} Anggota Hadir</span>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100 text-slate-600">
            <div>
                <p class="text-[11px] text-slate-400">Tanggal</p>
                <p class="font-medium text-slate-800">{{ $session->session_date->translatedFormat('d F Y') }}</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400">Waktu</p>
                <p class="font-medium text-slate-800">{{ substr($session->start_time, 0, 5) }} - {{ substr($session->end_time, 0, 5) }} WIB</p>
            </div>
            <div class="col-span-2">
                <p class="text-[11px] text-slate-400">Lokasi</p>
                <p class="font-medium text-slate-800">{{ $session->venue }}</p>
            </div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="space-y-2">
        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider px-1">
            Daftar Anggota Hadir ({{ $session->attendances->count() }})
        </h2>

        @forelse($session->attendances->sortBy('created_at') as $index => $att)
            <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between text-xs">
                <div class="flex items-center space-x-3 min-w-0">
                    <span class="text-[11px] font-bold text-slate-400 w-4 text-center shrink-0">{{ $index + 1 }}</span>
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-900 truncate">{{ $att->user->name }}</h3>
                        <p class="text-[11px] text-slate-500 truncate">{{ $att->user->nim }} • {{ $att->user->university }}</p>
                        @if($att->notes)
                            <p class="text-[10px] text-amber-600 italic mt-0.5 truncate">📝 {{ $att->notes }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-xs font-bold text-slate-800 block">{{ $att->created_at->format('H:i') }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded font-medium {{ $att->method === 'qr_scan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $att->method === 'qr_scan' ? 'QR Scan' : 'Manual' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
                <p class="text-xs text-slate-500">Belum ada anggota yang hadir pada sesi latihan ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
