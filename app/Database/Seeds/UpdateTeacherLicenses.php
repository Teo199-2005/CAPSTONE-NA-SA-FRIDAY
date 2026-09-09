<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateTeacherLicenses extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        $updates = [
            ['teacher_id' => '2025-0001', 'license_number' => '1234567'],
            ['teacher_id' => '2025-0002', 'license_number' => '2345678'],
            ['teacher_id' => '2025-0003', 'license_number' => '3456789'],
            ['teacher_id' => '2025-0004', 'license_number' => '4567890'],
            ['teacher_id' => '2025-0005', 'license_number' => '5678901'],
            ['teacher_id' => '2025-0006', 'license_number' => '6789012'],
            ['teacher_id' => '2025-0007', 'license_number' => '7890123'],
            ['teacher_id' => '2025-0008', 'license_number' => '8901234'],
            ['teacher_id' => '2025-0009', 'license_number' => '9012345'],
            ['teacher_id' => '2025-0010', 'license_number' => '0123456'],
            ['teacher_id' => '2025-0011', 'license_number' => '1357924'],
            ['teacher_id' => '2025-0012', 'license_number' => '2468135'],
            ['teacher_id' => '2025-0013', 'license_number' => '9876543'],
        ];
        
        foreach ($updates as $update) {
            $db->table('teachers')
               ->where('teacher_id', $update['teacher_id'])
               ->update(['license_number' => $update['license_number']]);
        }
        
        echo "Updated license numbers for all teachers.\n";
    }
}