<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EndToEndSimulationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_full_ukm_badminton_attendance_lifecycle(): void
    {
        // 1. Sekretaris login
        $sekre = User::where('role', 'secretary')->first();
        $this->assertNotNull($sekre);

        // 2. Anggota baru mendaftar
        $responseReg = $this->post('/register', [
            'name' => 'Taufik Hidayat',
            'email' => 'taufik@badminton.org',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'nim' => '1221111',
            'university' => 'Universitas Indonesia',
            'phone_number' => '081233334444',
        ]);
        $responseReg->assertRedirect('/login');

        $member = User::where('email', 'taufik@badminton.org')->first();
        $this->assertEquals('pending', $member->status);
        $this->assertNotNull($member->qr_token);

        // 3. Sekretaris menyetujui akun anggota baru
        $responseApprove = $this->actingAs($sekre)->post("/admin/members/{$member->id}/approve");
        $responseApprove->assertSessionHas('success');
        $this->assertEquals('approved', $member->fresh()->status);

        // 4. Sekretaris menjadwalkan dan membuka sesi latihan baru
        $responseSession = $this->actingAs($sekre)->post('/admin/sessions', [
            'title' => 'Latihan Gabungan Nasional',
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '15:00',
            'end_time' => '18:00',
            'venue' => 'GOR Bulutangkis Utama',
        ]);
        $responseSession->assertRedirect('/admin/sessions');

        $session = TrainingSession::where('title', 'Latihan Gabungan Nasional')->first();
        $this->assertEquals('scheduled', $session->status);

        // Aktifkan sesi
        $responseActivate = $this->actingAs($sekre)->post("/admin/sessions/{$session->id}/activate");
        $responseActivate->assertRedirect('/admin/sessions');
        $this->assertEquals('active', $session->fresh()->status);

        // 5. Anggota login, melihat badge QR personal mereka (setelah di-approve middleware lolos)
        $member->update(['status' => 'approved']);

        $responseMemberLogin = $this->actingAs($member)->get('/dashboard');
        $responseMemberLogin->assertStatus(200);
        $responseMemberLogin->assertSee('Taufik Hidayat');

        $responseBadge = $this->actingAs($member)->get('/my-badge');
        $responseBadge->assertStatus(200);
        $responseBadge->assertSee($member->qr_token);

        // 6. Sekretaris melakukan scan QR Badge anggota di lapangan
        $responseScan = $this->actingAs($sekre)->postJson("/admin/sessions/{$session->id}/scan", [
            'qr_token' => $member->qr_token,
        ]);
        $responseScan->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'name' => 'Taufik Hidayat',
                    'nim' => '1221111',
                ],
            ]);

        // Verifikasi kehadiran tercatat di database
        $this->assertDatabaseHas('attendances', [
            'training_session_id' => $session->id,
            'user_id' => $member->id,
            'method' => 'qr_scan',
        ]);

        // 7. Sekretaris menutup sesi latihan
        $responseClose = $this->actingAs($sekre)->post("/admin/sessions/{$session->id}/close");
        $responseClose->assertRedirect('/admin/sessions');
        $this->assertEquals('closed', $session->fresh()->status);

        // 8. Pengurus mengakses laporan rekapitulasi dan unduh CSV
        $responseReport = $this->actingAs($sekre)->get('/admin/reports');
        $responseReport->assertStatus(200);
        $responseReport->assertSee('Latihan Gabungan Nasional');

        $responseExport = $this->actingAs($sekre)->get("/admin/reports/export/session/{$session->id}");
        $responseExport->assertStatus(200);
        $responseExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
