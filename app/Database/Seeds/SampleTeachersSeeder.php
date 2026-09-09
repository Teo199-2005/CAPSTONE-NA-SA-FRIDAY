<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SampleTeachersSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Clear existing teachers first
        $db->table('teachers')->truncate();
        
        // CSCS Teachers with Subjects & Schedules
        $teachers = [
            [
                'teacher_id' => '2025-0001',
                'license_number' => '1234567',
                'first_name' => 'Annabel',
                'middle_name' => '',
                'last_name' => 'Portades',
                'gender' => 'Female',
                'date_of_birth' => '1985-03-15',
                'contact_number' => '09171234567',
                'email' => 'annabel.portades@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Filipino',
                'position' => 'Teacher',
                'specialization' => 'Filipino, Values Education',
                'date_hired' => '2010-06-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0002',
                'license_number' => '2345678',
                'first_name' => 'Aiza',
                'middle_name' => '',
                'last_name' => 'Sabordo',
                'gender' => 'Female',
                'date_of_birth' => '1982-07-22',
                'contact_number' => '09181234567',
                'email' => 'aiza.sabordo@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Science',
                'position' => 'Teacher',
                'specialization' => 'Science, Mathematics',
                'date_hired' => '2008-08-15',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0003',
                'license_number' => '3456789',
                'first_name' => 'Christine',
                'middle_name' => 'E.',
                'last_name' => 'Lumabe',
                'gender' => 'Female',
                'date_of_birth' => '1988-11-08',
                'contact_number' => '09191234567',
                'email' => 'christine.lumabe@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'English',
                'position' => 'Teacher',
                'specialization' => 'English, TLE FSC, Araling Panlipunan',
                'date_hired' => '2012-03-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0004',
                'license_number' => '4567890',
                'first_name' => 'Charito',
                'middle_name' => '',
                'last_name' => 'Malapo',
                'gender' => 'Female',
                'date_of_birth' => '1980-05-12',
                'contact_number' => '09201234567',
                'email' => 'charito.malapo@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'MAPEH',
                'position' => 'Teacher',
                'specialization' => 'MAPEH, Values Education',
                'date_hired' => '2007-07-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0005',
                'license_number' => '5678901',
                'first_name' => 'Elaine',
                'middle_name' => 'S.',
                'last_name' => 'Oida',
                'gender' => 'Female',
                'date_of_birth' => '1986-09-25',
                'contact_number' => '09211234567',
                'email' => 'elaine.oida@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Science',
                'position' => 'Teacher',
                'specialization' => 'Values Education, TLE',
                'date_hired' => '2011-06-15',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0006',
                'license_number' => '6789012',
                'first_name' => 'Elisa',
                'middle_name' => '',
                'last_name' => 'Ereno',
                'gender' => 'Female',
                'date_of_birth' => '1984-12-03',
                'contact_number' => '09221234567',
                'email' => 'elisa.ereno@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Mathematics',
                'position' => 'Teacher',
                'specialization' => 'Mathematics',
                'date_hired' => '2009-08-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0007',
                'license_number' => '7890123',
                'first_name' => 'Joven',
                'middle_name' => 'F.',
                'last_name' => 'Labilles',
                'gender' => 'Male',
                'date_of_birth' => '1983-04-18',
                'contact_number' => '09231234567',
                'email' => 'joven.labilles@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Science',
                'position' => 'Teacher',
                'specialization' => 'Science, English',
                'date_hired' => '2008-09-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0008',
                'license_number' => '8901234',
                'first_name' => 'Laila',
                'middle_name' => 'N.',
                'last_name' => 'Salvadora',
                'gender' => 'Female',
                'date_of_birth' => '1987-08-30',
                'contact_number' => '09241234567',
                'email' => 'laila.salvadora@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'MAPEH',
                'position' => 'Teacher',
                'specialization' => 'MAPEH, Araling Panlipunan',
                'date_hired' => '2012-07-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0009',
                'license_number' => '9012345',
                'first_name' => 'Maricar',
                'middle_name' => 'B.',
                'last_name' => 'Sapugay',
                'gender' => 'Female',
                'date_of_birth' => '1985-12-14',
                'contact_number' => '09251234567',
                'email' => 'maricar.sapugay@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Mathematics',
                'position' => 'Teacher',
                'specialization' => 'Values Education, MAPEH',
                'date_hired' => '2010-08-15',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0010',
                'license_number' => '0123456',
                'first_name' => 'Midlyn',
                'middle_name' => 'B.',
                'last_name' => 'Castillo',
                'gender' => 'Female',
                'date_of_birth' => '1986-06-22',
                'contact_number' => '09261234567',
                'email' => 'midlyn.castillo@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Filipino',
                'position' => 'Teacher',
                'specialization' => 'Filipino, Mathematics',
                'date_hired' => '2011-09-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0011',
                'license_number' => '1357924',
                'first_name' => 'Nathan David',
                'middle_name' => '',
                'last_name' => 'Dolorical',
                'gender' => 'Male',
                'date_of_birth' => '1984-10-05',
                'contact_number' => '09271234567',
                'email' => 'nathan.dolorical@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Social Studies',
                'position' => 'Teacher',
                'specialization' => 'Araling Panlipunan',
                'date_hired' => '2009-10-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0012',
                'license_number' => '2468135',
                'first_name' => 'Roselle',
                'middle_name' => 'A.',
                'last_name' => 'Plotado',
                'gender' => 'Female',
                'date_of_birth' => '1987-02-28',
                'contact_number' => '09281234567',
                'email' => 'roselle.plotado@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Science',
                'position' => 'Teacher',
                'specialization' => 'Science, MAPEH',
                'date_hired' => '2012-11-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'teacher_id' => '2025-0013',
                'license_number' => '9876543',
                'first_name' => 'Jeanette',
                'middle_name' => 'S.',
                'last_name' => 'Rodriguez',
                'gender' => 'Female',
                'date_of_birth' => '1985-11-16',
                'contact_number' => '09291234567',
                'email' => 'jeanette.rodriguez@lphs.edu.ph',
                'address' => 'Nabua, Camarines Sur',
                'department' => 'Mathematics',
                'position' => 'Teacher',
                'specialization' => 'Mathematics',
                'date_hired' => '2010-12-01',
                'employment_status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        // Insert all teachers
        $db->table('teachers')->insertBatch($teachers);
        
        echo "Sample teachers created successfully!\n";
        echo "Created " . count($teachers) . " teachers.\n";
        
        // Display created teachers
        echo "\nTeachers created:\n";
        foreach ($teachers as $teacher) {
            echo "  - {$teacher['first_name']} {$teacher['last_name']} ({$teacher['department']})\n";
        }
    }
}

