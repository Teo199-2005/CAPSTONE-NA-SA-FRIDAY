<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class QuickTeacherFix extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Add license_number column if it doesn't exist
        if (!$db->fieldExists('license_number', 'teachers')) {
            $db->query("ALTER TABLE teachers ADD COLUMN license_number VARCHAR(7) NULL AFTER teacher_id");
        }
        
        // Insert teachers with license numbers
        $teachers = [
            ['teacher_id' => '2025-0001', 'license_number' => '1234567', 'first_name' => 'Annabel', 'last_name' => 'Portades', 'email' => 'annabel.portades@lphs.edu.ph', 'department' => 'Filipino', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0002', 'license_number' => '2345678', 'first_name' => 'Aiza', 'last_name' => 'Sabordo', 'email' => 'aiza.sabordo@lphs.edu.ph', 'department' => 'Science', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0003', 'license_number' => '3456789', 'first_name' => 'Christine', 'last_name' => 'Lumabe', 'email' => 'christine.lumabe@lphs.edu.ph', 'department' => 'English', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0004', 'license_number' => '4567890', 'first_name' => 'Charito', 'last_name' => 'Malapo', 'email' => 'charito.malapo@lphs.edu.ph', 'department' => 'MAPEH', 'position' => 'Teacher', 'employment_status' => 'active'],
            ['teacher_id' => '2025-0005', 'license_number' => '5678901', 'first_name' => 'Elaine', 'last_name' => 'Oida', 'email' => 'elaine.oida@lphs.edu.ph', 'department' => 'Science', 'position' => 'Teacher', 'employment_status' => 'active'],
        ];
        
        $db->table('teachers')->truncate();
        
        foreach ($teachers as $teacher) {
            $teacher['created_at'] = date('Y-m-d H:i:s');
            $teacher['updated_at'] = date('Y-m-d H:i:s');
            $db->table('teachers')->insert($teacher);
        }
        
        echo "Teachers created with license numbers!\n";
    }
}