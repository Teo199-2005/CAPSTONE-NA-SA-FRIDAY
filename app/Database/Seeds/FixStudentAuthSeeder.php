<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixStudentAuthSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get student with LRN 740239857139
        $student = $db->query("SELECT * FROM students WHERE lrn = '740239857139'")->getRow();
        if (!$student) {
            echo "Student not found\n";
            return;
        }
        
        // Get user account
        $user = $db->query("SELECT * FROM users WHERE id = ?", [$student->user_id])->getRow();
        if (!$user) {
            echo "User not found\n";
            return;
        }
        
        // Update user email if empty
        if (empty($user->email)) {
            $email = strtolower($student->first_name . $student->last_name) . '@student.lphs.edu';
            $db->query("UPDATE users SET email = ? WHERE id = ?", [$email, $user->id]);
            echo "Updated user email to: {$email}\n";
        }
        
        // Set a known password for testing
        $testPassword = 'student123';
        $hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
        
        // Update auth identity
        $db->query("UPDATE auth_identities SET secret = ?, name = ? WHERE user_id = ?", [
            $hashedPassword, 
            $user->email ?: $email,
            $user->id
        ]);
        
        echo "Updated password for student {$student->first_name} {$student->last_name}\n";
        echo "LRN: {$student->lrn}\n";
        echo "Email: " . ($user->email ?: $email) . "\n";
        echo "Password: {$testPassword}\n";
    }
}