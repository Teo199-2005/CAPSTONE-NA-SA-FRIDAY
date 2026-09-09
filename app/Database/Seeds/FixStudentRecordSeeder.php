<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixStudentRecordSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get student user
        $studentUser = $db->table('users')->where('email', 'student@lphs.edu')->get()->getRow();
        if (!$studentUser) {
            echo "Student user not found\n";
            return;
        }
        
        // Check if student record exists
        $studentRecord = $db->table('students')->where('user_id', $studentUser->id)->get()->getRow();
        if ($studentRecord) {
            echo "Student record already exists for user ID: {$studentUser->id}\n";
            return;
        }
        
        // Check if there's an existing student record with LRN DEMO-STUDENT-001
        $existingStudent = $db->table('students')->where('lrn', 'DEMO-STUDENT-001')->get()->getRow();
        if ($existingStudent) {
            // Update existing student record to link to correct user
            $db->table('students')->where('id', $existingStudent->id)->update([
                'user_id' => $studentUser->id,
                'email' => 'student@lphs.edu',
                'enrollment_status' => 'enrolled',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "Updated existing student record ID: {$existingStudent->id} to link to user ID: {$studentUser->id}\n";
        } else {
            // Create new student record
            $db->table('students')->insert([
                'user_id' => $studentUser->id,
                'lrn' => 'DEMO-STUDENT-001',
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'email' => 'student@lphs.edu',
                'grade_level' => 6,
                'section_id' => 1,
                'enrollment_status' => 'enrolled',
                'school_year' => '2024-2025',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "Created new student record for user ID: {$studentUser->id}\n";
        }
    }
}