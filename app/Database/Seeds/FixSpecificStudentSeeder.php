<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixSpecificStudentSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get student with LRN 646715417683
        $student = $db->query("SELECT * FROM students WHERE lrn = '646715417683'")->getRow();
        if (!$student) {
            echo "Student not found\n";
            return;
        }
        
        echo "Student: {$student->first_name} {$student->last_name}\n";
        echo "Temp password: " . ($student->temp_password ?: 'NULL') . "\n";
        
        // Set the correct password (use temp_password or default)
        $password = $student->temp_password ?: 'Demo123!';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update auth identity with correct password
        $db->query("UPDATE auth_identities SET secret = ? WHERE user_id = ?", [
            $hashedPassword,
            $student->user_id
        ]);
        
        echo "Updated password to: {$password}\n";
    }
}