<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixAllStudentPasswordsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Get all approved students with temp_password
        $students = $db->query("
            SELECT s.*, ai.secret 
            FROM students s 
            JOIN auth_identities ai ON s.user_id = ai.user_id 
            WHERE s.enrollment_status = 'approved' 
            AND s.temp_password IS NOT NULL 
            AND ai.type = 'email_password'
        ")->getResult();
        
        $fixed = 0;
        
        foreach ($students as $student) {
            // Check if password is actually an email (not a proper hash)
            if (strpos($student->secret, '@') !== false || !password_get_info($student->secret)['algo']) {
                // Use temp_password or default
                $password = $student->temp_password ?: 'Demo123!';
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Update auth identity
                $db->query("UPDATE auth_identities SET secret = ? WHERE user_id = ?", [
                    $hashedPassword,
                    $student->user_id
                ]);
                
                echo "Fixed password for {$student->first_name} {$student->last_name} (LRN: {$student->lrn}) - Password: {$password}\n";
                $fixed++;
            }
        }
        
        echo "\nFixed {$fixed} student passwords.\n";
    }
}