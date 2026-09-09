<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

class FixTeacherEmails extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $userModel = model(UserModel::class);
        
        // Get all teachers
        $teachers = $db->table('teachers')->get()->getResultArray();
        
        foreach ($teachers as $teacher) {
            // Generate email
            $firstName = strtolower($teacher['first_name']);
            $lastName = strtolower($teacher['last_name']);
            $email = $firstName . '.' . $lastName . '@lphs.edu.ph';
            
            // Check if user exists with this user_id
            if ($teacher['user_id']) {
                $existingUser = $db->table('users')->where('id', $teacher['user_id'])->get()->getRowArray();
                if ($existingUser) {
                    // Update existing user's email
                    $db->table('users')
                        ->where('id', $teacher['user_id'])
                        ->update(['email' => $email]);
                    echo "Updated user ID {$teacher['user_id']} email to {$email}\n";
                } else {
                    echo "User ID {$teacher['user_id']} not found for {$teacher['first_name']} {$teacher['last_name']}\n";
                }
            } else {
                echo "No user_id for {$teacher['first_name']} {$teacher['last_name']}\n";
            }
        }
        
        echo "Email fix complete.\n";
    }
}