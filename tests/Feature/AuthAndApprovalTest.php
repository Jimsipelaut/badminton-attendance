<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthAndApprovalTest extends TestCase
{
    use DatabaseTransactions;
    public function test_guest_can_register_and_gets_pending_status_with_qr_token(): void
    {
        $response = $this->post('/register', [
            'name' => 'Kevin Sanjaya',
            'email' => 'kevin@test.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'nim' => '1221999',
            'university' => 'Universitas Indonesia',
            'phone_number' => '081299998888',
        ]);

        $response->assertRedirect('/login');

        $user = User::where('email', 'kevin@test.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('pending', $user->status);
        $this->assertEquals('member', $user->role);
        $this->assertStringStartsWith('urn:ukm-badminton:token:', $user->qr_token);
    }

    public function test_pending_member_cannot_login_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'pending@test.com',
            'password' => bcrypt('secret123'),
            'role' => 'member',
            'status' => 'pending',
            'nim' => '1221001',
            'university' => 'UI',
            'qr_token' => 'urn:ukm-badminton:token:test-pending',
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@test.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_secretary_can_approve_pending_member(): void
    {
        $sekre = User::where('role', 'secretary')->first();
        $pendingMember = User::factory()->create([
            'status' => 'pending',
            'role' => 'member',
        ]);

        $response = $this->actingAs($sekre)->post("/admin/members/{$pendingMember->id}/approve");

        $response->assertSessionHas('success');
        $this->assertEquals('approved', $pendingMember->fresh()->status);
        $this->assertEquals($sekre->id, $pendingMember->fresh()->approved_by);
    }

    public function test_approved_member_can_login_and_access_dashboard_and_badge(): void
    {
        $member = User::factory()->create([
            'name' => 'Approved Member User',
            'status' => 'approved',
            'role' => 'member',
        ]);

        $response = $this->actingAs($member)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Approved Member User');

        $badgeResponse = $this->actingAs($member)->get('/my-badge');
        $badgeResponse->assertStatus(200);
        $badgeResponse->assertSee($member->qr_token);
    }
}
