<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Cari sesi aktif saat ini
        $activeSession = TrainingSession::where('status', 'active')->first();

        // Cek apakah anggota sudah absen pada sesi aktif
        $alreadyAttended = false;
        if ($activeSession) {
            $alreadyAttended = Attendance::where('training_session_id', $activeSession->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        // Statistik kehadiran
        $totalSessions = TrainingSession::where('status', 'closed')->count();
        $attendedCount = Attendance::where('user_id', $user->id)
            ->whereHas('session', function ($query) {
                $query->where('status', 'closed');
            })
            ->count();

        $percentage = $totalSessions > 0 ? round(($attendedCount / $totalSessions) * 100, 1) : 100;

        // 5 riwayat kehadiran terakhir
        $recentAttendances = Attendance::with('session')
            ->where('user_id', $user->id)
            ->latest('check_in_time')
            ->take(5)
            ->get();

        return view('member.dashboard', compact(
            'user',
            'activeSession',
            'alreadyAttended',
            'totalSessions',
            'attendedCount',
            'percentage',
            'recentAttendances'
        ));
    }

    public function badge()
    {
        $user = Auth::user();
        return view('member.badge', compact('user'));
    }

    public function history()
    {
        $user = Auth::user();
        $attendances = Attendance::with('session')
            ->where('user_id', $user->id)
            ->latest('check_in_time')
            ->paginate(15);

        return view('member.history', compact('user', 'attendances'));
    }

    public function showProfile()
    {
        $user = Auth::user();
        return view('member.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nim' => ['required', 'string', 'max:30'],
            'university' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('avatars', $filename, 'public');
            $validated['avatar_path'] = 'avatars/' . $filename;
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function regenerateToken()
    {
        $user = Auth::user();
        $user->update([
            'qr_token' => 'urn:ukm-badminton:token:' . Str::uuid(),
        ]);

        return back()->with('success', 'QR Badge baru berhasil dibuat. QR lama sudah tidak berlaku.');
    }
}
