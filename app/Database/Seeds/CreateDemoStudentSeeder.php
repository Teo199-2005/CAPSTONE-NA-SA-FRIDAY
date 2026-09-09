<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

class CreateDemoStudentSeeder extends Seeder
{
    public function run()
    {
        $db        = $this->db;
        $userModel = new UserModel();

        $section = $db->table('sections')
            ->where('grade_level', 6)
            ->where('is_active', true)
            ->orderBy('id', 'ASC')
            ->get()
            ->getRowArray();

        if (! $section) {
            echo "No Grade 6 section found! Run PhilippineSectionsSeeder first.\n";
            return;
        }

        $currentYear = date('Y');
        $schoolYear  = $currentYear . '-' . ($currentYear + 1);

        $userData = [
            'email'    => 'mariasantos67@hotmail.com',
            'password' => 'student123',
            'active'   => 1,
        ];

        $existingUser = $userModel->where('email', 'mariasantos67@hotmail.com')->first();

        if ($existingUser) {
            echo "Demo student user already exists. Updating student record...\n";
            $userId = $existingUser->id;
        } else {
            $userModel->save($userData);
            $userId = $userModel->getInsertID();

            $user = $userModel->find($userId);
            $user->addGroup('student');
            echo "Created demo student user account.\n";
        }

        $lastStudent = $db->table('students')->select('lrn')->orderBy('lrn', 'DESC')->get()->getRowArray();
        $nextLrn     = $lastStudent ? (string) (intval($lastStudent['lrn']) + 1) : '100000000001';

        $existingStudent = $db->table('students')->where('user_id', $userId)->get()->getRowArray();

        if ($existingStudent) {
            $db->table('students')
                ->where('id', $existingStudent['id'])
                ->update([
                    'grade_level'       => 6,
                    'section_id'        => $section['id'],
                    'enrollment_status' => 'enrolled',
                    'school_year'       => $schoolYear,
                    'updated_at'        => date('Y-m-d H:i:s'),
                ]);
            echo "Updated existing demo student record.\n";
        } else {
            $db->table('students')->insert([
                'user_id'                        => $userId,
                'lrn'                            => $nextLrn,
                'first_name'                     => 'Maria',
                'last_name'                      => 'Santos',
                'gender'                         => 'Female',
                'date_of_birth'                  => date('Y-m-d', strtotime('-12 years -3 months')),
                'place_of_birth'                 => 'Cauayan City, Isabela',
                'nationality'                    => 'Filipino',
                'religion'                       => 'Catholic',
                'contact_number'                 => '09123456789',
                'address'                        => 'Barangay South, Cauayan City, Isabela',
                'emergency_contact_name'         => 'Rosa Santos',
                'emergency_contact_number'       => '09987654321',
                'emergency_contact_relationship' => 'Mother',
                'grade_level'                    => 6,
                'section_id'                     => $section['id'],
                'enrollment_status'              => 'enrolled',
                'school_year'                    => $schoolYear,
                'created_at'                     => date('Y-m-d H:i:s'),
                'updated_at'                     => date('Y-m-d H:i:s'),
            ]);
            echo "Created new demo student record.\n";
        }

        $count = $db->table('students')
            ->where('section_id', $section['id'])
            ->where('enrollment_status', 'enrolled')
            ->countAllResults();

        $db->table('sections')
            ->where('id', $section['id'])
            ->update(['current_enrollment' => $count]);

        echo "Demo student created successfully!\n";
        echo "Email: mariasantos67@hotmail.com\n";
        echo "Password: student123\n";
        echo "Grade: 6\n";
        echo "Section: {$section['section_name']}\n";
        echo "LRN: {$nextLrn}\n";
    }
}
