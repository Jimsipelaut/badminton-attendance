<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Akun Utama
        $admin = User::firstOrCreate(
            ['email' => 'admin@ukmbadminton.org'],
            [
                'name' => 'Admin UKM Bulutangkis',
                'password' => bcrypt('password'),
                'nim' => 'ADM-001',
                'university' => 'Universitas Indonesia',
                'phone_number' => '081234567890',
                'qr_token' => 'urn:ukm-badminton:token:admin-super-token-001',
                'role' => 'admin',
                'status' => 'approved',
                'approved_at' => now(),
            ]
        );

        // 2. Sekretaris Lapangan
        $sekre = User::firstOrCreate(
            ['email' => 'sekre@ukmbadminton.org'],
            [
                'name' => 'Sekretaris Lapangan',
                'password' => bcrypt('password'),
                'nim' => 'SEK-001',
                'university' => 'Universitas Indonesia',
                'phone_number' => '081234567891',
                'qr_token' => 'urn:ukm-badminton:token:sekre-token-002',
                'role' => 'secretary',
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $admin->id,
            ]
        );

        // 3. Anggota Percontohan
        User::firstOrCreate(
            ['email' => 'member@ukmbadminton.org'],
            [
                'name' => 'Budi Santoso',
                'password' => bcrypt('password'),
                'nim' => '1221045',
                'university' => 'Universitas Indonesia',
                'phone_number' => '081234567892',
                'qr_token' => 'urn:ukm-badminton:token:member-budi-003',
                'role' => 'member',
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $sekre->id,
            ]
        );
    }
}
