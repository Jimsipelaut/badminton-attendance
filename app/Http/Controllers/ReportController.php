<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Halaman Utama Rekapitulasi Presensi (Per Sesi & Per Anggota)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'session');

        // Total sesi selesai untuk kalkulasi persentase
        $totalClosedSessions = TrainingSession::whereIn('status', ['closed', 'active'])->count();

        // 1. Data Rekap Per Sesi
        $sessionQuery = TrainingSession::withCount('attendances')->with('creator');
        if ($request->filled('start_date')) {
            $sessionQuery->whereDate('session_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $sessionQuery->whereDate('session_date', '<=', $request->end_date);
        }
        $sessions = $sessionQuery->latest('session_date')->paginate(10, ['*'], 'sessions_page');

        // 2. Data Rekap Per Anggota
        $memberQuery = User::where('role', 'member')
            ->where('status', 'approved')
            ->withCount(['attendances' => function ($query) use ($request) {
                if ($request->filled('start_date') || $request->filled('end_date')) {
                    $query->whereHas('trainingSession', function ($q) use ($request) {
                        if ($request->filled('start_date')) {
                            $q->whereDate('session_date', '>=', $request->start_date);
                        }
                        if ($request->filled('end_date')) {
                            $q->whereDate('session_date', '<=', $request->end_date);
                        }
                    });
                }
            }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $memberQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('university', 'like', "%{$search}%");
            });
        }

        $members = $memberQuery->orderByDesc('attendances_count')->paginate(15, ['*'], 'members_page');

        return view('admin.reports.index', compact('sessions', 'members', 'totalClosedSessions', 'tab'));
    }

    /**
     * Detail Rekapitulasi Satu Sesi Latihan
     */
    public function sessionDetail($id)
    {
        $session = TrainingSession::with(['attendances.user', 'creator'])->findOrFail($id);

        return view('admin.reports.session-detail', compact('session'));
    }

    /**
     * Ekspor Data Presensi Sesi ke CSV (Standar Excel UTF-8)
     */
    public function exportSession($id): StreamedResponse
    {
        $session = TrainingSession::with(['attendances.user'])->findOrFail($id);
        $filename = 'rekap_presensi_' . str_replace(' ', '_', strtolower($session->title)) . '_' . $session->session_date->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($session) {
            $handle = fopen('php://output', 'w');
            // Tulis UTF-8 BOM agar Excel menampilkan aksen karakter dengan benar
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header informasi sesi
            fputcsv($handle, ['REKAPITULASI PRESENSI UKM BULUTANGKIS']);
            fputcsv($handle, ['Sesi Latihan', $session->title]);
            fputcsv($handle, ['Tanggal', $session->session_date->format('d/m/Y')]);
            fputcsv($handle, ['Waktu', substr($session->start_time, 0, 5) . ' - ' . substr($session->end_time, 0, 5) . ' WIB']);
            fputcsv($handle, ['Lokasi', $session->venue]);
            fputcsv($handle, ['Total Hadir', $session->attendances->count()]);
            fputcsv($handle, []); // Baris kosong

            // Header Kolom Tabel
            fputcsv($handle, ['No', 'Nama Lengkap', 'NIM', 'Asal Kampus / Universitas', 'Nomor HP', 'Waktu Presensi', 'Metode', 'Catatan']);

            $no = 1;
            foreach ($session->attendances->sortBy('created_at') as $att) {
                fputcsv($handle, [
                    $no++,
                    $att->user->name ?? '-',
                    $att->user->nim ?? '-',
                    $att->user->university ?? '-',
                    $att->user->phone_number ?? '-',
                    $att->created_at->format('H:i:s') . ' WIB',
                    $att->method === 'qr_scan' ? 'QR Scan' : 'Manual',
                    $att->notes ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Ekspor Data Rekapitulasi Seluruh Anggota ke CSV
     */
    public function exportMembers(Request $request): StreamedResponse
    {
        $filename = 'rekap_kehadiran_anggota_' . date('Ymd_His') . '.csv';

        $totalClosedSessions = TrainingSession::whereIn('status', ['closed', 'active'])->count();

        $members = User::where('role', 'member')
            ->where('status', 'approved')
            ->withCount('attendances')
            ->orderByDesc('attendances_count')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($members, $totalClosedSessions) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['REKAPITULASI KEHADIRAN ANGGOTA UKM BULUTANGKIS']);
            fputcsv($handle, ['Total Sesi Berjalan', $totalClosedSessions]);
            fputcsv($handle, ['Tanggal Ekspor', date('d/m/Y H:i') . ' WIB']);
            fputcsv($handle, []);

            fputcsv($handle, ['No', 'Nama Anggota', 'NIM', 'Asal Kampus / Universitas', 'Nomor HP', 'Total Hadir', 'Total Sesi', 'Persentase Kehadiran (%)']);

            $no = 1;
            foreach ($members as $m) {
                $percentage = $totalClosedSessions > 0
                    ? round(($m->attendances_count / $totalClosedSessions) * 100, 1)
                    : 0;

                fputcsv($handle, [
                    $no++,
                    $m->name,
                    $m->nim,
                    $m->university,
                    $m->phone_number ?? '-',
                    $m->attendances_count,
                    $totalClosedSessions,
                    $percentage . '%',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
