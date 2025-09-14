<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@pkp.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Pejabat Penilai (Assessor)
        User::create([
            'name' => 'Ahmad Rahman',
            'email' => 'pejabat@pkp.test',
            'password' => Hash::make('password'),
            'role' => 'pejabat_penilai',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Atasan Pejabat Penilai (Supervisor)
        User::create([
            'name' => 'Dr. Siti Nurhaliza',
            'email' => 'atasan@pkp.test',
            'password' => Hash::make('password'),
            'role' => 'atasan_pejabat',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Pegawai (Employee)
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'pegawai@pkp.test',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create another Pegawai
        User::create([
            'name' => 'Maya Sari',
            'email' => 'pegawai2@pkp.test',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create inactive user for testing
        User::create([
            'name' => 'User Nonaktif',
            'email' => 'nonaktif@pkp.test',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'is_active' => false,
            'email_verified_at' => now(),
        ]);
    }
}
