@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-base font-bold text-slate-900">Database Anggota UKM</h1>
            <p class="text-xs text-slate-500">Kelola dan pantau seluruh data keanggotaan</p>
        </div>
        @if($pendingCount > 0)
            <a href="{{ route('admin.members.pending') }}" class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">
                {{ $pendingCount }} Pending
            </a>
        @endif
    </div>

    <!-- Search & Filter Form -->
    <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.members.index') }}" class="flex items-center space-x-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari Nama, NIM, atau Kampus..."
                   class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-primary">

            <select name="status" class="px-2 py-2 rounded-xl border border-slate-300 text-xs text-slate-600 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button type="submit" class="px-3 py-2 bg-primary text-white rounded-xl text-xs font-semibold">
                Cari
            </button>
        </form>
    </div>

    <!-- Member List -->
    @if($members->isEmpty())
        <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center">
            <span class="text-3xl">👥</span>
            <p class="text-xs text-slate-500 mt-2">Tidak ditemukan data anggota yang cocok.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 shadow-sm">
            @foreach($members as $m)
                <div class="p-3.5 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr($m->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-slate-900 truncate">{{ $m->name }}</h4>
                            <p class="text-[11px] text-slate-500 truncate">NIM: {{ $m->nim }} • {{ $m->university }}</p>
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        @if($m->status === 'approved')
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-semibold rounded-md text-[10px]">
                                Approved
                            </span>
                        @elseif($m->status === 'pending')
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 font-semibold rounded-md text-[10px]">
                                Pending
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-semibold rounded-md text-[10px]">
                                Rejected
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $members->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
