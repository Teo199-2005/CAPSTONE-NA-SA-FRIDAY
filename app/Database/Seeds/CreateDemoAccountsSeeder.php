<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class CreateDemoAccountsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        
        // Demo Admin
        $this->createDemoAdmin($userModel, $db);
        
        // Demo Teacher
        $this->createDemoTeacher($userModel, $db);
        
        // Demo Student
        $this->createDemoStudent($userModel, $db);
        
        echo "Demo accounts created successfully!\n";
        echo "Admin: demo.admin@lphs.edu / DemoPass123!\n";
        echo "Teacher: PRC-2024-001 / DemoPass123!\n";
        echo "Student: 1234101702 / DemoPass123!\n";
    }
    
    private function createDemoAdmin($userModel, $db)
    {
        $email = 'demo.admin@lphs.edu';
        
        // Check if admin user exists
        $existingUser = $userModel->where('email', $email)->first();
        
        if (!$existingUser) {
            // Create admin user
            $userEntity = new User([
                'email' => $email,
                'password' => 'DemoPass123!',
                'active' => 1,
            ]);
            $userModel->save($userEntity);
            $userId = $userModel->getInsertID();
            
            // Add to admin group
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id' => $userId,
                'group' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            echo "Created demo admin user\n";
        } else {
            echo "Demo admin user already exists\n";
        }
    }
    
    private function createDemoTeacher($userModel, $db)
    {
        $email = 'demo.teacher@lphs.edu';
        $license = 'PRC-2024-001';
        
        // Check if teacher user exists
        $existingUser = $userModel->where('email', $email)->first();
        
        if (!$existingUser) {
            // Create teacher user
            $userEntity = new User([
                'email' => $email,
                'password' => 'DemoPass123!',
                'active' => 1,
            ]);
            $userModel->save($userEntity);
            $userId = $userModel->getInsertID();
            
            // Add to teacher group
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id' => $userId,
                'group' => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            // Create teacher record
            $db->table('teachers')->ignore(true)->insert([
                'user_id' => $userId,
                'teacher_id' => 'DEMO-T001',
                'license_number' => $license,
                'first_name' => 'Demo',
                'last_name' => 'Teacher',
                'gender' => 'Female',
                'date_of_birth' => '1985-01-01',
                'contact_number' => '09123456789',
                'address' => 'Demo Address',
                'department' => 'Mathematics',
                'position' => 'Teacher III',
                'specialization' => 'Mathematics',
                'date_hired' => '2020-01-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            echo "Created demo teacher user\n";
        } else {
            // Update existing teacher with license number
            $teacher = $db->table('teachers')->where('user_id', $existingUser->id)->get()->getRowArray();
            if ($teacher) {
                $db->table('teachers')->where('user_id', $existingUser->id)->update([
                    'license_number' => $license
                ]);
                echo "Updated existing teacher with license number\n";
            }
        }
    }
    
    private function createDemoStudent($userModel, $db)
    {
        $email = 'demo.student@lphs.edu';
        $lrn = '1234101702';
        
        // Check if student user exists
        $existingUser = $userModel->where('email', $email)->first();
        
        if (!$existingUser) {
            // Create student user
            $userEntity = new User([
                'email' => $email,
                'password' => 'DemoPass123!',
                'active' => 1,
            ]);
            $userModel->save($userEntity);
            $userId = $userModel->getInsertID();
            
            // Add to student group
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id' => $userId,
                'group' => 'student',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            // Create student record
            $db->table('students')->ignore(true)->insert([
                'user_id' => $userId,
                'lrn' => $lrn,
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'gender' => 'Male',
                'date_of_birth' => '2008-01-01',
                'email' => $email,
                'enrollment_status' => 'enrolled',
                'grade_level' => 6,
                'school_year' => '2024-2025',
                'address' => 'Demo Address',
                'contact_number' => '09123456789',
                'emergency_contact_name' => 'Demo Parent',
                'emergency_contact_number' => '09987654321',
                'emergency_contact_relationship' => 'Parent',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            echo "Created demo student user\n";
        } else {
            // Update existing student with LRN
            $student = $db->table('students')->where('user_id', $existingUser->id)->get()->getRowArray();
            if ($student) {
                $db->table('students')->where('user_id', $existingUser->id)->update([
                    'lrn' => $lrn
                ]);
                echo "Updated existing student with LRN\n";
            }
        }
    }
}