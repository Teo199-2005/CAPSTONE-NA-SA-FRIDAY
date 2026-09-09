<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TeacherModel;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\SectionModel;
use App\Models\TeacherScheduleModel;
use App\Models\SubjectModel;

class Teachers extends BaseController
{
    /**
     * Display teachers list
     */
    public function index()
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = model(TeacherModel::class);
        $db = \Config\Database::connect();
        
        $search = $this->request->getGet('search');
        $assignment = $this->request->getGet('assignment');
        $sortBy = $this->request->getGet('sort_by');
        $sortOrder = strtolower((string) $this->request->getGet('sort_order')) === 'desc' ? 'DESC' : 'ASC';

        if (! in_array($sortBy, ['name', 'age'], true)) {
            $sortBy = 'name';
        }
        
        // Get all teachers
        $builder = $teacherModel->select('teachers.*');
        
        if ($search) {
            $builder->groupStart()
                   ->like('teachers.first_name', $search)
                   ->orLike('teachers.last_name', $search)
                   ->orLike('teachers.license_number', $search)
                   ->orLike('teachers.email', $search)
                   ->orLike('teachers.government_employee_no', $search)
                   ->orLike('teachers.tin', $search)
                   ->orLike('teachers.philsys_number', $search)
                   ->groupEnd();
        }
        
        if ($sortBy === 'age') {
            // Age ASC = younger first (newer DOB); DESC = older first (earlier DOB).
            $builder->where('teachers.date_of_birth IS NOT NULL');
            if ($sortOrder === 'ASC') {
                $builder->orderBy('teachers.date_of_birth', 'DESC');
            } else {
                $builder->orderBy('teachers.date_of_birth', 'ASC');
            }
            $builder->orderBy('teachers.last_name', 'ASC')
                   ->orderBy('teachers.first_name', 'ASC');
        } else {
            $builder->orderBy('teachers.last_name', $sortOrder)
                   ->orderBy('teachers.first_name', $sortOrder);
        }

        $teachers = $builder->findAll();
        
        // Get advisory sections and teaching sections for each teacher
        foreach ($teachers as &$teacher) {
            // Calculate age from date of birth for table display.
            $teacher['age'] = null;
            if (! empty($teacher['date_of_birth'])) {
                try {
                    $dob = new \DateTime((string) $teacher['date_of_birth']);
                    $today = new \DateTime('today');
                    $teacher['age'] = $dob->diff($today)->y;
                } catch (\Throwable $e) {
                    $teacher['age'] = null;
                }
            }

            // Get advisory section
            $advisorySection = $db->table('sections')
                ->select('section_name, grade_level')
                ->where('adviser_id', $teacher['id'])
                ->get()->getRow();
            
            $teacher['section_name'] = $advisorySection->section_name ?? null;
            $teacher['grade_level'] = $advisorySection->grade_level ?? null;
            
            // Get teaching sections (from teacher_schedules)
            $teachingSections = $db->query(
                "SELECT DISTINCT s.section_name, s.grade_level, sub.subject_name
                 FROM teacher_schedules ts 
                 JOIN sections s ON s.id = ts.section_id 
                 JOIN subjects sub ON sub.id = ts.subject_id
                 WHERE ts.teacher_id = ? AND s.adviser_id != ?",
                [$teacher['id'], $teacher['id']]
            )->getResultArray();
            
            $teacher['teaching_sections'] = $teachingSections;
        }
        
        // Apply assignment filter
        if ($assignment === 'assigned') {
            $teachers = array_filter($teachers, fn($t) => !empty($t['section_name']));
        } elseif ($assignment === 'unassigned') {
            $teachers = array_filter($teachers, fn($t) => empty($t['section_name']));
        }

        return view('admin/teachers', [
            'title' => 'Manage Teachers - CSCS SMS',
            'teachers' => $teachers,
            'search' => $search,
            'assignment' => $assignment,
            'sortBy' => $sortBy,
            'sortOrder' => strtolower($sortOrder),
        ]);
    }

    /**
     * Show create teacher form
     */
    public function create()
    {
        // Check if user is admin
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        return view('admin/teachers_create', [
            'title' => 'Add New Teacher - CSCS SMS'
        ]);
    }

    /**
     * Store new teacher
     */
    public function store()
    {
        // Check if user is admin
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        helper('teacher_form');
        $rules = teacher_store_validation_rules(true);

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $personnel = teacher_collect_personnel_from_request($this->request);
        if (empty($personnel['date_of_birth'])) {
            return redirect()->back()->withInput()->with('error', 'Please enter a valid date of birth.');
        }

        $userModel = model(UserModel::class);
        $teacherModel = model(TeacherModel::class);

        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create user record
            $db->table('users')->insert([
                'email' => $this->request->getPost('email'),
                'active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $userId = $db->insertID();
            if (!$userId) {
                throw new \Exception('Failed to create user account');
            }

            // Create password hash and auth identity
            $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            $db->table('auth_identities')->insert([
                'user_id' => $userId,
                'type' => 'email_password',
                'name' => '',
                'secret' => $hashedPassword,
                'secret2' => null,
                'expires' => null,
                'extra' => null,
                'force_reset' => 0,
                'last_used_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Add user to teacher group
            $db->table('auth_groups_users')->insert([
                'user_id' => $userId,
                'group' => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $teacherData = array_merge($personnel, [
                'user_id'     => $userId,
                'email'       => $this->request->getPost('email'),
            ]);

            $teacherModel->skipValidation(true);
            if (!$teacherModel->save($teacherData)) {
                $teacherModel->skipValidation(false);
                throw new \Exception('Failed to create teacher record: ' . implode(', ', $teacherModel->errors()));
            }
            $teacherModel->skipValidation(false);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('admin/teachers')
                ->with('success', 'Teacher created successfully.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create teacher: ' . $e->getMessage());
        }
    }

    /**
     * Show edit teacher form
     */
    public function edit($teacherId)
    {
        // Check if user is admin
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = model(TeacherModel::class);

        // Get teacher with user details - using WHERE clause instead of find()
        $teacher = $teacherModel->select('teachers.*, users.email')
            ->join('users', 'users.id = teachers.user_id', 'inner')
            ->where('teachers.id', $teacherId)
            ->first();

        if (!$teacher) {
            return redirect()->to('admin/teachers')
                ->with('error', 'Teacher not found.');
        }

        return view('admin/teachers_edit', [
            'title' => 'Edit Teacher - CSCS SMS',
            'teacher' => $teacher
        ]);
    }

    /**
     * Get teacher edit form for modal
     */
    public function editForm($teacherId)
    {
        // Check if user is admin
        if (! is_any_admin()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized access']);
        }

        // Validate teacher ID
        if (!is_numeric($teacherId) || $teacherId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid teacher ID']);
        }

        $teacherModel = model(TeacherModel::class);
        
        try {
            // Check if teacher exists without join first
            $teacherExists = $teacherModel->find($teacherId);
            if (!$teacherExists) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher not found']);
            }

            // Get teacher with user details - using WHERE clause instead of find()
            $teacher = $teacherModel->select('teachers.*, users.email')
                ->join('users', 'users.id = teachers.user_id', 'left')
                ->where('teachers.id', $teacherId)
                ->first();

            if (!$teacher) {
                return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher data could not be loaded']);
            }

            return view('admin/partials/teacher_edit_form', [
                'teacher' => $teacher
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error loading teacher edit form: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error occurred while loading teacher data']);
        }
    }

    /**
     * Update teacher
     */
    public function update($teacherId)
    {
        try {
            if (! is_any_admin()) {
                return $this->respondUpdate(false, 'Unauthorized', [], 403);
            }

            helper('teacher_form');
            $teacherModel = model(TeacherModel::class);
            $teacher = $teacherModel->find($teacherId);

            if (! $teacher) {
                return $this->respondUpdate(false, 'Teacher not found', [], 404);
            }

            $rules = teacher_store_validation_rules(false);
            $newEmail = $this->request->getPost('email');
            if ($newEmail && $newEmail !== $teacher['email']) {
                $rules['email'] = "required|valid_email|is_unique[teachers.email,id,{$teacherId}]";
            } else {
                $rules['email'] = 'required|valid_email';
            }

            if (! $this->validate($rules)) {
                return $this->respondUpdate(false, 'Validation failed', $this->validator->getErrors(), 422);
            }

            $personnel = teacher_collect_personnel_from_request($this->request);
            if (empty($personnel['date_of_birth'])) {
                return $this->respondUpdate(false, 'Validation failed', [
                    'birth_day' => 'Please enter a valid date of birth.',
                ], 422);
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $teacherData = $personnel;
            if ($newEmail) {
                $teacherData['email'] = $newEmail;
                if ($newEmail !== $teacher['email']) {
                    $db->table('users')->where('id', $teacher['user_id'])->update(['email' => $newEmail]);
                }
            }

            $teacherModel->skipValidation(true);
            $teacherModel->update($teacherId, $teacherData);
            $teacherModel->skipValidation(false);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->respondUpdate(false, 'Failed to update teacher data.');
            }

            return $this->respondUpdate(true, 'Teacher updated successfully.');
        } catch (\Exception $e) {
            log_message('error', 'Teacher update error: ' . $e->getMessage());

            return $this->respondUpdate(false, 'Server error occurred: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * JSON for AJAX modal updates; redirect for full-page edit form.
     */
    private function respondUpdate(bool $success, string $message, array $errors = [], int $status = 200)
    {
        $isAjax = $this->request->isAJAX()
            || str_contains((string) $this->request->getHeaderLine('Accept'), 'application/json');

        if ($isAjax) {
            $payload = ['success' => $success, 'message' => $message];
            if (! $success) {
                $payload['error'] = $message;
            }
            if ($errors !== []) {
                $payload['errors'] = $errors;
            }

            return $this->response->setStatusCode($status)->setJSON($payload);
        }

        if ($success) {
            return redirect()->to('admin/teachers')->with('success', $message);
        }

        return redirect()->back()->withInput()->with('error', $message)->with('errors', $errors);
    }



    /**
     * Delete teacher
     */
    public function delete($teacherId)
    {
        // Check if user is admin
        if (! is_any_admin()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $teacherModel = model(TeacherModel::class);
        $userModel = model(UserModel::class);

        $teacher = $teacherModel->find($teacherId);
        if (!$teacher) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher not found']);
        }

        // Delete teacher record first
        if ($teacherModel->delete($teacherId)) {
            // Then delete user account
            $userModel->delete($teacher['user_id']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Teacher deleted successfully.'
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to delete teacher.'
            ]);
        }
    }

    /**
     * View teacher details as full page
     */
    public function viewTeacher($teacherId)
    {
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = model(TeacherModel::class);
        $sectionModel = model(SectionModel::class);

        // Get teacher details
        $teacher = $teacherModel->where('id', $teacherId)->first();

        if (!$teacher) {
            return redirect()->to('admin/teachers')->with('error', 'Teacher not found');
        }

        // Get sections assigned to this teacher
        $sections = $sectionModel->where('adviser_id', $teacherId)->findAll();

        return view('admin/teacher_view', [
            'title' => 'Teacher Details - CSCS SMS',
            'teacher' => $teacher,
            'sections' => $sections
        ]);
    }

    /**
     * Get teacher details for modal display
     */
    public function details($teacherId)
    {
        return $this->getTeacherDetails($teacherId);
    }

    /**
     * Get teacher details for modal display
     */
    public function getTeacherDetails($teacherId)
    {
        // Check if user is admin
        if (! is_any_admin()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $teacherModel = model(TeacherModel::class);
        $sectionModel = model(SectionModel::class);

        // Get teacher details - using WHERE clause instead of find()
        $teacher = $teacherModel->where('id', $teacherId)->first();

        if (!$teacher) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Teacher not found']);
        }

        // Get sections assigned to this teacher
        $sections = $sectionModel->where('adviser_id', $teacherId)->findAll();

        return view('admin/partials/teacher_details_modal', [
            'teacher' => $teacher,
            'sections' => $sections
        ]);
    }

    /**
     * Manage teacher schedule
     */
    public function schedule($teacherId)
    {
        if (!auth()->user() || ! is_any_admin()) {
            return redirect()->to(base_url('/'));
        }

        $teacherModel = model(TeacherModel::class);
        $scheduleModel = model(TeacherScheduleModel::class);
        $subjectModel = model(SubjectModel::class);
        $sectionModel = model(SectionModel::class);

        $teacher = $teacherModel->find($teacherId);
        if (!$teacher) {
            return redirect()->to('admin/teachers')->with('error', 'Teacher not found');
        }

        $schedules = $scheduleModel->getTeacherSchedule($teacherId);
        
        // Get unique section-subject combinations assigned to this teacher
        $db = \Config\Database::connect();
        
        // Get assignments from teacher_schedules
        $assignments = $db->query("
            SELECT DISTINCT ts.section_id, ts.subject_id, 
                   s.section_name, s.grade_level, 
                   sub.subject_name
            FROM teacher_schedules ts
            JOIN sections s ON s.id = ts.section_id
            JOIN subjects sub ON sub.id = ts.subject_id
            WHERE ts.teacher_id = ? AND ts.school_year = ?
        ", [$teacherId, get_current_school_year()])->getResultArray();
        
        // Also get advisory section with all its subjects
        $advisorySection = $db->query("
            SELECT s.id as section_id, s.section_name, s.grade_level
            FROM sections s
            WHERE s.adviser_id = ?
        ", [$teacherId])->getRow();
        
        if ($advisorySection) {
            // Get all subjects for the advisory section's grade level
            $advisorySubjects = $db->query("
                SELECT id as subject_id, subject_name
                FROM subjects
                WHERE grade_level = ? AND is_active = 1
            ", [$advisorySection->grade_level])->getResultArray();
            
            // Add advisory section-subject combinations to assignments
            foreach ($advisorySubjects as $subject) {
                $assignments[] = [
                    'section_id' => $advisorySection->section_id,
                    'subject_id' => $subject['subject_id'],
                    'section_name' => $advisorySection->section_name,
                    'grade_level' => $advisorySection->grade_level,
                    'subject_name' => $subject['subject_name']
                ];
            }
        }
        
        // Extract unique sections and subjects from assignments
        $sectionIds = [];
        $subjectIds = [];
        $assignedCombinations = [];
        
        foreach ($assignments as $assignment) {
            $sectionIds[$assignment['section_id']] = [
                'id' => $assignment['section_id'],
                'section_name' => $assignment['section_name'],
                'grade_level' => $assignment['grade_level']
            ];
            $subjectIds[$assignment['subject_id']] = [
                'id' => $assignment['subject_id'],
                'subject_name' => $assignment['subject_name'],
                'grade_level' => $assignment['grade_level']
            ];
            $assignedCombinations[$assignment['section_id']][] = $assignment['subject_id'];
        }
        
        $sections = array_values($sectionIds);
        $subjects = array_values($subjectIds);
        
        // Group subjects by grade level for JavaScript
        $subjectsByGrade = [];
        foreach ($subjects as $subject) {
            $subjectsByGrade[$subject['grade_level']][] = $subject;
        }

        return view('admin/teacher_schedule', [
            'title' => 'Manage Schedule - ' . $teacher['first_name'] . ' ' . $teacher['last_name'],
            'teacher' => $teacher,
            'schedules' => $schedules,
            'subjects' => $subjects,
            'subjectsByGrade' => $subjectsByGrade,
            'sections' => $sections,
            'assignedCombinations' => $assignedCombinations
        ]);
    }

    /**
     * Get smart schedule suggestions
     */
    public function getScheduleSuggestions($teacherId)
    {
        if (! is_any_admin()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);
        $subjectId = $input['subject_id'] ?? null;
        $sectionId = $input['section_id'] ?? null;
        $day = $input['day'] ?? null;

        if (!$subjectId || !$sectionId) {
            return $this->response->setJSON(['success' => false, 'error' => 'Missing parameters']);
        }

        $db = \Config\Database::connect();
        $scheduleModel = model(TeacherScheduleModel::class);
        $subjectModel = model(SubjectModel::class);
        $sectionModel = model(SectionModel::class);

        // Get subject and section details
        $subject = $subjectModel->find($subjectId);
        $section = $sectionModel->find($sectionId);

        if (!$subject || !$section) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid subject or section']);
        }

        // Get all teachers who can teach this subject
        $availableTeachers = $db->query(
            "SELECT DISTINCT t.id, t.first_name, t.last_name, t.specialization
             FROM teachers t
             WHERE t.employment_status = 'active'
             AND (t.specialization LIKE ? OR t.department LIKE ?)",
            ['%' . $subject['subject_name'] . '%', '%' . $subject['subject_name'] . '%']
        )->getResultArray();

        // Get available rooms
        $allRooms = ['Room 101', 'Room 102', 'Room 103', 'Room 104', 'Room 201', 'Room 202', 'Room 203', 'Room 204'];

        // Get occupied time slots for this teacher
        $occupiedSlots = $scheduleModel->where('teacher_id', $teacherId)
            ->where('school_year', get_current_school_year())
            ->findAll();

        // Get occupied rooms for all teachers
        $occupiedRooms = $scheduleModel->where('school_year', get_current_school_year())
            ->findAll();

        // Define time slots
        $timeSlots = [
            ['start' => '07:00', 'end' => '08:00'],
            ['start' => '08:00', 'end' => '09:00'],
            ['start' => '09:00', 'end' => '10:00'],
            ['start' => '10:00', 'end' => '11:00'],
            ['start' => '11:00', 'end' => '12:00'],
            ['start' => '12:00', 'end' => '13:00'],
            ['start' => '13:00', 'end' => '14:00'],
            ['start' => '14:00', 'end' => '15:00'],
            ['start' => '15:00', 'end' => '16:00'],
            ['start' => '16:00', 'end' => '17:00']
        ];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $suggestions = [];

        foreach ($days as $dayName) {
            if ($day && $day !== $dayName) continue;

            foreach ($timeSlots as $slot) {
                // Check if teacher is available
                $teacherBusy = false;
                foreach ($occupiedSlots as $occupied) {
                    if ($occupied['day_of_week'] === $dayName &&
                        $occupied['start_time'] === $slot['start'] . ':00' &&
                        $occupied['end_time'] === $slot['end'] . ':00') {
                        $teacherBusy = true;
                        break;
                    }
                }

                if ($teacherBusy) continue;

                // Find available rooms
                $availableRooms = [];
                foreach ($allRooms as $room) {
                    $roomBusy = false;
                    foreach ($occupiedRooms as $occupied) {
                        if ($occupied['day_of_week'] === $dayName &&
                            $occupied['start_time'] === $slot['start'] . ':00' &&
                            $occupied['end_time'] === $slot['end'] . ':00' &&
                            $occupied['room'] === $room) {
                            $roomBusy = true;
                            break;
                        }
                    }
                    if (!$roomBusy) {
                        $availableRooms[] = $room;
                    }
                }

                if (!empty($availableRooms)) {
                    $suggestions[] = [
                        'day' => $dayName,
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end'],
                        'available_rooms' => $availableRooms
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'suggestions' => $suggestions,
            'available_teachers' => $availableTeachers,
            'subject' => $subject,
            'section' => $section
        ]);
    }

    /**
     * Save teacher schedule
     */
    public function saveSchedule($teacherId)
    {
        if (! is_any_admin()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        log_message('info', 'Saving schedule for teacher ID: ' . $teacherId);
        
        $scheduleModel = model(TeacherScheduleModel::class);
        
        // Get JSON data from request body
        $input = $this->request->getJSON(true);
        $schedules = $input['schedules'] ?? [];
        
        log_message('info', 'Schedule data received: ' . json_encode($schedules));

        if (empty($schedules)) {
            return $this->response->setJSON(['success' => false, 'error' => 'No schedule data provided']);
        }

        $db = \Config\Database::connect();
        
        // Check for duplicate subject-section combinations on the same day
        $subjectSectionByDay = [];
        foreach ($schedules as $schedule) {
            $day = $schedule['day_of_week'];
            $subjectId = $schedule['subject_id'];
            $sectionId = $schedule['section_id'];
            $key = $subjectId . '_' . $sectionId;
            
            if (isset($subjectSectionByDay[$day][$key])) {
                // Get subject and section names for error message
                $subjectModel = model(SubjectModel::class);
                $sectionModel = model(SectionModel::class);
                $subject = $subjectModel->find($subjectId);
                $section = $sectionModel->find($sectionId);
                $subjectName = $subject['subject_name'] ?? 'Subject';
                $sectionName = $section['section_name'] ?? 'Section';
                
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Duplicate detected: "' . $subjectName . '" for section "' . $sectionName . '" is scheduled multiple times on ' . $day . '. One subject per section per day only!'
                ]);
            }
            $subjectSectionByDay[$day][$key] = true;
        }
        
        // Check for room conflicts with other teachers
        foreach ($schedules as $schedule) {
            if (empty($schedule['room'])) continue;
            
            $conflicts = $db->table('teacher_schedules')
                ->where('day_of_week', $schedule['day_of_week'])
                ->where('start_time', $schedule['start_time'])
                ->where('end_time', $schedule['end_time'])
                ->where('room', $schedule['room'])
                ->where('teacher_id !=', $teacherId)
                ->where('school_year', get_current_school_year())
                ->get()->getResultArray();
            
            if (!empty($conflicts)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Room "' . $schedule['room'] . '" is already occupied on ' . $schedule['day_of_week'] . ' from ' . substr($schedule['start_time'], 0, 5) . ' to ' . substr($schedule['end_time'], 0, 5) . ' by another teacher.'
                ]);
            }
        }
        
        $db->transStart();

        try {
            // Delete existing schedules
            $deleted = $scheduleModel->where('teacher_id', $teacherId)->delete();
            log_message('info', 'Deleted existing schedules: ' . ($deleted ? 'success' : 'failed'));

            // Insert new schedules
            $inserted = 0;
            $errors = [];
            
            foreach ($schedules as $schedule) {
                $data = [
                    'teacher_id' => (int)$teacherId,
                    'subject_id' => (int)$schedule['subject_id'],
                    'section_id' => (int)$schedule['section_id'],
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'room' => $schedule['room'] ?? '',
                    'school_year' => get_current_school_year()
                ];
                
                log_message('info', 'Attempting to insert: ' . json_encode($data));
                
                if ($scheduleModel->insert($data)) {
                    $inserted++;
                    log_message('info', 'Successfully inserted schedule entry');
                } else {
                    $modelErrors = $scheduleModel->errors();
                    $errors[] = $modelErrors;
                    log_message('error', 'Failed to insert schedule: ' . json_encode($data) . ' Errors: ' . json_encode($modelErrors));
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                log_message('error', 'Transaction failed');
                return $this->response->setJSON(['success' => false, 'error' => 'Transaction failed']);
            }
            
            log_message('info', 'Inserted ' . $inserted . ' schedule entries out of ' . count($schedules));
            
            if ($inserted > 0) {
                return $this->response->setJSON(['success' => true, 'message' => 'Schedule saved successfully (' . $inserted . ' entries)']);
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'No schedules were saved. Errors: ' . json_encode($errors)]);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Schedule save error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}

