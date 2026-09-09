<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateDemoPasswordsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get existing users
        $adminUser = $db->table('users')->where('email', 'admin@lphs.edu')->get()->getRow();
        $studentUser = $db->table('users')->where('email', 'student@lphs.edu')->get()->getRow();
        $teacherUser = $db->table('users')->where('email', 'demo.teacher@lphs.edu')->get()->getRow();
        
        // Add/update admin password
        if ($adminUser) {
            $existing = $db->table('auth_identities')->where('user_id', $adminUser->id)->get()->getRow();
            if (!$existing) {
                $db->table('auth_identities')->insert([
                    'user_id' => $adminUser->id,
                    'type' => 'email_password',
                    'name' => 'admin@lphs.edu',
                    'secret' => password_hash('admin123', PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        // Create student if doesn't exist
        if (!$studentUser) {
            $db->table('users')->insert([
                'username' => 'student',
                'email' => 'student@lphs.edu',
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $studentId = $db->insertID();
            
            $db->table('auth_identities')->insert([
                'user_id' => $studentId,
                'type' => 'email_password',
                'name' => 'student@lphs.edu',
                'secret' => password_hash('student123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $db->table('students')->insert([
                'user_id' => $studentId,
                'lrn' => 'DEMO-STUDENT-001',
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'grade_level' => 6,
                'section_id' => 1,
                'enrollment_status' => 'enrolled',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Add teacher password
        if ($teacherUser) {
            $existing = $db->table('auth_identities')->where('user_id', $teacherUser->id)->get()->getRow();
            if (!$existing) {
                $db->table('auth_identities')->insert([
                    'user_id' => $teacherUser->id,
                    'type' => 'email_password',
                    'name' => 'demo.teacher@lphs.edu',
                    'secret' => password_hash('DemoPass123!', PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}