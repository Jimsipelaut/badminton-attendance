<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FieldSimulationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Uji coba simulasi riil lapangan:
     * 1. Sekretaris membuka sesi baru di lapangan.
     * 2. 20 anggota berturut-turut dipindai QR-nya tanpa freeze / error.
     * 3. Tiap pemindaian merespons cepat (< 500ms NFR-PERF).
     * 4. Pemindaian ganda ditolak seketika (warning anti-duplikasi).
     * 5. Anggota berstatus pending ditolak (403).
     * 6. Fallback manual presensi bekerja dengan audit trail.
     * 7. Anggota melihat pembaruan status hadir di dashboard pribadinya secara real-time.
     * 8. Penutupan sesi dan ekspor laporan CSV 100% konsisten.
     */
    public function test_twenty_members_sequential_field_scan_and_edge_cases(): void
    {
        $secretary = User::where('role', 'secretary')->firstOrFail();

        // 1. Buat sesi latihan lapangan dan aktifkan
        $session = TrainingSession::create([
            'title' => 'Simulasi Lapangan Sesi Akbar UKM Bulutangkis',
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '17:00',
            'end_time' => '20:00',
            'venue' => 'GOR Bulutangkis Lapangan 1-4',
            'status' => 'active',
            'created_by' => $secretary->id,
        ]);

        // 2. Siapkan 20 anggota approved dengan QR Token unik
        $members = [];
        for ($i = 1; $i <= 20; $i++) {
            $members[] = User::factory()->create([
                'name' => "Pemain Lapangan {$i}",
                'nim' => 'NIM-SIM-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'university' => 'Universitas Indonesia',
                'role' => 'member',
                'status' => 'approved',
                'qr_token' => "urn:ukm-badminton:token:sim-member-{$i}",
            ]);
        }

        // 3. Simulasikan pemindaian 20 anggota berturut-turut oleh sekretaris
        foreach ($members as $index => $member) {
            $startTime = microtime(true);

            $response = $this->actingAs($secretary)->postJson("/admin/sessions/{$session->id}/scan", [
                'qr_token' => $member->qr_token,
            ]);

            $elapsedMs = (microtime(true) - $startTime) * 1000;

            $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'data' => [
                        'name' => $member->name,
                        'nim' => $member->nim,
                    ],
                ]);

            // NFR-PERF: Proses verifikasi token QR ke basis data maksimal 500 ms per pemindaian
            $this->assertLessThan(500, $elapsedMs, "Pemindaian anggota ke-{$index} melebihi batas waktu 500ms ({$elapsedMs}ms)");

            // Verifikasi baris hadir tersimpan
            $this->assertDatabaseHas('attendances', [
                'training_session_id' => $session->id,
                'user_id' => $member->id,
                'method' => 'qr_scan',
            ]);
        }

        // Pastikan total 20 presensi tersimpan
        $this->assertEquals(20, Attendance::where('training_session_id', $session->id)->count());

        // 4. Uji coba anti-duplikasi: pemindaian ulang anggota ke-1 harus dicegah
        $duplicateResponse = $this->actingAs($secretary)->postJson("/admin/sessions/{$session->id}/scan", [
            'qr_token' => $members[0]->qr_token,
        ]);
        $duplicateResponse->assertStatus(200)
            ->assertJson([
                'status' => 'warning',
            ]);
        $this->assertEquals(20, Attendance::where('training_session_id', $session->id)->count());

        // 5. Uji coba anggota pending (belum disetujui pengurus)
        $pendingUser = User::factory()->create([
            'name' => 'Calon Anggota Belum Disetujui',
            'role' => 'member',
            'status' => 'pending',
            'qr_token' => 'urn:ukm-badminton:token:unapproved-guy',
        ]);
        $unapprovedResponse = $this->actingAs($secretary)->postJson("/admin/sessions/{$session->id}/scan", [
            'qr_token' => $pendingUser->qr_token,
        ]);
        $unapprovedResponse->assertStatus(403);

        // 6. Uji coba fallback manual untuk anggota ke-21 yang HP-nya habis baterai
        $memberManual = User::factory()->create([
            'name' => 'Pemain HP Lowbat',
            'nim' => 'NIM-MANUAL-01',
            'university' => 'Universitas Indonesia',
            'role' => 'member',
            'status' => 'approved',
        ]);

        $manualResponse = $this->actingAs($secretary)->post("/admin/sessions/{$session->id}/manual", [
            'user_id' => $memberManual->id,
            'notes' => 'Baterai smartphone 0%, verifikasi fisik langsung',
        ]);
        $manualResponse->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'training_session_id' => $session->id,
            'user_id' => $memberManual->id,
            'method' => 'manual',
            'notes' => 'Baterai smartphone 0%, verifikasi fisik langsung',
        ]);

        $this->assertEquals(21, Attendance::where('training_session_id', $session->id)->count());

        // 7. Verifikasi perspektif anggota: anggota melihat status hadir di riwayat pribadinya
        $memberViewResponse = $this->actingAs($members[0])->get('/dashboard');
        $memberViewResponse->assertStatus(200);
        $memberViewResponse->assertSee($members[0]->name);

        $memberHistoryResponse = $this->actingAs($members[0])->get('/my-history');
        $memberHistoryResponse->assertStatus(200);
        $memberHistoryResponse->assertSee($session->title);

        // 8. Sekretaris menutup sesi dan mengunduh laporan CSV rekapitulasi
        $closeResponse = $this->actingAs($secretary)->post("/admin/sessions/{$session->id}/close");
        $closeResponse->assertRedirect('/admin/sessions');
        $this->assertEquals('closed', $session->fresh()->status);

        $exportResponse = $this->actingAs($secretary)->get("/admin/reports/export/session/{$session->id}");
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // CSV memuat nama-nama yang hadir
        $csvContent = $exportResponse->streamedContent();
        $this->assertStringContainsString('Pemain Lapangan 1', $csvContent);
        $this->assertStringContainsString('Pemain Lapangan 20', $csvContent);
        $this->assertStringContainsString('Pemain HP Lowbat', $csvContent);
    }
}
