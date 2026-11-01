<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'member');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('university', 'like', "%{$search}%");
            });
        }

        $members = $query->latest()->paginate(20);
        $pendingCount = User::where('role', 'member')->where('status', 'pending')->count();

        return view('admin.members.index', compact('members', 'pendingCount'));
    }

    public function pending()
    {
        $pendingMembers = User::where('role', 'member')
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('admin.members.pending', compact('pendingMembers'));
    }

    public function approve($id)
    {
        $member = User::where('role', 'member')->findOrFail($id);
        $member->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve_member',
            'description' => "Menyetujui pendaftaran anggota: {$member->name} (NIM: {$member->nim})",
        ]);

        return back()->with('success', "Akun anggota {$member->name} berhasil disetujui.");
    }

    public function reject($id)
    {
        $member = User::where('role', 'member')->findOrFail($id);
        $member->update([
            'status' => 'rejected',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'reject_member',
            'description' => "Menolak pendaftaran anggota: {$member->name} (NIM: {$member->nim})",
        ]);

        return back()->with('success', "Akun anggota {$member->name} telah ditolak.");
    }
}
