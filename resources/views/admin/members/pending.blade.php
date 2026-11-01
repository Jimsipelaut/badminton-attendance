@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-bold text-slate-900">Persetujuan Anggota Baru</h1>
            <p class="text-xs text-slate-500">Daftar pendaftar menunggu verifikasi pengurus</p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="text-xs text-primary font-semibold hover:underline">
            Semua Anggota
        </a>
    </div>

    @if($pendingMembers->isEmpty())
        <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
            <span class="text-3xl">✅</span>
            <p class="text-sm font-semibold text-slate-800 mt-2">Semua Pendaftaran Sudah Diverifikasi</p>
            <p class="text-xs text-slate-400 mt-0.5">Tidak ada akun anggota yang berstatus pending saat ini.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($pendingMembers as $m)
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-base shrink-0">
                            {{ strtoupper(substr($m->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-slate-900 truncate">{{ $m->name }}</h4>
                            <p class="text-xs text-slate-600 font-medium">NIM: {{ $m->nim }}</p>
                            <p class="text-xs text-slate-500 truncate">🏛️ {{ $m->university }}</p>
                            <p class="text-xs text-slate-500">📱 {{ $m->phone_number ?? '-' }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Mendaftar: {{ $m->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center space-x-2">
                        <form action="{{ route('admin.members.approve', $m->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                ✓ Setujui
                            </button>
                        </form>
                        <form action="{{ route('admin.members.reject', $m->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Tolak pendaftaran ini?')">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 rounded-xl text-xs font-semibold transition border border-slate-200">
                                ✕ Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $pendingMembers->links() }}
        </div>
    @endif
</div>
@endsection
