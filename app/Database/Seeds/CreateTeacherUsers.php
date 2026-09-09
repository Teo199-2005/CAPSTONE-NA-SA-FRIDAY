<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

class CreateTeacherUsers extends Seeder
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
            
            // Check if user already exists
            $existingUser = $db->table('users')->where('email', $email)->get()->getRowArray();
            
            if (!$existingUser) {
                // Create user account
                $userData = [
                    'email' => $email,
                    'password' => 'password123',
                    'active' => 1
                ];
                
                $user = $userModel->save($userData);
                if ($user) {
                    $userId = $userModel->getInsertID();
                    
                    // Add user to teacher group
                    $userEntity = $userModel->find($userId);
                    $userEntity->addGroup('teacher');
                    
                    // Update teacher record with user_id
                    $db->table('teachers')
                        ->where('id', $teacher['id'])
                        ->update(['user_id' => $userId]);
                    
                    echo "Created user account for {$teacher['first_name']} {$teacher['last_name']} - {$email}\n";
                } else {
                    echo "Failed to create user for {$teacher['first_name']} {$teacher['last_name']}\n";
                }
            } else {
                // Link existing user to teacher
                $db->table('teachers')
                    ->where('id', $teacher['id'])
                    ->update(['user_id' => $existingUser['id']]);
                
                echo "Linked existing user {$email} to {$teacher['first_name']} {$teacher['last_name']}\n";
            }
        }
        
        echo "Teacher user creation complete.\n";
    }
}