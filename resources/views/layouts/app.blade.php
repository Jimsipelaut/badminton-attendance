<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'UKM Bulutangkis Presensi' }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E40AF',
                        secondary: '#0F766E',
                        accent: '#F59E0B',
                        dark: '#0F172A',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            -webkit-tap-highlight-color: transparent;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col justify-between antialiased">

    <!-- Top App Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-md mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-xl">🏸</span>
                <span class="font-bold text-slate-900 tracking-tight text-base">UKM Bulutangkis</span>
            </div>

            @auth
                <div class="flex items-center space-x-3 text-xs">
                    <span class="px-2 py-0.5 rounded-full font-semibold {{ Auth::user()->role === 'admin' ? 'bg-red-100 text-red-700' : (Auth::user()->role === 'secretary' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:text-red-600 font-medium">Keluar</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-md w-full mx-auto p-4 pb-24">
        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center space-x-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center space-x-2">
                <span>⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                <p class="font-semibold mb-1">Perhatian:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Navigation Bar (Auth Users) -->
    @auth
        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40 shadow-lg">
            <div class="max-w-md mx-auto px-4 h-16 flex items-center justify-around text-xs font-medium">
                @if(Auth::user()->role === 'member')
                    <a href="{{ route('member.dashboard') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('member.dashboard') ? 'text-primary font-bold' : 'text-slate-500' }}">
                        <span class="text-lg">🏠</span>
                        <span>Beranda</span>
                    </a>
                    <a href="{{ route('member.badge') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('member.badge') ? 'text-primary font-bold' : 'text-slate-500' }}">
                        <span class="text-lg">🎟️</span>
                        <span>QR Badge</span>
                    </a>
                    <a href="{{ route('member.history') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('member.history') ? 'text-primary font-bold' : 'text-slate-500' }}">
                        <span class="text-lg">📜</span>
                        <span>Riwayat</span>
                    </a>
                    <a href="{{ route('member.profile') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('member.profile') ? 'text-primary font-bold' : 'text-slate-500' }}">
                        <span class="text-lg">👤</span>
                        <span>Profil</span>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.dashboard') ? 'text-primary font-bold' : 'text-slate-500' }}">
                        <span class="text-lg">📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.sessions.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.sessions.*') ? 'text-primary font-bold' : 'text-slate-500' }}">
                        <span class="text-lg">🏸</span>
                        <span>Sesi</span>
                    </a>
                    <a href="{{ route('admin.members.pending') }}" class="flex flex-col items-center py-1 relative {{ request()->routeIs('admin.members.*') ? 'text-primary font-bold' : 'text-slate-500' }}">
                        <span class="text-lg">👥</span>
                        <span>Anggota</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center py-1 {{ request()->routeIs('admin.reports.*') ? 'text-primary font-bold' : 'text-slate-500' }}">
                        <span class="text-lg">📑</span>
                        <span>Rekap</span>
                    </a>
                @endif
            </div>
        </nav>
    @endauth

    @stack('scripts')
</body>
</html>
