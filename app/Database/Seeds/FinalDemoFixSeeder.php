<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinalDemoFixSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Check if admin exists
        $admin = $db->table('users')->where('email', 'admin@lphs.edu')->get()->getRow();
        if (!$admin) {
            // Create admin user
            $db->table('users')->insert([
                'username' => 'admin',
                'email' => 'admin@lphs.edu',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $adminId = $db->insertID();
            
            // Add admin password
            $db->table('auth_identities')->insert([
                'user_id' => $adminId,
                'type' => 'email_password',
                'name' => 'admin@lphs.edu',
                'secret' => password_hash('admin123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Check if admin has password
            $identity = $db->table('auth_identities')->where('user_id', $admin->id)->get()->getRow();
            if (!$identity) {
                $db->table('auth_identities')->insert([
                    'user_id' => $admin->id,
                    'type' => 'email_password',
                    'name' => 'admin@lphs.edu',
                    'secret' => password_hash('admin123', PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}