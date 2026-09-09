<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixDemoCredentialsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Update demo teacher with PRC license
        $db->table('teachers')
           ->where('first_name', 'Elaine')
           ->where('last_name', 'Oida')
           ->update(['license_number' => 'PRC-2024-001']);

        echo "Updated demo teacher with PRC license: PRC-2024-001\n";

        // Check demo student LRN
        $student = $db->table('students')
                     ->where('first_name', 'Maria')
                     ->where('last_name', 'Santos')
                     ->where('grade_level', 6)
                     ->get()
                     ->getRowArray();

        if ($student) {
            echo "Demo student Maria Santos LRN: " . ($student['lrn'] ?? 'Not set') . "\n";
        } else {
            echo "Demo student Maria Santos not found\n";
        }

        echo "Demo credentials setup complete!\n";
        echo "Teacher login: PRC-2024-001 / DemoPass123!\n";
        echo "Student login: 202491667 / DemoPass123!\n";
    }
}