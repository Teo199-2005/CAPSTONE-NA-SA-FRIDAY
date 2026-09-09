<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SimpleDemoAccountsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Create demo admin if not exists
        $adminEmail = 'admin@lphs.edu';
        $adminUser = $db->table('users')->where('email', $adminEmail)->get()->getRowArray();
        
        if (!$adminUser) {
            // Insert user
            $db->table('users')->insert([
                'email' => $adminEmail,
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $adminUserId = $db->insertID();
            
            // Insert identity
            $db->table('auth_identities')->insert([
                'user_id' => $adminUserId,
                'type' => 'email_password',
                'secret' => $adminEmail,
                'secret2' => password_hash('admin123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Add to admin group
            $db->table('auth_groups_users')->insert([
                'user_id' => $adminUserId,
                'group' => 'admin',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            echo "Created admin account: $adminEmail / admin123\n";
        } else {
            echo "Admin account already exists\n";
        }
        
        // Create demo student if not exists
        $studentEmail = 'student@lphs.edu';
        $studentUser = $db->table('users')->where('email', $studentEmail)->get()->getRowArray();
        
        if (!$studentUser) {
            // Insert user
            $db->table('users')->insert([
                'email' => $studentEmail,
                'password_hash' => password_hash('student123', PASSWORD_DEFAULT),
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $studentUserId = $db->insertID();
            
            // Insert identity
            $db->table('auth_identities')->insert([
                'user_id' => $studentUserId,
                'type' => 'email_password',
                'secret' => $studentEmail,
                'secret2' => password_hash('student123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Add to student group
            $db->table('auth_groups_users')->insert([
                'user_id' => $studentUserId,
                'group' => 'student',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Create student record
            $db->table('students')->insert([
                'user_id' => $studentUserId,
                'lrn' => 'DEMO-STU-001',
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'gender' => 'Male',
                'date_of_birth' => '2008-01-01',
                'email' => $studentEmail,
                'enrollment_status' => 'enrolled',
                'grade_level' => 6,
                'school_year' => '2024-2025',
                'address' => 'Demo Address',
                'contact_number' => '09123456789',
                'emergency_contact_name' => 'Demo Parent',
                'emergency_contact_number' => '09987654321',
                'emergency_contact_relationship' => 'Parent',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            echo "Created student account: $studentEmail / student123\n";
        } else {
            echo "Student account already exists\n";
        }
    }
}