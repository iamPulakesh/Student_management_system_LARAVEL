<?php

/**
 * Seeds the demo admin account (admin@sms.test / password).
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Create the administrator account, unless an administrator already exists.
     */
    public function run(): void
    {
        if (User::query()->where('role', User::ROLE_ADMIN)->exists()) {
            $this->command?->warn('An administrator already exists. Skipping admin seeding.');

            return;
        }

        User::createWithRole([
            'name' => 'System Administrator',
            'email' => 'admin@sms.test',
            'password' => 'password',
        ], User::ROLE_ADMIN);
    }
}
