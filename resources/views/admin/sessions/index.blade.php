@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-bold text-slate-900">Manajemen Sesi Latihan</h1>
            <p class="text-xs text-slate-500">Jadwalkan dan kontrol sesi presensi lapangan</p>
        </div>
        <a href="{{ route('admin.sessions.create') }}" class="px-3 py-1.5 bg-primary hover:bg-blue-800 text-white rounded-xl text-xs font-semibold shadow-sm transition">
            + Buat Sesi
        </a>
    </div>

    <!-- Active Session Spotlight Card -->
    @if($activeSession)
        <div class="p-4 bg-emerald-600 text-white rounded-2xl shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <span class="px-2 py-0.5 bg-white/20 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">
                    ● Sesi Aktif Saat Ini
                </span>
                <span class="text-xs text-emerald-100">
                    {{ $activeSession->attendances()->count() }} Hadir
                </span>
            </div>
            <div>
                <h3 class="text-base font-bold">{{ $activeSession->title }}</h3>
                <p class="text-xs text-emerald-100 mt-0.5">📍 {{ $activeSession->venue }}</p>
                <p class="text-xs text-emerald-100">⏰ {{ substr($activeSession->start_time, 0, 5) }} - {{ substr($activeSession->end_time, 0, 5) }} WIB</p>
            </div>
            <div class="pt-2 border-t border-white/20 flex items-center space-x-2">
                <a href="{{ route('admin.sessions.scanner', $activeSession->id) }}" class="flex-1 py-2 bg-white text-emerald-800 hover:bg-emerald-50 rounded-xl text-xs font-bold text-center shadow-sm transition flex items-center justify-center space-x-1">
                    <span>📷</span>
                    <span>Buka Kamera Scanner</span>
                </a>
                <form action="{{ route('admin.sessions.close', $activeSession->id) }}" method="POST" class="shrink-0" onsubmit="return confirm('Tutup sesi latihan ini? Presensi baru akan dikunci.')">
                    @csrf
                    <button type="submit" class="py-2 px-3 bg-rose-700 hover:bg-rose-800 text-white rounded-xl text-xs font-semibold transition">
                        Tutup Sesi
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Session List Filter -->
    <div class="bg-white p-2.5 rounded-2xl border border-slate-200 shadow-sm flex items-center space-x-1 overflow-x-auto text-xs">
        <a href="{{ route('admin.sessions.index') }}" class="px-3 py-1.5 rounded-xl font-medium shrink-0 {{ !request('status') ? 'bg-primary text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Sesi
        </a>
        <a href="{{ route('admin.sessions.index', ['status' => 'active']) }}" class="px-3 py-1.5 rounded-xl font-medium shrink-0 {{ request('status') === 'active' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            Aktif
        </a>
        <a href="{{ route('admin.sessions.index', ['status' => 'scheduled']) }}" class="px-3 py-1.5 rounded-xl font-medium shrink-0 {{ request('status') === 'scheduled' ? 'bg-blue-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            Dijadwalkan
        </a>
        <a href="{{ route('admin.sessions.index', ['status' => 'closed']) }}" class="px-3 py-1.5 rounded-xl font-medium shrink-0 {{ request('status') === 'closed' ? 'bg-slate-800 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            Selesai
        </a>
    </div>

    <!-- Sessions List -->
    @if($sessions->isEmpty())
        <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
            <span class="text-3xl">🏸</span>
            <p class="text-xs text-slate-500 mt-2">Belum ada sesi latihan yang dibuat.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($sessions as $s)
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-2.5">
                    <div class="flex items-start justify-between">
                        <div>
                            @if($s->status === 'active')
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">AKTIF</span>
                            @elseif($s->status === 'scheduled')
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-full">DIJADWALKAN</span>
                            @elseif($s->status === 'closed')
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-full">SELESAI</span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full">DIBATALKAN</span>
                            @endif
                            <h3 class="text-sm font-bold text-slate-900 mt-1">{{ $s->title }}</h3>
                            <p class="text-xs text-slate-500">📅 {{ $s->session_date->translatedFormat('l, d F Y') }}</p>
                            <p class="text-xs text-slate-500">⏰ {{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }} WIB • 📍 {{ $s->venue }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-sm font-bold text-primary">{{ $s->attendances_count }}</span>
                            <span class="text-[10px] text-slate-400 block">Hadir</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-1.5">
                            @if($s->status === 'scheduled')
                                <form action="{{ route('admin.sessions.activate', $s->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold transition">
                                        ▶ Buka Sesi
                                    </button>
                                </form>
                                <form action="{{ route('admin.sessions.cancel', $s->id) }}" method="POST" onsubmit="return confirm('Batalkan sesi ini?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-slate-500 hover:text-rose-600 rounded-lg transition">
                                        Batal
                                    </button>
                                </form>
                            @elseif($s->status === 'active')
                                <a href="{{ route('admin.sessions.scanner', $s->id) }}" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold transition">
                                    📷 Scanner
                                </a>
                                <form action="{{ route('admin.sessions.close', $s->id) }}" method="POST" onsubmit="return confirm('Tutup sesi ini?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 rounded-lg transition border border-slate-200">
                                        Tutup
                                    </button>
                                </form>
                            @endif
                        </div>

                        <span class="text-[10px] text-slate-400">
                            Oleh: {{ $s->creator->name ?? 'Admin' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $sessions->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
