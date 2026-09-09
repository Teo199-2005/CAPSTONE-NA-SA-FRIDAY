<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreateDemoTeacherWithStudentsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $currentYear = date('Y');
        $schoolYear  = $currentYear . '-' . ($currentYear + 1);

        $teacherData = [
            'employee_id'       => 'DEMO-T001',
            'first_name'        => 'Demo',
            'last_name'         => 'Teacher',
            'gender'            => 'Male',
            'email'             => 'demo.teacher@lphs.edu',
            'employment_status' => 'active',
            'position'          => 'Teacher I',
            'department'        => 'Elementary',
        ];

        $existingTeacher = $db->table('teachers')->where('employee_id', 'DEMO-T001')->get()->getRowArray();
        if ($existingTeacher) {
            $db->table('teachers')->where('id', $existingTeacher['id'])->update($teacherData);
            $teacherId = $existingTeacher['id'];
        } else {
            $db->table('teachers')->insert($teacherData);
            $teacherId = $db->insertID();
        }

        $sectionData = [
            'section_name' => '6-A',
            'grade_level'  => 6,
            'adviser_id'   => $teacherId,
            'school_year'  => $schoolYear,
            'max_capacity' => 40,
            'is_active'    => 1,
        ];

        $existingSection = $db->table('sections')
            ->where('section_name', '6-A')
            ->where('grade_level', 6)
            ->where('school_year', $schoolYear)
            ->get()
            ->getRowArray();

        if ($existingSection) {
            $db->table('sections')->where('id', $existingSection['id'])->update($sectionData);
            $sectionId = $existingSection['id'];
        } else {
            $db->table('sections')->insert($sectionData);
            $sectionId = $db->insertID();
        }

        $students = [
            ['lrn' => '136001000001', 'first_name' => 'Juan',   'last_name' => 'Cruz',      'gender' => 'Male'],
            ['lrn' => '136001000002', 'first_name' => 'Maria',  'last_name' => 'Santos',    'gender' => 'Female'],
            ['lrn' => '136001000003', 'first_name' => 'Pedro',  'last_name' => 'Garcia',    'gender' => 'Male'],
            ['lrn' => '136001000004', 'first_name' => 'Ana',    'last_name' => 'Lopez',     'gender' => 'Female'],
            ['lrn' => '136001000005', 'first_name' => 'Jose',   'last_name' => 'Martinez',  'gender' => 'Male'],
            ['lrn' => '136001000006', 'first_name' => 'Carmen', 'last_name' => 'Rodriguez', 'gender' => 'Female'],
            ['lrn' => '136001000007', 'first_name' => 'Miguel', 'last_name' => 'Hernandez', 'gender' => 'Male'],
            ['lrn' => '136001000008', 'first_name' => 'Sofia',  'last_name' => 'Gonzalez',  'gender' => 'Female'],
        ];

        $createdStudents = 0;
        foreach ($students as $studentData) {
            $fullStudentData = array_merge($studentData, [
                'section_id'                     => $sectionId,
                'grade_level'                    => 6,
                'enrollment_status'              => 'enrolled',
                'school_year'                    => $schoolYear,
                'date_of_birth'                  => date('Y-m-d', strtotime('-12 years')),
                'address'                        => 'Cauayan City, Isabela',
                'contact_number'                 => '09123456789',
                'emergency_contact_name'         => 'Parent Name',
                'emergency_contact_number'       => '09987654321',
                'emergency_contact_relationship' => 'Parent',
            ]);

            $existing = $db->table('students')->where('lrn', $studentData['lrn'])->get()->getRowArray();
            if ($existing) {
                $db->table('students')->where('id', $existing['id'])->update($fullStudentData);
            } else {
                $db->table('students')->insert($fullStudentData);
                $createdStudents++;
            }
        }

        $count = $db->table('students')
            ->where('section_id', $sectionId)
            ->where('enrollment_status', 'enrolled')
            ->countAllResults();

        $db->table('sections')
            ->where('id', $sectionId)
            ->update(['current_enrollment' => $count]);

        echo 'Demo teacher created/updated with Grade 6 section containing ' . count($students) . " students ({$createdStudents} new).\n";
    }
}
