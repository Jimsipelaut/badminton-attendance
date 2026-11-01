@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-base font-bold text-slate-900">Riwayat Presensi</h1>
        <span class="text-xs text-slate-500">{{ $attendances->total() }} Catatan</span>
    </div>

    @if($attendances->isEmpty())
        <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
            <span class="text-3xl">📜</span>
            <p class="text-xs text-slate-500 mt-2">Belum ada data kehadiran yang tercatat.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm divide-y divide-slate-100">
            @foreach($attendances as $att)
                <div class="p-4 flex items-center justify-between text-xs">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $att->session->title ?? 'Sesi Latihan' }}</h4>
                        <p class="text-slate-500 mt-0.5">📍 {{ $att->session->venue ?? 'Lapangan' }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Waktu: {{ \Carbon\Carbon::parse($att->check_in_time)->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-lg text-xs">
                            {{ strtoupper($att->status) }}
                        </span>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase">
                            Metode: {{ $att->method === 'qr_scan' ? 'Scan QR' : 'Manual' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
@endsection
