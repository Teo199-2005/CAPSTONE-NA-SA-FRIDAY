<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;
use App\Models\TeacherModel;
use App\Models\StudentModel;
use App\Models\ParentModel;

class DemoAccountsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $users = model(UserModel::class);
        $password = 'DemoPass123!';

        $created = [];

        // Admin
        $created['admin'] = $this->createUserIfMissing($users, 'demo.admin@lphs.edu', $password, 'admin');

        // Teacher
        $teacherUserId = $this->createUserIfMissing($users, 'demo.teacher@lphs.edu', $password, 'teacher');
        if ($teacherUserId) {
            $tm = new TeacherModel();
            $exists = $tm->where('user_id', $teacherUserId)->first();
            if (! $exists) {
                $tm->insert([
                    'user_id'           => $teacherUserId,
                    'employee_id'       => 'DEMO-T001',
                    'first_name'        => 'Demo',
                    'last_name'         => 'Teacher',
                    'gender'            => 'Male',
                    'email'             => 'demo.teacher@lphs.edu',
                    'employment_status' => 'active',
                ]);
            }
        }

        $currentYear = date('Y');
        $schoolYear  = $currentYear . '-' . ($currentYear + 1);

        $studentUserId = $this->createUserIfMissing($users, 'demo.student@lphs.edu', $password, 'student');
        if ($studentUserId) {
            $sm     = new StudentModel();
            $exists = $sm->where('user_id', $studentUserId)->first();
            if (! $exists) {
                $sm->insert([
                    'user_id'           => $studentUserId,
                    'lrn'               => $this->nextLrn($db),
                    'first_name'        => 'Demo',
                    'last_name'         => 'Student',
                    'gender'            => 'Male',
                    'date_of_birth'     => date('Y-m-d', strtotime('-12 years')),
                    'email'             => 'demo.student@lphs.edu',
                    'enrollment_status' => 'enrolled',
                    'grade_level'       => 6,
                    'school_year'       => $schoolYear,
                ]);
            }
        }

        $newStudentUserId = $this->createUserIfMissing($users, 'new.student@lphs.edu', $password, 'student');
        if ($newStudentUserId) {
            $sm     = new StudentModel();
            $exists = $sm->where('user_id', $newStudentUserId)->first();
            if (! $exists) {
                $sm->insert([
                    'user_id'                        => $newStudentUserId,
                    'lrn'                            => $this->nextLrn($db),
                    'first_name'                     => 'John',
                    'last_name'                      => 'Doe',
                    'gender'                         => 'Male',
                    'date_of_birth'                  => date('Y-m-d', strtotime('-11 years')),
                    'email'                          => 'new.student@lphs.edu',
                    'enrollment_status'              => 'pending',
                    'grade_level'                    => 5,
                    'school_year'                    => $schoolYear,
                    'address'                        => '123 Main Street, City',
                    'contact_number'                 => '09123456789',
                    'emergency_contact_name'         => 'Jane Doe',
                    'emergency_contact_number'       => '09987654321',
                    'emergency_contact_relationship' => 'Mother',
                ]);
            }
        }

        // Parent
        $parentUserId = $this->createUserIfMissing($users, 'demo.parent@lphs.edu', $password, 'parent');
        if ($parentUserId) {
            $pm = new ParentModel();
            $exists = $pm->where('user_id', $parentUserId)->first();
            if (! $exists) {
                $pm->insert([
                    'user_id' => $parentUserId,
                    'first_name' => 'Demo',
                    'last_name' => 'Parent',
                    'email' => 'demo.parent@lphs.edu',
                    'contact_number' => '09123456789',
                    'address' => 'Demo Address',
                ]);
            }
        }
    }

    private function nextLrn($db): string
    {
        $row = $db->table('students')
            ->select('lrn')
            ->where('lrn IS NOT NULL', null, false)
            ->orderBy('lrn', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        return $row ? (string) (((int) $row['lrn']) + 1) : '136001000001';
    }

    private function createUserIfMissing(UserModel $users, string $email, string $password, string $group): ?int
    {
        $db = \Config\Database::connect();

        $identity = $db->table('auth_identities')
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->get()
            ->getRowArray();

        if ($identity) {
            $userId = (int) $identity['user_id'];
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id'    => $userId,
                'group'      => $group,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            return $userId;
        }

        $existing = $db->table('users')->where('email', $email)->get()->getRowArray();
        if ($existing) {
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id'    => $existing['id'],
                'group'      => $group,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            return (int) $existing['id'];
        }

        $user = new User([
            'email'    => $email,
            'password' => $password,
            'active'   => 1,
        ]);
        $users->save($user);
        $id = (int) $users->getInsertID();

        $db->table('auth_groups_users')->ignore(true)->insert([
            'user_id'    => $id,
            'group'      => $group,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $id;
    }
}


