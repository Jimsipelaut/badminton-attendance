<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Tampilan Scanner Kamera & Fallback Manual untuk Sesi Aktif
     */
    public function scanner($sessionId)
    {
        $session = TrainingSession::with(['attendances.user'])->findOrFail($sessionId);

        if ($session->status !== 'active') {
            return redirect()->route('admin.sessions.index')
                ->with('error', 'Presensi hanya dapat dibuka untuk sesi yang berstatus AKTIF.');
        }

        // Ambil ID anggota yang sudah hadir pada sesi ini
        $attendedUserIds = $session->attendances->pluck('user_id')->toArray();

        // Ambil daftar anggota aktif yang BELUM hadir untuk opsi manual
        $absentMembers = User::where('role', 'member')
            ->where('status', 'approved')
            ->whereNotIn('id', $attendedUserIds)
            ->orderBy('name')
            ->get();

        return view('admin.sessions.scanner', compact('session', 'absentMembers'));
    }

    /**
     * Endpoint API AJAX Scan QR Token oleh Kamera Pengurus
     */
    public function scan(Request $request, $sessionId)
    {
        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $session = TrainingSession::findOrFail($sessionId);

        if ($session->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi latihan ini sudah tidak aktif / ditutup.',
            ], 422);
        }

        // Cari anggota pemilik token
        $member = User::where('qr_token', $request->qr_token)->first();

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak dikenali atau tidak valid dalam sistem UKM.',
            ], 404);
        }

        if ($member->status !== 'approved') {
            return response()->json([
                'status' => 'error',
                'message' => "Akun anggota {$member->name} berstatus {$member->status} (belum disetujui pengurus).",
            ], 403);
        }

        // Cek duplikasi kehadiran pada sesi yang sama (FR-ABSEN-03)
        $existing = Attendance::where('training_session_id', $session->id)
            ->where('user_id', $member->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'warning',
                'message' => "Anggota {$member->name} SUDAH tercatat hadir sebelumnya pada jam {$existing->created_at->format('H:i:s')} WIB.",
                'data' => [
                    'name' => $member->name,
                    'nim' => $member->nim,
                    'university' => $member->university,
                    'avatar_url' => $member->avatar_path ? asset('storage/' . $member->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=0A264A&color=fff',
                    'time' => $existing->created_at->format('H:i') . ' WIB',
                    'method' => $existing->method,
                ],
            ], 200);
        }

        // Rekam kehadiran baru (FR-ABSEN-01)
        $attendance = Attendance::create([
            'training_session_id' => $session->id,
            'user_id' => $member->id,
            'method' => 'qr_scan',
            'recorded_by' => Auth::id(),
            'notes' => null,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'scan_attendance',
            'description' => "Scan QR presensi: {$member->name} ({$member->nim}) hadir di {$session->title}",
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi berhasil dicatat!',
            'data' => [
                'name' => $member->name,
                'nim' => $member->nim,
                'university' => $member->university,
                'avatar_url' => $member->avatar_path ? asset('storage/' . $member->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=0A264A&color=fff',
                'time' => $attendance->created_at->format('H:i') . ' WIB',
                'method' => 'QR Scan',
            ],
        ]);
    }

    /**
     * Fallback Manual Presensi oleh Sekretaris (FR-ABSEN-04)
     */
    public function manualAttendance(Request $request, $sessionId)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $session = TrainingSession::findOrFail($sessionId);

        if ($session->status !== 'active') {
            return back()->with('error', 'Sesi latihan sudah tidak aktif.');
        }

        $member = User::findOrFail($request->user_id);

        if ($member->status !== 'approved') {
            return back()->with('error', 'Hanya anggota berstatus disetujui yang dapat dipresensikan.');
        }

        $existing = Attendance::where('training_session_id', $session->id)
            ->where('user_id', $member->id)
            ->first();

        if ($existing) {
            return back()->with('error', "Anggota {$member->name} sudah tercatat hadir.");
        }

        Attendance::create([
            'training_session_id' => $session->id,
            'user_id' => $member->id,
            'method' => 'manual',
            'recorded_by' => Auth::id(),
            'notes' => $request->notes ?? 'Input manual sekretaris di lapangan',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'manual_attendance',
            'description' => "Input manual presensi: {$member->name} hadir di {$session->title}. Alasan: " . ($request->notes ?? 'Manual'),
        ]);

        return back()->with('success', "Kehadiran {$member->name} berhasil dicatat secara manual.");
    }
}
