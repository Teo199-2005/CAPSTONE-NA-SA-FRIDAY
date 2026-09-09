<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DirectDemoAccountsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Create admin user
        $adminData = [
            'username' => 'admin',
            'email' => 'admin@lphs.edu',
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $db->table('users')->insert($adminData);
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
        
        // Create student user
        $studentData = [
            'username' => 'student', 
            'email' => 'student@lphs.edu',
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $db->table('users')->insert($studentData);
        $studentId = $db->insertID();
        
        // Add student password
        $db->table('auth_identities')->insert([
            'user_id' => $studentId,
            'type' => 'email_password', 
            'name' => 'student@lphs.edu',
            'secret' => password_hash('student123', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Add student record
        $db->table('students')->insert([
            'user_id' => $studentId,
            'lrn' => 'DEMO-STUDENT-001',
            'first_name' => 'Demo',
            'last_name' => 'Student', 
            'grade_level' => 6,
            'section' => 'Aristotle',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Fix teacher password
        $teacherUser = $db->table('users')->where('email', 'demo.teacher@lphs.edu')->get()->getRow();
        if ($teacherUser) {
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