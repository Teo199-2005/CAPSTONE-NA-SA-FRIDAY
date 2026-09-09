<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

class CreateCompleteStudentAccountSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $userModel = new UserModel();
        
        // Check if user already exists
        $existingUser = $userModel->where('email', 'mariasantos67@hotmail.com')->first();
        
        if ($existingUser) {
            echo "User already exists with ID: {$existingUser->id}\n";
            $userId = $existingUser->id;
        } else {
            // Create user account
            $userData = [
                'email' => 'mariasantos67@hotmail.com',
                'password' => 'student123',
                'active' => 1
            ];
            
            $userModel->save($userData);
            $userId = $userModel->getInsertID();
            
            // Add user to student group
            $user = $userModel->find($userId);
            $user->addGroup('student');
            echo "Created new user with ID: $userId\n";
        }
        
        $existingStudent = $db->table('students')
            ->where('first_name', 'Maria')
            ->where('last_name', 'Santos')
            ->where('grade_level', 6)
            ->get()
            ->getRowArray();

        if ($existingStudent) {
            $db->table('students')
                ->where('id', $existingStudent['id'])
                ->update([
                    'user_id'           => $userId,
                    'enrollment_status' => 'enrolled',
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);
            echo "Updated existing student record ID: {$existingStudent['id']}\n";
        } else {
            $section = $db->table('sections')
                ->where('grade_level', 6)
                ->where('is_active', true)
                ->get()
                ->getRowArray();

            if (! $section) {
                echo "No Grade 6 section found!\n";
                return;
            }
            
            // Generate LRN
            $lastStudent = $db->table('students')->select('lrn')->orderBy('lrn', 'DESC')->get()->getRowArray();
            $nextLrn = $lastStudent ? (string)(intval($lastStudent['lrn']) + 1) : '100000000001';
            
            // Create new student record
            $studentData = [
                'user_id' => $userId,
                'lrn' => $nextLrn,
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'gender' => 'Female',
                'date_of_birth' => '2008-03-15',
                'place_of_birth' => 'Panglao, Bohol',
                'nationality' => 'Filipino',
                'religion' => 'Catholic',
                'contact_number' => '09123456789',
                'address' => 'Barangay Lourdes, Panglao, Bohol',
                'emergency_contact_name' => 'Rosa Santos',
                'emergency_contact_number' => '09987654321',
                'emergency_contact_relationship' => 'Mother',
                'grade_level' => 6,
                'section_id' => $section['id'],
                'enrollment_status' => 'enrolled',
                'school_year' => '2025-2026',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('students')->insert($studentData);
            echo "Created new student record with LRN: $nextLrn\n";
        }
        
        echo "Complete student account setup finished!\n";
    }
}