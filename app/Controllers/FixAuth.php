<?php
namespace App\Controllers;

use App\Controllers\BaseController;

class FixAuth extends BaseController
{
    public function createSofiaAuth()
    {
        $db = \Config\Database::connect();
        
        // Hash the password
        $password = 'Demo9005!';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if auth_identity already exists
        $existing = $db->table('auth_identities')
            ->where('user_id', 128)
            ->where('type', 'email_password')
            ->get()
            ->getRowArray();
        
        if ($existing) {
            // Update existing record
            $updated = $db->table('auth_identities')
                ->where('user_id', 128)
                ->where('type', 'email_password')
                ->update([
                    'secret' => 'sofia.aguilar@lphs.edu',
                    'secret2' => $hashedPassword,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            if ($updated) {
                return "Auth identity updated successfully for Sofia Aguilar<br>Email: sofia.aguilar@lphs.edu<br>Password: Demo9005!";
            } else {
                return "Failed to update auth identity";
            }
        } else {
            // Insert new auth_identity record
            $inserted = $db->table('auth_identities')->insert([
                'user_id' => 128,
                'type' => 'email_password',
                'name' => '',
                'secret' => 'sofia.aguilar@lphs.edu',
                'secret2' => $hashedPassword,
                'expires' => null,
                'extra' => null,
                'force_reset' => 0,
                'last_used_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($inserted) {
                return "Auth identity created successfully for Sofia Aguilar<br>Email: sofia.aguilar@lphs.edu<br>Password: Demo9005!";
            } else {
                return "Failed to create auth identity";
            }
        }
    }
}