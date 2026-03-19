<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates (or updates) the default admin user.
     *
     * Login credentials:
     *   Email:    admin@example.com
     *   Password: Admin@123
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('Admin@123'),
                'is_admin'          => true,
                'is_active'         => true,
                'role'              => 'admin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user created/updated.');
        $this->command->info('   Email:    admin@example.com');
        $this->command->info('   Password: Admin@123');
        $this->command->warn('   ⚠  Change the password after first login!');
    }
}
