@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.sessions.index') }}" class="p-2 bg-white rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                ←
            </a>
            <div>
                <h1 class="text-base font-bold text-slate-900 leading-tight">Presensi Lapangan</h1>
                <p class="text-xs text-slate-500">{{ $session->title }}</p>
            </div>
        </div>
        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-full animate-pulse">
            ● SESI AKTIF
        </span>
    </div>

    <!-- Mode Selector Tabs -->
    <div class="grid grid-cols-2 p-1 bg-slate-200 rounded-xl text-xs font-semibold">
        <button id="tabScannerBtn" onclick="switchTab('scanner')" class="py-2 rounded-lg bg-white text-primary shadow-sm transition">
            📷 Scan QR Badge
        </button>
        <button id="tabManualBtn" onclick="switchTab('manual')" class="py-2 rounded-lg text-slate-600 hover:text-slate-900 transition">
            ✍️ Input Manual
        </button>
    </div>

    <!-- Tab 1: Scanner Kamera WebRTC -->
    <div id="scannerSection" class="space-y-3">
        <!-- Viewfinder Card -->
        <div class="bg-slate-900 rounded-3xl p-4 text-white text-center shadow-lg relative overflow-hidden">
            <div id="reader" class="w-full rounded-2xl overflow-hidden bg-black aspect-square max-h-72 mx-auto"></div>

            <div id="cameraControls" class="mt-3 flex items-center justify-center space-x-2 text-xs">
                <button id="startScanBtn" onclick="startScanner()" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow transition">
                    Nyalakan Kamera
                </button>
                <button id="stopScanBtn" onclick="stopScanner()" class="hidden px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition">
                    Matikan Kamera
                </button>
            </div>
            <p id="scanStatusText" class="text-[11px] text-slate-400 mt-2">Arahkan kamera ke QR Badge di HP anggota.</p>
        </div>

        <!-- Pop-up Kartu Verifikasi Visual Anti-Joki (FR-ABSEN-02) -->
        <div id="verificationCard" class="hidden p-4 rounded-2xl border-2 transition-all duration-300">
            <div class="flex items-center space-x-3.5">
                <img id="vAvatar" src="" alt="Avatar" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm shrink-0">
                <div class="flex-1 min-w-0">
                    <span id="vBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full inline-block mb-1"></span>
                    <h3 id="vName" class="text-sm font-bold truncate"></h3>
                    <p id="vNim" class="text-xs opacity-90 truncate"></p>
                    <p id="vUniv" class="text-[11px] opacity-75 truncate"></p>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-black/10 flex items-center justify-between text-xs">
                <span id="vMessage" class="font-medium text-[11px]"></span>
                <span id="vTime" class="font-bold"></span>
            </div>
        </div>
    </div>

    <!-- Tab 2: Input Manual (Fallback Sinyal/Kamera Bermasalah FR-ABSEN-04) -->
    <div id="manualSection" class="hidden bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div>
            <h2 class="text-sm font-bold text-slate-900">Input Presensi Manual</h2>
            <p class="text-xs text-slate-500">Gunakan bila HP anggota mati atau kamera gagal memindai</p>
        </div>

        @if($absentMembers->isEmpty())
            <div class="p-4 bg-slate-50 rounded-xl text-center text-xs text-slate-500">
                Semua anggota aktif sudah tercatat hadir pada sesi ini.
            </div>
        @else
            <form action="{{ route('admin.sessions.manual', $session->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Anggota</label>
                    <select name="user_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-primary focus:bg-white">
                        <option value="">-- Pilih Nama Anggota --</option>
                        @foreach($absentMembers as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->nim }} - {{ $m->university }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Alasan (Opsional)</label>
                    <input type="text" name="notes" placeholder="cth: Baterai HP habis, izin terlambat..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-primary focus:bg-white">
                </div>
                <button type="submit" class="w-full py-2.5 bg-primary hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition">
                    Simpan Presensi Manual
                </button>
            </form>
        @endif
    </div>

    <!-- Live Attendance List -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Daftar Hadir Sesi Ini (<span id="totalAttendedCount">{{ $session->attendances->count() }}</span>)
            </h2>
            <span class="text-[10px] text-slate-400">Paling baru di atas</span>
        </div>

        <div id="attendanceListContainer" class="space-y-2 max-h-64 overflow-y-auto">
            @forelse($session->attendances->sortByDesc('created_at') as $att)
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0">
                            {{ substr($att->user->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 truncate">{{ $att->user->name }}</p>
                            <p class="text-[10px] text-slate-400">{{ $att->user->nim }} • {{ $att->user->university }}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[10px] font-semibold text-slate-600 block">{{ $att->created_at->format('H:i') }}</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded {{ $att->method === 'qr_scan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $att->method === 'qr_scan' ? 'QR Scan' : 'Manual' }}
                        </span>
                    </div>
                </div>
            @empty
                <p id="emptyAttendanceNotice" class="text-center text-xs text-slate-400 py-4">Belum ada anggota yang presensi pada sesi ini.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Library html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let isProcessing = false;
    let autoDismissTimer = null;

    // Web Audio API Beep Synthesizer
    function playBeep(isSuccess = true) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (isSuccess) {
                osc.frequency.setValueAtTime(880, ctx.currentTime); // A5 note
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                osc.start();
                osc.stop(ctx.currentTime + 0.15);
            } else {
                osc.frequency.setValueAtTime(300, ctx.currentTime);
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                osc.start();
                osc.stop(ctx.currentTime + 0.3);
            }
        } catch (e) {
            // AudioContext might be restricted until user gesture
        }
    }

    function switchTab(tab) {
        const scannerSec = document.getElementById('scannerSection');
        const manualSec = document.getElementById('manualSection');
        const tabScanBtn = document.getElementById('tabScannerBtn');
        const tabManBtn = document.getElementById('tabManualBtn');

        if (tab === 'scanner') {
            scannerSec.classList.remove('hidden');
            manualSec.classList.add('hidden');
            tabScanBtn.classList.add('bg-white', 'text-primary', 'shadow-sm');
            tabScanBtn.classList.remove('text-slate-600');
            tabManBtn.classList.remove('bg-white', 'text-primary', 'shadow-sm');
            tabManBtn.classList.add('text-slate-600');
        } else {
            scannerSec.classList.add('hidden');
            manualSec.classList.remove('hidden');
            tabManBtn.classList.add('bg-white', 'text-primary', 'shadow-sm');
            tabManBtn.classList.remove('text-slate-600');
            tabScanBtn.classList.remove('bg-white', 'text-primary', 'shadow-sm');
            tabScanBtn.classList.add('text-slate-600');
            stopScanner();
        }
    }

    function startScanner() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = {
            fps: 10,
            qrbox: { width: 220, height: 220 },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanError
        ).then(() => {
            document.getElementById('startScanBtn').classList.add('hidden');
            document.getElementById('stopScanBtn').classList.remove('hidden');
            document.getElementById('scanStatusText').textContent = "Kamera aktif. Dekatkan ke QR Code.";
        }).catch(err => {
            alert("Tidak dapat mengakses kamera: " + err + ". Pastikan izin kamera aktif atau gunakan HTTPS/localhost.");
        });
    }

    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                document.getElementById('startScanBtn').classList.remove('hidden');
                document.getElementById('stopScanBtn').classList.add('hidden');
                document.getElementById('scanStatusText').textContent = "Kamera dimatikan.";
            });
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;

        document.getElementById('scanStatusText').textContent = "Memverifikasi QR Code...";

        fetch("{{ route('admin.sessions.scan', $session->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_token: decodedText })
        })
        .then(res => res.json())
        .then(result => {
            if (result.status === 'success') {
                playBeep(true);
                showVerificationCard(result.data, 'success', result.message);
                prependAttendanceRow(result.data);
            } else if (result.status === 'warning') {
                playBeep(false);
                showVerificationCard(result.data, 'warning', result.message);
            } else {
                playBeep(false);
                alert("GAGAL: " + result.message);
            }
        })
        .catch(err => {
            playBeep(false);
            alert("Koneksi gagal saat memverifikasi QR: " + err);
        })
        .finally(() => {
            setTimeout(() => {
                isProcessing = false;
                document.getElementById('scanStatusText').textContent = "Siap scan anggota berikutnya.";
            }, 2500);
        });
    }

    function onScanError(errorMessage) {
        // Abaikan parse noise dari frame kamera
    }

    function showVerificationCard(data, type, message) {
        clearTimeout(autoDismissTimer);
        const card = document.getElementById('verificationCard');
        const vAvatar = document.getElementById('vAvatar');
        const vBadge = document.getElementById('vBadge');
        const vName = document.getElementById('vName');
        const vNim = document.getElementById('vNim');
        const vUniv = document.getElementById('vUniv');
        const vMessage = document.getElementById('vMessage');
        const vTime = document.getElementById('vTime');

        vAvatar.src = data.avatar_url;
        vName.textContent = data.name;
        vNim.textContent = "NIM: " + data.nim;
        vUniv.textContent = data.university;
        vMessage.textContent = message;
        vTime.textContent = data.time;

        if (type === 'success') {
            card.className = "p-4 rounded-2xl border-2 bg-emerald-50 border-emerald-500 text-emerald-950 shadow-md block transition";
            vBadge.className = "text-[10px] font-bold px-2 py-0.5 rounded-full inline-block mb-1 bg-emerald-200 text-emerald-800";
            vBadge.textContent = "✓ BERHASIL HADIR";
        } else {
            card.className = "p-4 rounded-2xl border-2 bg-amber-50 border-amber-500 text-amber-950 shadow-md block transition";
            vBadge.className = "text-[10px] font-bold px-2 py-0.5 rounded-full inline-block mb-1 bg-amber-200 text-amber-900";
            vBadge.textContent = "⚠ SUDAH TERCATAT";
        }

        autoDismissTimer = setTimeout(() => {
            card.classList.add('hidden');
        }, 5000);
    }

    function prependAttendanceRow(data) {
        const container = document.getElementById('attendanceListContainer');
        const emptyNotice = document.getElementById('emptyAttendanceNotice');
        if (emptyNotice) emptyNotice.remove();

        const countSpan = document.getElementById('totalAttendedCount');
        if (countSpan) {
            countSpan.textContent = parseInt(countSpan.textContent || 0) + 1;
        }

        const div = document.createElement('div');
        div.className = "flex items-center justify-between p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-100 text-xs animate-pulse";
        div.innerHTML = `
            <div class="flex items-center space-x-2.5 min-w-0">
                <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                    ${data.name.substring(0, 1)}
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-slate-800 truncate">${data.name}</p>
                    <p class="text-[10px] text-slate-400">${data.nim} • ${data.university}</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-[10px] font-semibold text-slate-600 block">${data.time}</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">${data.method}</span>
            </div>
        `;
        container.insertBefore(div, container.firstChild);
    }
</script>
@endsection
