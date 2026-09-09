<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixAdminGroupSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get admin user
        $admin = $db->table('users')->where('email', 'admin@lphs.edu')->get()->getRow();
        if ($admin) {
            // Check if admin group assignment exists
            $groupAssignment = $db->table('auth_groups_users')->where('user_id', $admin->id)->get()->getRow();
            if (!$groupAssignment) {
                // Add admin to admin group
                $db->table('auth_groups_users')->insert([
                    'user_id' => $admin->id,
                    'group' => 'admin',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                echo "Added admin group assignment\n";
            } else {
                echo "Admin already has group: {$groupAssignment->group}\n";
            }
        }
        
        // Get student user
        $student = $db->table('users')->where('email', 'student@lphs.edu')->get()->getRow();
        if ($student) {
            // Check if student group assignment exists
            $groupAssignment = $db->table('auth_groups_users')->where('user_id', $student->id)->get()->getRow();
            if (!$groupAssignment) {
                // Add student to student group
                $db->table('auth_groups_users')->insert([
                    'user_id' => $student->id,
                    'group' => 'student',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                echo "Added student group assignment\n";
            } else {
                echo "Student already has group: {$groupAssignment->group}\n";
            }
        }
        
        // Get teacher user
        $teacher = $db->table('users')->where('email', 'demo.teacher@lphs.edu')->get()->getRow();
        if ($teacher) {
            // Check if teacher group assignment exists
            $groupAssignment = $db->table('auth_groups_users')->where('user_id', $teacher->id)->get()->getRow();
            if (!$groupAssignment) {
                // Add teacher to teacher group
                $db->table('auth_groups_users')->insert([
                    'user_id' => $teacher->id,
                    'group' => 'teacher',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                echo "Added teacher group assignment\n";
            } else {
                echo "Teacher already has group: {$groupAssignment->group}\n";
            }
        }
    }
}