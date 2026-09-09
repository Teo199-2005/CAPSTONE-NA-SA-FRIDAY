<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

class AddTeacherUserAccounts extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $userModel = model(UserModel::class);
        
        // Get all teachers without user accounts
        $teachers = $db->table('teachers')
            ->groupStart()
            ->where('user_id IS NULL')
            ->orWhere('user_id', 0)
            ->orWhere('user_id', '')
            ->groupEnd()
            ->get()
            ->getResultArray();
        
        if (empty($teachers)) {
            echo "No teachers found without user accounts.\n";
            return;
        }
        
        foreach ($teachers as $teacher) {
            // Generate email based on name
            $firstName = strtolower($teacher['first_name']);
            $lastName = strtolower($teacher['last_name']);
            $email = $firstName . '.' . $lastName . '@lphs.edu.ph';
            
            // Create user account using Shield UserModel
            $userData = [
                'email' => $email,
                'password' => 'password123',
                'active' => 1
            ];
            
            $user = $userModel->save($userData);
            if (!$user) {
                echo "Failed to create user for {$teacher['first_name']} {$teacher['last_name']}\n";
                continue;
            }
            
            $userId = $userModel->getInsertID();
            
            // Add user to teacher group
            $userEntity = $userModel->find($userId);
            $userEntity->addGroup('teacher');
            
            // Update teacher record with user_id
            $db->table('teachers')
                ->where('id', $teacher['id'])
                ->update(['user_id' => $userId]);
            
            echo "Created user account for {$teacher['first_name']} {$teacher['last_name']} - {$email}\n";
        }
        
        echo "Successfully created user accounts for " . count($teachers) . " teachers.\n";
    }
}