<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmergencyContactsSeeder extends Seeder
{
    public function run()
    {
        // Sample emergency contacts data
        $emergencyContacts = [
            ['name' => 'Maria Garcia', 'number' => '09171234567', 'relationship' => 'Mother'],
            ['name' => 'Juan Lopez', 'number' => '09181234568', 'relationship' => 'Father'],
            ['name' => 'Rosa Martinez', 'number' => '09191234569', 'relationship' => 'Mother'],
            ['name' => 'Carlos Rodriguez', 'number' => '09201234570', 'relationship' => 'Father'],
            ['name' => 'Ana Hernandez', 'number' => '09211234571', 'relationship' => 'Mother'],
            ['name' => 'Luis Santos', 'number' => '09221234572', 'relationship' => 'Father'],
            ['name' => 'Carmen Cruz', 'number' => '09231234573', 'relationship' => 'Mother'],
            ['name' => 'Miguel Reyes', 'number' => '09241234574', 'relationship' => 'Father'],
            ['name' => 'Elena Torres', 'number' => '09251234575', 'relationship' => 'Mother'],
            ['name' => 'Diego Morales', 'number' => '09261234576', 'relationship' => 'Father'],
            ['name' => 'Lucia Flores', 'number' => '09271234577', 'relationship' => 'Mother'],
            ['name' => 'Roberto Gutierrez', 'number' => '09281234578', 'relationship' => 'Father'],
            ['name' => 'Demo Parent', 'number' => '09123456789', 'relationship' => 'Mother']
        ];

        // Get students with missing emergency contacts
        $builder = $this->db->table('students');
        $students = $builder->where('emergency_contact_name IS NULL OR emergency_contact_name = ""')->get()->getResultArray();

        echo "Found " . count($students) . " students with missing emergency contacts.\n";

        $contactIndex = 0;
        foreach ($students as $student) {
            if ($contactIndex >= count($emergencyContacts)) {
                $contactIndex = 0; // Reset to reuse contacts
            }
            
            $contact = $emergencyContacts[$contactIndex];
            
            $updateData = [
                'emergency_contact_name' => $contact['name'],
                'emergency_contact_number' => $contact['number'],
                'emergency_contact_relationship' => $contact['relationship']
            ];
            
            $builder->where('id', $student['id'])->update($updateData);
            
            echo "Updated {$student['first_name']} {$student['last_name']} ({$student['student_id']}) with emergency contact: {$contact['name']}\n";
            
            $contactIndex++;
        }

        echo "Emergency contacts update completed!\n";
    }
}