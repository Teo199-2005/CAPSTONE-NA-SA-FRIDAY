<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LinkTeachersToUsers extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get all teachers
        $teachers = $db->table('teachers')->get()->getResultArray();
        
        foreach ($teachers as $teacher) {
            // Generate expected email
            $firstName = strtolower($teacher['first_name']);
            $lastName = strtolower($teacher['last_name']);
            $email = $firstName . '.' . $lastName . '@lphs.edu.ph';
            
            // Find user with this email
            $user = $db->table('users')->where('email', $email)->get()->getRowArray();
            
            if ($user) {
                // Link teacher to user
                $db->table('teachers')
                    ->where('id', $teacher['id'])
                    ->update(['user_id' => $user['id']]);
                
                echo "Linked {$teacher['first_name']} {$teacher['last_name']} to user {$email}\n";
            } else {
                echo "No user found for {$teacher['first_name']} {$teacher['last_name']} ({$email})\n";
            }
        }
        
        echo "Teacher-user linking complete.\n";
    }
}