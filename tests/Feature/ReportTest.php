<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportTest extends TestCase
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
            'name' => 'Jonatan Christie',
            'nim' => '1221888',
            'university' => 'Universitas Indonesia',
            'role' => 'member',
            'status' => 'approved',
        ]);

        $this->session = TrainingSession::create([
            'title' => 'Latihan Rutin Bulutangkis',
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '16:00',
            'end_time' => '19:00',
            'venue' => 'GOR Bulutangkis Lapangan 2',
            'status' => 'closed',
            'created_by' => $this->sekre->id,
        ]);

        Attendance::create([
            'training_session_id' => $this->session->id,
            'user_id' => $this->member->id,
            'method' => 'qr_scan',
            'recorded_by' => $this->sekre->id,
        ]);
    }

    public function test_secretary_can_access_report_index_and_tabs(): void
    {
        $response = $this->actingAs($this->sekre)->get('/admin/reports');
        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Kehadiran');
        $response->assertSee('Latihan Rutin Bulutangkis');

        $responseMemberTab = $this->actingAs($this->sekre)->get('/admin/reports?tab=member');
        $responseMemberTab->assertStatus(200);
        $responseMemberTab->assertSee('Jonatan Christie');
    }

    public function test_secretary_can_view_session_detail_report(): void
    {
        $response = $this->actingAs($this->sekre)->get("/admin/reports/session/{$this->session->id}");
        $response->assertStatus(200);
        $response->assertSee('Detail Rekap Presensi');
        $response->assertSee('Jonatan Christie');
        $response->assertSee('1221888');
    }

    public function test_secretary_can_export_session_attendance_to_csv(): void
    {
        $response = $this->actingAs($this->sekre)->get("/admin/reports/export/session/{$this->session->id}");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
    }

    public function test_secretary_can_export_members_recap_to_csv(): void
    {
        $response = $this->actingAs($this->sekre)->get('/admin/reports/export/members');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
    }

    public function test_regular_member_forbidden_from_reports(): void
    {
        $response = $this->actingAs($this->member)->get('/admin/reports');
        $response->assertStatus(403);

        $responseExport = $this->actingAs($this->member)->get("/admin/reports/export/session/{$this->session->id}");
        $responseExport->assertStatus(403);
    }
}
