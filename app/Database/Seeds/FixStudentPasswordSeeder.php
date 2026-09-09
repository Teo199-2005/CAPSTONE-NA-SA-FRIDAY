<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixStudentPasswordSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get student with LRN 340979438047
        $student = $db->query("SELECT * FROM students WHERE lrn = '340979438047'")->getRow();
        if (!$student) {
            echo "Student not found\n";
            return;
        }
        
        // Set the correct password
        $password = 'Demo123!';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update auth identity with correct password
        $db->query("UPDATE auth_identities SET secret = ? WHERE user_id = ?", [
            $hashedPassword,
            $student->user_id
        ]);
        
        echo "Updated password for student {$student->first_name} {$student->last_name}\n";
        echo "LRN: {$student->lrn}\n";
        echo "Password: {$password}\n";
    }
}