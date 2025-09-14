<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@pkp.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Pejabat Penilai (Assessor)
        User::updateOrCreate(
            ['email' => 'pejabat@pkp.test'],
            [
                'name' => 'Ahmad Rahman',
                'password' => Hash::make('password'),
                'role' => 'pejabat_penilai',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Atasan Pejabat Penilai (Supervisor)
        User::updateOrCreate(
            ['email' => 'atasan@pkp.test'],
            [
                'name' => 'Dr. Siti Nurhaliza',
                'password' => Hash::make('password'),
                'role' => 'atasan_pejabat',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Regular Employee
        User::updateOrCreate(
            ['email' => 'pegawai@pkp.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create another Employee
        User::updateOrCreate(
            ['email' => 'pegawai2@pkp.test'],
            [
                'name' => 'Maya Sari',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Inactive User
        User::updateOrCreate(
            ['email' => 'inactive@pkp.test'],
            [
                'name' => 'User Nonaktif',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'is_active' => false,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Super Admin: superadmin@pkp.test / password');
        $this->command->info('Pejabat Penilai: pejabat@pkp.test / password');
        $this->command->info('Atasan Pejabat: atasan@pkp.test / password');
        $this->command->info('Pegawai: pegawai@pkp.test / password');
        $this->command->info('Pegawai 2: pegawai2@pkp.test / password');
        $this->command->info('Inactive: nonaktif@pkp.test / password');
    }
}
