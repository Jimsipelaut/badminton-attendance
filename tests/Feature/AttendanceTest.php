<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use DatabaseTransactions;

    protected $sekre;
    protected $member;
    protected $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sekre = User::where('role', 'secretary')->first();

        $this->member = User::factory()->create([
            'name' => 'Anthony Ginting',
            'nim' => '1221777',
            'university' => 'Universitas Indonesia',
            'role' => 'member',
            'status' => 'approved',
            'qr_token' => 'urn:ukm-badminton:token:ginting-uuid',
        ]);

        $this->session = TrainingSession::create([
            'title' => 'Latihan Rutin Aktif',
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '16:00',
            'end_time' => '19:00',
            'venue' => 'GOR Lapangan 1',
            'status' => 'active',
            'created_by' => $this->sekre->id,
        ]);
    }

    public function test_secretary_can_view_scanner_on_active_session(): void
    {
        $response = $this->actingAs($this->sekre)->get("/admin/sessions/{$this->session->id}/scanner");
        $response->assertStatus(200);
        $response->assertSee('Presensi Lapangan');
        $response->assertSee('Latihan Rutin Aktif');
    }

    public function test_scanner_redirects_if_session_is_not_active(): void
    {
        $this->session->update(['status' => 'closed']);

        $response = $this->actingAs($this->sekre)->get("/admin/sessions/{$this->session->id}/scanner");
        $response->assertRedirect('/admin/sessions');
        $response->assertSessionHas('error');
    }

    public function test_valid_qr_token_scan_records_attendance_and_returns_member_data(): void
    {
        $response = $this->actingAs($this->sekre)->postJson("/admin/sessions/{$this->session->id}/scan", [
            'qr_token' => $this->member->qr_token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'name' => 'Anthony Ginting',
                    'nim' => '1221777',
                    'university' => 'Universitas Indonesia',
                ],
            ]);

        $this->assertDatabaseHas('attendances', [
            'training_session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'method' => 'qr_scan',
        ]);
    }

    public function test_duplicate_qr_scan_is_prevented(): void
    {
        // First scan
        Attendance::create([
            'training_session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'method' => 'qr_scan',
            'recorded_by' => $this->sekre->id,
        ]);

        // Second scan attempt
        $response = $this->actingAs($this->sekre)->postJson("/admin/sessions/{$this->session->id}/scan", [
            'qr_token' => $this->member->qr_token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'warning',
            ]);

        // Ensure row count is still 1
        $this->assertEquals(1, Attendance::where('training_session_id', $this->session->id)->where('user_id', $this->member->id)->count());
    }

    public function test_invalid_qr_token_returns_404(): void
    {
        $response = $this->actingAs($this->sekre)->postJson("/admin/sessions/{$this->session->id}/scan", [
            'qr_token' => 'urn:ukm-badminton:token:random-invalid-token',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_pending_member_qr_token_is_rejected(): void
    {
        $pendingUser = User::factory()->create([
            'role' => 'member',
            'status' => 'pending',
            'qr_token' => 'urn:ukm-badminton:token:pending-token',
        ]);

        $response = $this->actingAs($this->sekre)->postJson("/admin/sessions/{$this->session->id}/scan", [
            'qr_token' => $pendingUser->qr_token,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_fallback_manual_attendance_records_correctly(): void
    {
        $otherMember = User::factory()->create([
            'name' => 'Fajar Alfian',
            'role' => 'member',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->sekre)->post("/admin/sessions/{$this->session->id}/manual", [
            'user_id' => $otherMember->id,
            'notes' => 'Baterai HP habis',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'training_session_id' => $this->session->id,
            'user_id' => $otherMember->id,
            'method' => 'manual',
            'notes' => 'Baterai HP habis',
        ]);
    }

    public function test_regular_member_cannot_scan_or_record_manual(): void
    {
        $response = $this->actingAs($this->member)->postJson("/admin/sessions/{$this->session->id}/scan", [
            'qr_token' => $this->member->qr_token,
        ]);
        $response->assertStatus(403);

        $responseManual = $this->actingAs($this->member)->post("/admin/sessions/{$this->session->id}/manual", [
            'user_id' => $this->member->id,
        ]);
        $responseManual->assertStatus(403);
    }
}
