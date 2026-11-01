<?php

namespace Tests\Feature;

use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TrainingSessionTest extends TestCase
{
    use DatabaseTransactions;
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_secretary_can_create_new_training_session(): void
    {
        $sekre = User::where('role', 'secretary')->first();

        $response = $this->actingAs($sekre)->post('/admin/sessions', [
            'title' => 'Latihan Rutin Sabtu',
            'session_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '16:00',
            'end_time' => '19:00',
            'venue' => 'GOR Bulutangkis Kampus A',
            'notes' => 'Harap membawa raket masing-masing',
        ]);

        $response->assertRedirect('/admin/sessions');

        $session = TrainingSession::where('title', 'Latihan Rutin Sabtu')->first();
        $this->assertNotNull($session);
        $this->assertEquals('scheduled', $session->status);
        $this->assertEquals($sekre->id, $session->created_by);
    }

    public function test_secretary_can_activate_session_and_previous_active_session_is_closed(): void
    {
        $sekre = User::where('role', 'secretary')->first();

        // Buat sesi 1 dan aktifkan
        $session1 = TrainingSession::create([
            'title' => 'Sesi 1',
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '11:00',
            'venue' => 'GOR',
            'status' => 'active',
            'created_by' => $sekre->id,
        ]);

        // Buat sesi 2
        $session2 = TrainingSession::create([
            'title' => 'Sesi 2',
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '15:00',
            'end_time' => '18:00',
            'venue' => 'GOR',
            'status' => 'scheduled',
            'created_by' => $sekre->id,
        ]);

        // Aktifkan sesi 2
        $response = $this->actingAs($sekre)->post("/admin/sessions/{$session2->id}/activate");
        $response->assertRedirect('/admin/sessions');

        // Pastikan Sesi 1 otomatis closed dan Sesi 2 active
        $this->assertEquals('closed', $session1->fresh()->status);
        $this->assertEquals('active', $session2->fresh()->status);
    }

    public function test_secretary_can_close_active_session(): void
    {
        $sekre = User::where('role', 'secretary')->first();
        $session = TrainingSession::create([
            'title' => 'Sesi Siap Tutup',
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'venue' => 'GOR',
            'status' => 'active',
            'created_by' => $sekre->id,
        ]);

        $response = $this->actingAs($sekre)->post("/admin/sessions/{$session->id}/close");
        $response->assertRedirect('/admin/sessions');

        $this->assertEquals('closed', $session->fresh()->status);
    }

    public function test_regular_member_forbidden_from_session_management(): void
    {
        $member = User::where('role', 'member')->where('status', 'approved')->first();

        $response = $this->actingAs($member)->get('/admin/sessions');
        $response->assertStatus(403);

        $responsePost = $this->actingAs($member)->post('/admin/sessions', [
            'title' => 'Sesi Ilegal',
            'session_date' => now()->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'venue' => 'GOR',
        ]);
        $responsePost->assertStatus(403);
    }
}
