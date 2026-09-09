<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        helper('auth');

        $users    = model(UserModel::class);
        $email    = 'admin@lphs.edu';
        $password = 'ChangeMe123!';

        $user = $users->where('email', $email)->first();

        if (! $user) {
            $user = new User([
                'username' => 'admin',
                'email'    => $email,
                'password' => $password,
                'active'   => 1,
            ]);
            $users->save($user);
            $userId = (int) $users->getInsertID();
        } else {
            $userId = (int) $user->id;
            $user->active = 1;
            $users->save($user);
        }

        sync_auth_password($userId, $email, $password);
        ensure_user_in_group($userId, 'admin');

        echo "Master admin ready: {$email} / {$password}\n";
    }
}
