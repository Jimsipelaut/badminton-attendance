@extends('layouts.app')

@section('content')
<div class="space-y-4 pt-2">

    <!-- QR Badge Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 text-center">

        <!-- Avatar & Name Header -->
        <div class="flex flex-col items-center">
            <div class="w-20 h-20 rounded-full bg-slate-100 border-2 border-primary/20 flex items-center justify-center text-2xl font-bold overflow-hidden shadow-inner mb-3">
                @if($user->avatar_path)
                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
            </div>
            <h1 class="text-lg font-bold text-slate-900">{{ $user->name }}</h1>
            <p class="text-xs font-semibold text-primary">NIM: {{ $user->nim }}</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $user->university }}</p>
        </div>

        <!-- QR Code Container -->
        <div class="my-6 p-4 bg-slate-50 rounded-2xl border border-slate-200 inline-block shadow-inner">
            <div id="qrcode" class="flex justify-center"></div>
        </div>

        <!-- Field Tip for Badminton Court -->
        <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-2xl text-[11px] text-amber-900 flex items-center space-x-2 text-left">
            <span class="text-base shrink-0">💡</span>
            <span><strong>Tips Lapangan:</strong> Naikkan kecerahan layar HP ke tingkat maksimal agar kamera pemindai membaca QR seketika di pencahayaan GOR.</span>
        </div>

        <p class="text-xs text-slate-500 font-medium max-w-xs mx-auto">
            Sodorkan QR Code ini ke kamera smartphone Sekretaris di meja presensi lapangan.
        </p>

        <!-- Status Pill -->
        <div class="mt-4 inline-flex items-center space-x-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Badge Aktif & Terverifikasi</span>
        </div>
    </div>

    <!-- Security & Reset Options -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-2">
        <p class="font-bold text-slate-800">⚠️ Keamanan Token</p>
        <p class="text-[11px] text-slate-500">
            Jangan bagikan tangkapan layar (screenshot) QR ini ke orang lain. Jika dicurigai bocor, Anda dapat memperbarui token:
        </p>
        <form action="{{ route('member.badge.regenerate') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membuat QR baru? QR lama akan langsung tidak berlaku.')">
            @csrf
            <button type="submit" class="w-full mt-2 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition border border-slate-300">
                🔄 Perbarui / Reset QR Code
            </button>
        </form>
    </div>

</div>

<!-- QRCode Library via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const qrContainer = document.getElementById("qrcode");
        new QRCode(qrContainer, {
            text: "{{ $user->qr_token }}",
            width: 220,
            height: 220,
            colorDark: "#0F172A",
            colorLight: "#FFFFFF",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Cegah layar sleep saat menunggu antrean presensi di lapangan
        if ('wakeLock' in navigator) {
            let wakeLock = null;
            const requestWakeLock = async () => {
                try {
                    wakeLock = await navigator.wakeLock.request('screen');
                } catch (err) {}
            };
            requestWakeLock();
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') requestWakeLock();
            });
        }
    });
</script>
@endsection
