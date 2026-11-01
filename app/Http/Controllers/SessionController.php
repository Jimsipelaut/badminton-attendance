<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingSession::withCount('attendances')->with('creator');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->orderByRaw("FIELD(status, 'active', 'scheduled', 'closed', 'cancelled')")
            ->latest('session_date')
            ->paginate(15);

        $activeSession = TrainingSession::where('status', 'active')->first();

        return view('admin.sessions.index', compact('sessions', 'activeSession'));
    }

    public function create()
    {
        return view('admin.sessions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'session_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'venue' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'scheduled';

        $session = TrainingSession::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_session',
            'description' => "Membuat jadwal latihan: {$session->title} pada tanggal {$session->session_date->format('d/m/Y')}",
        ]);

        return redirect()->route('admin.sessions.index')->with('success', 'Sesi latihan berhasil dijadwalkan.');
    }

    public function activate($id)
    {
        $session = TrainingSession::findOrFail($id);

        if ($session->status === 'closed') {
            return back()->with('error', 'Sesi yang sudah ditutup tidak dapat dibuka kembali.');
        }

        // Pastikan hanya satu sesi aktif dalam satu waktu (FR-SESI-02)
        TrainingSession::where('status', 'active')->where('id', '!=', $session->id)->update([
            'status' => 'closed',
        ]);

        $session->update([
            'status' => 'active',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'activate_session',
            'description' => "Membuka sesi latihan aktif: {$session->title}",
        ]);

        return redirect()->route('admin.sessions.index')->with('success', "Sesi {$session->title} sekarang AKTIF dan siap menerima presensi.");
    }

    public function close($id)
    {
        $session = TrainingSession::findOrFail($id);
        $session->update([
            'status' => 'closed',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'close_session',
            'description' => "Menutup sesi latihan: {$session->title}",
        ]);

        return redirect()->route('admin.sessions.index')->with('success', "Sesi {$session->title} berhasil ditutup. Presensi dikunci.");
    }

    public function cancel($id)
    {
        $session = TrainingSession::findOrFail($id);
        $session->update([
            'status' => 'cancelled',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'cancel_session',
            'description' => "Membatalkan sesi latihan: {$session->title}",
        ]);

        return redirect()->route('admin.sessions.index')->with('success', "Sesi {$session->title} telah dibatalkan.");
    }
}
