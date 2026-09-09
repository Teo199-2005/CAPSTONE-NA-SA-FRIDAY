<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class FixDemoAccountsSeeder extends Seeder
{
    public function run()
    {
        $users = auth()->getProvider();
        
        // Create admin demo account
        $adminUser = new User([
            'username' => 'admin',
            'email'    => 'admin@lphs.edu',
            'password' => 'admin123',
        ]);
        $users->save($adminUser);
        $adminUser->activate();
        $adminUser->addToGroup('admin');
        
        // Create student demo account  
        $studentUser = new User([
            'username' => 'student',
            'email'    => 'student@lphs.edu', 
            'password' => 'student123',
        ]);
        $users->save($studentUser);
        $studentUser->activate();
        $studentUser->addToGroup('student');
        
        // Add student record
        $db = \Config\Database::connect();
        $db->table('students')->insert([
            'user_id' => $studentUser->id,
            'lrn' => 'DEMO-STUDENT-001',
            'first_name' => 'Demo',
            'last_name' => 'Student',
            'grade_level' => 6,
            'section' => 'Aristotle',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Fix teacher demo password
        $teacherUser = $users->findByCredentials(['email' => 'demo.teacher@lphs.edu']);
        if ($teacherUser) {
            $teacherUser->password = 'DemoPass123!';
            $users->save($teacherUser);
        }
    }
}