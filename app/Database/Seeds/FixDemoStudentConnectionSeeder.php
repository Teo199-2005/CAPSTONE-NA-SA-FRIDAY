<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixDemoStudentConnectionSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        
        // Find the user with the demo email
        $user = $db->table('users')
            ->where('email', 'mariasantos67@hotmail.com')
            ->get()
            ->getRowArray();
        
        if (!$user) {
            echo "User not found!\n";
            return;
        }
        
        echo "Found user ID: {$user['id']}\n";
        
        // Find the student record for Maria Santos
        $student = $db->table('students')
            ->where('first_name', 'Maria')
            ->where('last_name', 'Santos')
            ->where('grade_level', 6)
            ->get()
            ->getRowArray();
        
        if (!$student) {
            echo "Student record not found!\n";
            return;
        }
        
        echo "Found student ID: {$student['id']}\n";
        echo "Current student user_id: " . ($student['user_id'] ?? 'NULL') . "\n";
        
        // Update the student record to link to the correct user
        $db->table('students')
            ->where('id', $student['id'])
            ->update([
                'user_id' => $user['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
        echo "Updated student record to link to user ID: {$user['id']}\n";
        
        // Verify the connection
        $updatedStudent = $db->table('students')
            ->where('id', $student['id'])
            ->get()
            ->getRowArray();
        
        echo "Verification - Student user_id is now: {$updatedStudent['user_id']}\n";
        echo "Demo student connection fixed successfully!\n";
    }
}