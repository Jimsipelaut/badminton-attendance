@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-bold text-slate-900">Rekapitulasi Kehadiran</h1>
            <p class="text-xs text-slate-500">Laporan presensi & ekspor CSV/Excel</p>
        </div>
        @if($tab === 'member')
            <a href="{{ route('admin.reports.export.members') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center space-x-1">
                <span>📥</span>
                <span>Ekspor CSV</span>
            </a>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="grid grid-cols-2 p-1 bg-slate-200 rounded-xl text-xs font-semibold">
        <a href="{{ route('admin.reports.index', ['tab' => 'session']) }}" class="py-2 text-center rounded-lg transition {{ $tab === 'session' ? 'bg-white text-primary shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900' }}">
            🏸 Rekap Per Sesi
        </a>
        <a href="{{ route('admin.reports.index', ['tab' => 'member']) }}" class="py-2 text-center rounded-lg transition {{ $tab === 'member' ? 'bg-white text-primary shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900' }}">
            👥 Rekap Per Anggota
        </a>
    </div>

    @if($tab === 'session')
        <!-- Tab 1: Rekap Per Sesi -->
        <div class="space-y-3">
            <!-- Filter Tanggal -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm space-y-2.5 text-xs">
                <input type="hidden" name="tab" value="session">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs">
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="submit" class="flex-1 py-1.5 bg-primary text-white rounded-lg font-semibold hover:bg-blue-800 transition">
                        Filter
                    </button>
                    @if(request('start_date') || request('end_date'))
                        <a href="{{ route('admin.reports.index', ['tab' => 'session']) }}" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- List Sesi Rekap -->
            @forelse($sessions as $s)
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $s->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ strtoupper($s->status) }}
                            </span>
                            <h3 class="text-sm font-bold text-slate-900 mt-1">{{ $s->title }}</h3>
                            <p class="text-xs text-slate-500">📅 {{ $s->session_date->translatedFormat('l, d F Y') }}</p>
                            <p class="text-xs text-slate-400">📍 {{ $s->venue }} • ⏰ {{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-bold text-primary">{{ $s->attendances_count }}</span>
                            <span class="text-[10px] text-slate-400 block">Hadir</span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end space-x-2 text-xs">
                        <a href="{{ route('admin.reports.session', $s->id) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition">
                            Detail Peserta
                        </a>
                        <a href="{{ route('admin.reports.export.session', $s->id) }}" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition">
                            📥 Unduh CSV
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
                    <p class="text-xs text-slate-500">Tidak ada sesi yang ditemukan.</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $sessions->withQueryString()->links() }}
            </div>
        </div>
    @else
        <!-- Tab 2: Rekap Per Anggota -->
        <div class="space-y-3">
            <!-- Pencarian Anggota -->
            <form action="{{ route('admin.reports.index') }}" method="GET" class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm text-xs">
                <input type="hidden" name="tab" value="member">
                <div class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIM, atau universitas..." class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-primary focus:bg-white">
                    <button type="submit" class="px-3 py-2 bg-primary text-white rounded-xl font-bold hover:bg-blue-800 transition">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.reports.index', ['tab' => 'member']) }}" class="px-2.5 py-2 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition">
                            ✕
                        </a>
                    @endif
                </div>
            </form>

            <!-- Ringkasan Info -->
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900 flex items-center justify-between">
                <span>Total Sesi Terselenggara: <strong>{{ $totalClosedSessions }} Sesi</strong></span>
                <span class="text-[10px] text-blue-700 font-medium">Berdasarkan sesi aktif/selesai</span>
            </div>

            <!-- Tabel / Kartu Kehadiran Anggota -->
            <div class="space-y-2">
                @forelse($members as $m)
                    @php
                        $pct = $totalClosedSessions > 0 ? round(($m->attendances_count / $totalClosedSessions) * 100, 1) : 0;
                    @endphp
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $m->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $m->nim }} • {{ $m->university }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-sm font-bold text-primary">{{ $m->attendances_count }}</span>
                                <span class="text-[10px] text-slate-400">/ {{ $totalClosedSessions }} Sesi</span>
                            </div>
                        </div>

                        <!-- Progress Bar Persentase -->
                        <div>
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="text-slate-400">Tingkat Kehadiran</span>
                                <span class="font-bold {{ $pct >= 75 ? 'text-emerald-600' : ($pct >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $pct }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $pct >= 75 ? 'bg-emerald-500' : ($pct >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
                        <p class="text-xs text-slate-500">Tidak ada anggota yang cocok dengan filter.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $members->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
