<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Entities\User;

class DemoAccountsCompleteSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        
        echo "Starting complete demo accounts seeding...\n\n";
        
        // Clean up old conflicting demo records first
        $this->cleanupOldDemoRecords($db);
        
        // Demo Admin
        $this->createDemoAdmin($userModel, $db);
        
        // Create multiple demo teachers with different license types
        $teacherIds = $this->createDemoTeachers($userModel, $db);
        
        // Create sections with teachers as advisers
        $sectionIds = $this->createSections($db, $teacherIds);
        
        // Create student demos with both LRN formats
        $this->createDemoStudents($userModel, $db, $sectionIds);
        
        // Seed subjects and assign teachers
        $this->seedSubjectsAndAssignments($db, $teacherIds, $sectionIds);
        
        // Seed demo grades and attendance for analytics
        $this->seedDemoGradesAndAttendance($db, $teacherIds, $sectionIds);
        
        echo "\n=== DEMO ACCOUNTS CREATED SUCCESSFULLY ===\n";
        echo "Admin:\n";
        echo "  Email: demo.admin@lphs.edu\n";
        echo "  Password: DemoPass123!\n\n";
        
        echo "Teachers:\n";
        echo "  1. Maria Santos (Adviser - 1-A/Numerical)\n";
        echo "     Email: teacher.santos@lphs.edu\n";
        echo "     Employee ID: TCHR-2024-001\n";
        echo "     License: PRC-2024-001\n";
        echo "     Password: Teacher123!\n\n";
        echo "  2. Juan Reyes (Adviser - 1-B/Numerical)\n";
        echo "     Email: teacher.reyes@lphs.edu\n";
        echo "     Employee ID: EMP-2024-002\n";
        echo "     License: EMP-2024-002\n";
        echo "     Password: Teacher123!\n\n";
        echo "  3. Ana Garcia (Teacher)\n";
        echo "     Email: teacher.garcia@lphs.edu\n";
        echo "     Employee ID: TCHR-2024-003\n";
        echo "     License: PRC-2024-003\n";
        echo "     Password: Teacher123!\n\n";
        
        echo "Students:\n";
        echo "  Student 1 (Numerical LRN): demo.student1@lphs.edu / LRN: 136001000010 / Password: DemoPass123! - Section: 1-A\n";
        echo "  Student 2 (Non-Numerical LRN): demo.student2@lphs.edu / LRN: STU-2024-DEMO / Password: DemoPass123! - Section: 1-B\n\n";
        
        echo "Sections Created:\n";
        echo "  Grade 1 - 1-A (numerical) - Adviser: Maria Santos (ID: {$teacherIds[0]})\n";
        echo "  Grade 1 - 1-B (numerical) - Adviser: Ana Garcia (ID: {$teacherIds[2]})\n";
        echo "  Grade 1 - Aralin (numerical) - Adviser: Maria Santos\n";
        echo "  Grade 1 - Baybayin (non_numerical) - Adviser: Juan Reyes\n\n";
    }
    
    private function cleanupOldDemoRecords($db)
    {
        // Remove old conflicting demo records
        $emailsToClean = [
            'demo.teacher@lphs.edu',
            'demo.student@lphs.edu',
            'demo.student1@lphs.edu',
            'demo.student2@lphs.edu'
        ];
        
        foreach ($emailsToClean as $email) {
            $user = $db->table('users')->where('email', $email)->get()->getRow();
            if ($user) {
                // Delete related records first
                $db->table('auth_identities')->where('user_id', $user->id)->delete();
                $db->table('auth_groups_users')->where('user_id', $user->id)->delete();
                $db->table('teachers')->where('user_id', $user->id)->delete();
                $db->table('students')->where('user_id', $user->id)->delete();
                
                // Delete the user
                $db->table('users')->where('id', $user->id)->delete();
                
                echo "Cleaned up old demo records for: {$email}\n";
            }
        }
        
        // Also clean up duplicate teacher records with demo.teacher email
        $db->table('teachers')->where('email', 'demo.teacher@lphs.edu')->delete();
    }
    
    private function createDemoAdmin($userModel, $db)
    {
        $email = 'demo.admin@lphs.edu';
        $password = 'DemoPass123!';
        
        $existingUser = $db->table('users')->where('email', $email)->get()->getRow();
        
        if (!$existingUser) {
            $db->table('users')->insert([
                'email' => $email,
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $userId = $db->insertID();
            
            $db->table('auth_identities')->insert([
                'user_id' => $userId,
                'type' => 'email_password',
                'name' => $email,
                'secret' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id' => $userId,
                'group' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            echo "✓ Created demo admin\n";
        } else {
            echo "✓ Demo admin already exists\n";
        }
    }
    
    private function createDemoTeachers($userModel, $db)
    {
        $teachers = [
            [
                'email' => 'teacher.santos@lphs.edu',
                'employee_id' => 'TCHR-2024-001',
                'license_number' => 'PRC-2024-001',
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'gender' => 'Female',
                'date_of_birth' => '1985-03-15',
                'contact_number' => '09123456701',
                'address' => '123 Rizal St, Cauayan City',
                'department' => 'Mathematics',
                'position' => 'Teacher III',
                'specialization' => 'Mathematics',
                'date_hired' => '2020-06-15',
                'employment_status' => 'active'
            ],
            [
                'email' => 'teacher.reyes@lphs.edu',
                'employee_id' => 'EMP-2024-002',
                'license_number' => 'EMP-2024-002',
                'first_name' => 'Juan',
                'last_name' => 'Reyes',
                'gender' => 'Male',
                'date_of_birth' => '1988-07-22',
                'contact_number' => '09123456702',
                'address' => '456 Bonifacio Ave, Cauayan City',
                'department' => 'English',
                'position' => 'Teacher I',
                'specialization' => 'English Literature',
                'date_hired' => '2022-07-01',
                'employment_status' => 'active'
            ],
            [
                'email' => 'teacher.garcia@lphs.edu',
                'employee_id' => 'TCHR-2024-003',
                'license_number' => 'PRC-2024-003',
                'first_name' => 'Ana',
                'last_name' => 'Garcia',
                'gender' => 'Female',
                'date_of_birth' => '1982-11-08',
                'contact_number' => '09123456703',
                'address' => '789 Mabini St, Cauayan City',
                'department' => 'Science',
                'position' => 'Teacher IV',
                'specialization' => 'Biology',
                'date_hired' => '2018-06-01',
                'employment_status' => 'active'
            ]
        ];
        
        $teacherIds = [];
        
        foreach ($teachers as $teacherData) {
            $email = $teacherData['email'];
            
            $existingUser = $db->table('users')->where('email', $email)->get()->getRow();
            
            if (!$existingUser) {
                $db->table('users')->insert([
                    'email' => $email,
                    'active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $userId = $db->insertID();
                
                $db->table('auth_identities')->insert([
                    'user_id' => $userId,
                    'type' => 'email_password',
                    'name' => $email,
                    'secret' => password_hash('Teacher123!', PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                echo "✓ Created user for teacher: {$teacherData['first_name']} {$teacherData['last_name']}\n";
            } else {
                $userId = $existingUser->id;
                
                $identity = $db->table('auth_identities')
                    ->where('user_id', $userId)
                    ->where('type', 'email_password')
                    ->get()
                    ->getRow();
                
                if (!$identity) {
                    $db->table('auth_identities')->insert([
                        'user_id' => $userId,
                        'type' => 'email_password',
                        'name' => $email,
                        'secret' => password_hash('Teacher123!', PASSWORD_DEFAULT),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                
                echo "✓ User exists for teacher: {$teacherData['first_name']} {$teacherData['last_name']}\n";
            }
            
            $db->table('auth_groups_users')->ignore(true)->insert([
                'user_id' => $userId,
                'group' => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            $teacher = $db->table('teachers')->where('user_id', $userId)->get()->getRow();
            
            if (!$teacher) {
                $db->table('teachers')->ignore(true)->insert(array_merge($teacherData, [
                    'user_id' => $userId,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]));
                $teacherId = $db->insertID();
                echo "✓ Created teacher: {$teacherData['first_name']} {$teacherData['last_name']} ({$teacherData['license_number']})\n";
            } else {
                $db->table('teachers')->where('id', $teacher->id)->update(array_merge($teacherData, [
                    'updated_at' => date('Y-m-d H:i:s')
                ]));
                $teacherId = $teacher->id;
                echo "✓ Updated teacher: {$teacherData['first_name']} {$teacherData['last_name']}\n";
            }
            
            $teacherIds[] = $teacherId;
        }
        
        return $teacherIds;
    }
    
    private function createSections($db, $teacherIds)
    {
        $currentSchoolYear = get_current_school_year();
        
        // Remove old sections
        $db->table('sections')->where('school_year', $currentSchoolYear)->delete();
        $db->table('sections')->where('school_year', '2024-2025')->delete();
        
        $sections = [
            [
                'section_name' => '1-A',
                'grade_level' => 1,
                'school_year' => $currentSchoolYear,
                'grading_type' => 'numerical',
                'adviser_id' => $teacherIds[0] ?? null,
                'max_capacity' => 40,
                'current_enrollment' => 1
            ],
            [
                'section_name' => '1-B',
                'grade_level' => 1,
                'school_year' => $currentSchoolYear,
                'grading_type' => 'numerical',
                'adviser_id' => $teacherIds[2] ?? null,
                'max_capacity' => 40,
                'current_enrollment' => 1
            ],
            [
                'section_name' => 'Aralin',
                'grade_level' => 1,
                'school_year' => '2024-2025',
                'grading_type' => 'numerical',
                'adviser_id' => $teacherIds[0] ?? null,
                'max_capacity' => 40,
                'current_enrollment' => 1
            ],
            [
                'section_name' => 'Baybayin',
                'grade_level' => 1,
                'school_year' => '2024-2025',
                'grading_type' => 'non_numerical',
                'adviser_id' => $teacherIds[1] ?? null,
                'max_capacity' => 40,
                'current_enrollment' => 1
            ]
        ];
        
        $sectionIds = [];
        
        foreach ($sections as $section) {
            $db->table('sections')->ignore(true)->insert(array_merge($section, [
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]));
            $sectionIds[$section['section_name']] = $db->insertID();
            echo "✓ Created section: {$section['section_name']} - Grade {$section['grade_level']} - Adviser ID: {$section['adviser_id']}\n";
        }
        
        return $sectionIds;
    }
    
    private function seedSubjectsAndAssignments($db, $teacherIds, $sectionIds)
    {
        echo "\nSeeding subjects and assignments...\n";
        
        $subjects = $db->table('subjects')
            ->where('grade_level', 1)
            ->where('is_active', 1)
            ->get()
            ->getResult();
        
        if (empty($subjects)) {
            echo "⚠ No subjects found for grade 1\n";
            return;
        }
        
        echo "Found " . count($subjects) . " subjects for grade 1\n";
        
        foreach ($subjects as $subject) {
            foreach ($sectionIds as $sectionName => $sectionId) {
                $existing = $db->table('section_subjects')
                    ->where('section_id', $sectionId)
                    ->where('subject_id', $subject->id)
                    ->get()
                    ->getRow();
                
                if (!$existing) {
                    $db->table('section_subjects')->ignore(true)->insert([
                        'section_id' => $sectionId,
                        'subject_id' => $subject->id,
                        'is_active' => true,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    echo "✓ Assigned subject: {$subject->subject_name} to section {$sectionName}\n";
                }
            }
        }
        
        $this->createClassSchedules($db, $teacherIds, $sectionIds, $subjects);
    }
    
    private function createClassSchedules($db, $teacherIds, $sectionIds, $subjects)
    {
        echo "\nCreating class schedules...\n";
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timeSlots = [
            '07:30:00', '08:30:00', '09:30:00', '10:30:00', '11:30:00', 
            '13:00:00', '14:00:00', '15:00:00', '16:00:00'
        ];
        
        $scheduleIndex = 0;
        $teacherIndex = 0;
        
        foreach ($sectionIds as $sectionName => $sectionId) {
            $currentTeacherId = $teacherIds[$teacherIndex] ?? $teacherIds[0];
            
            foreach ($subjects as $subject) {
                if ($scheduleIndex >= count($timeSlots)) {
                    $scheduleIndex = 0;
                }
                
                $day = $days[array_rand($days)];
                $timeSlot = $timeSlots[$scheduleIndex];
                
                $teacher = $db->table('teachers')->where('id', $currentTeacherId)->get()->getRow();
                $teacherName = $teacher ? $teacher->first_name . ' ' . $teacher->last_name : 'Demo Teacher';
                
                $db->table('class_schedules')->ignore(true)->insert([
                    'section_id' => $sectionId,
                    'subject_name' => $subject->subject_name,
                    'teacher_name' => $teacherName,
                    'day_of_week' => $day,
                    'start_time' => $timeSlot,
                    'end_time' => date('H:i:s', strtotime($timeSlot . ' +50 minutes')),
                    'room' => 'Room ' . ($sectionId + 10),
                    'school_year' => '2024-2025',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $scheduleIndex++;
            }
            
            $teacherIndex++;
        }
        
        echo "✓ Created class schedules for all subjects\n";
    }
    
    private function seedDemoGradesAndAttendance($db, $teacherIds, $sectionIds)
    {
        echo "\nSeeding demo grades and attendance...\n";
        
        $students = $db->table('students')
            ->where('school_year', '2024-2025')
            ->get()
            ->getResult();
        
        if (empty($students)) {
            echo "⚠ No students found for grading\n";
            return;
        }
        
        $subjects = $db->table('subjects')
            ->where('is_active', 1)
            ->get()
            ->getResult();
        
        if (empty($subjects)) {
            echo "⚠ No subjects found\n";
            return;
        }
        
        $terms = [1, 2, 3];
        $schoolYear = '2024-2025';
        
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                foreach ($terms as $term) {
                    $grade = rand(70, 100);
                    
                    $db->table('grades')->ignore(true)->insert([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'school_year' => $schoolYear,
                        'term' => $term,
                        'grade' => $grade,
                        'remarks' => $grade >= 75 ? 'Passed' : 'Failed',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        
        echo "✓ Created grades for " . count($students) . " students across " . count($terms) . " terms\n";
        
        $this->seedAttendanceRecords($db, $students, $teacherIds[0]);
    }
    
    private function seedAttendanceRecords($db, $students, $teacherId)
    {
        echo "\nSeeding attendance records...\n";
        
        $statuses = ['Present', 'Absent', 'Late', 'Excused'];
        $weights = [85, 8, 5, 2];
        
        for ($i = 1; $i <= 30; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayOfWeek = date('N', strtotime($date));
            
            if ($dayOfWeek > 5) {
                continue;
            }
            
            foreach ($students as $student) {
                $status = $this->getWeightedRandomStatus($statuses, $weights);
                
                $db->table('attendance')->ignore(true)->insert([
                    'student_id' => $student->id,
                    'teacher_id' => $teacherId,
                    'date' => $date,
                    'status' => $status,
                    'remarks' => $status === 'Late' ? 'Arrived 15 minutes late' : null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        echo "✓ Created attendance records for past 30 school days\n";
    }
    
    private function getWeightedRandomStatus($statuses, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        $currentWeight = 0;
        
        foreach ($statuses as $index => $status) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $status;
            }
        }
        
        return $statuses[0];
    }
    
    private function createDemoStudents($userModel, $db, $sectionIds)
    {
        $students = [
            [
                'email' => 'demo.student1@lphs.edu',
                'lrn' => '136001000010',
                'first_name' => 'Demo',
                'last_name' => 'Student1',
                'gender' => 'Male',
                'date_of_birth' => '2015-01-01',
                'grade_level' => 1,
                'school_year' => '2024-2025',
                'section_id' => $sectionIds['1-A'] ?? null,
                'address' => 'Demo Address, City',
                'contact_number' => '09123456789',
                'emergency_contact_name' => 'Demo Parent',
                'emergency_contact_number' => '09987654321',
                'emergency_contact_relationship' => 'Parent'
            ],
            [
                'email' => 'demo.student2@lphs.edu',
                'lrn' => 'STU-2024-DEMO',
                'first_name' => 'Demo',
                'last_name' => 'Student2',
                'gender' => 'Female',
                'date_of_birth' => '2015-05-15',
                'grade_level' => 1,
                'school_year' => '2024-2025',
                'section_id' => $sectionIds['1-B'] ?? null,
                'address' => 'Demo Address, City',
                'contact_number' => '09234567890',
                'emergency_contact_name' => 'Demo Parent',
                'emergency_contact_number' => '09987654321',
                'emergency_contact_relationship' => 'Parent'
            ]
        ];
        
        foreach ($students as $student) {
            $existingLrn = $db->table('students')->where('lrn', $student['lrn'])->get()->getRow();
            
            if ($existingLrn) {
                echo "⚠ Skipping student {$student['first_name']} {$student['last_name']} - LRN {$student['lrn']} already exists\n";
                continue;
            }
            
            $existingUser = $db->table('users')->where('email', $student['email'])->get()->getRow();
            
            if (!$existingUser) {
                $db->table('users')->insert([
                    'email' => $student['email'],
                    'active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $userId = $db->insertID();
                
                $db->table('auth_identities')->insert([
                    'user_id' => $userId,
                    'type' => 'email_password',
                    'name' => $student['email'],
                    'secret' => password_hash('DemoPass123!', PASSWORD_DEFAULT),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $db->table('auth_groups_users')->ignore(true)->insert([
                    'user_id' => $userId,
                    'group' => 'student',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                
                echo "✓ Created user for student: {$student['first_name']} {$student['last_name']}\n";
            } else {
                $userId = $existingUser->id;
                
                $identity = $db->table('auth_identities')
                    ->where('user_id', $userId)
                    ->where('type', 'email_password')
                    ->get()
                    ->getRow();
                
                if (!$identity) {
                    $db->table('auth_identities')->insert([
                        'user_id' => $userId,
                        'type' => 'email_password',
                        'name' => $student['email'],
                        'secret' => password_hash('DemoPass123!', PASSWORD_DEFAULT),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                
                $db->table('auth_groups_users')->ignore(true)->insert([
                    'user_id' => $userId,
                    'group' => 'student',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                
                echo "✓ User exists for student: {$student['first_name']} {$student['last_name']}\n";
            }
            
            $studentRecord = $db->table('students')->where('user_id', $userId)->get()->getRow();
            
            if (!$studentRecord) {
                $insertData = [
                    'user_id' => $userId,
                    'lrn' => $student['lrn'],
                    'first_name' => $student['first_name'],
                    'last_name' => $student['last_name'],
                    'gender' => $student['gender'],
                    'date_of_birth' => $student['date_of_birth'],
                    'email' => $student['email'],
                    'enrollment_status' => 'enrolled',
                    'grade_level' => $student['grade_level'],
                    'section_id' => $student['section_id'],
                    'school_year' => $student['school_year'],
                    'address' => $student['address'],
                    'contact_number' => $student['contact_number'],
                    'emergency_contact_name' => $student['emergency_contact_name'],
                    'emergency_contact_number' => $student['emergency_contact_number'],
                    'emergency_contact_relationship' => $student['emergency_contact_relationship'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                $db->table('students')->ignore(true)->insert($insertData);
                echo "✓ Created student record: {$student['first_name']} {$student['last_name']} (LRN: {$student['lrn']}) in section ID: {$student['section_id']}\n";
            } else {
                $db->table('students')->where('id', $studentRecord->id)->update([
                    'lrn' => $student['lrn'],
                    'section_id' => $student['section_id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                echo "✓ Updated student record: {$student['first_name']} {$student['last_name']} (LRN: {$student['lrn']}) - section: {$student['section_id']}\n";
            }
        }
    }
}