<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddAdminPasswordSeeder extends Seeder
{
    public function run(): void
    {
        helper('auth');

        $db    = \Config\Database::connect();
        $email = 'admin@lphs.edu';
        $admin = $db->table('users')->where('email', $email)->get()->getRow();

        if (! $admin) {
            echo "No admin user found ({$email}). Run AuthSeeder first.\n";

            return;
        }

        sync_auth_password((int) $admin->id, $email, 'ChangeMe123!');
        ensure_user_in_group((int) $admin->id, 'admin');

        echo "Admin password and group synced for user ID {$admin->id}\n";
    }
}
