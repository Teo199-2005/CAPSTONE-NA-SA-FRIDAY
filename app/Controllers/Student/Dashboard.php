<?php
namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\GradeModel;
use App\Models\AnnouncementModel;
use App\Models\SubjectModel;
use App\Models\NotificationModel;

class Dashboard extends BaseController
{
    protected $auth;
    protected $studentRecord;
    
    public function __construct()
    {
        $this->auth = auth();
        helper('time');
    }

    protected function getStudentRecord()
    {
        if (!$this->studentRecord) {
            $studentModel = new StudentModel();
            $this->studentRecord = $studentModel->where('user_id', $this->auth->id())->first();
        }
        return $this->studentRecord;
    }

    /**
     * Generate performance message based on grade average
     */
    protected function getPerformanceMessage($average)
    {
        if ($average === null) {
            return ['message' => 'No grades yet', 'class' => 'bg-secondary text-white', 'icon' => 'bi-clipboard-data'];
        }

        if ($average >= 95) {
            return ['message' => 'Outstanding!', 'class' => 'bg-success text-white', 'icon' => 'bi-trophy-fill'];
        } elseif ($average >= 90) {
            return ['message' => 'Excellent work!', 'class' => 'bg-success text-white', 'icon' => 'bi-star-fill'];
        } elseif ($average >= 85) {
            return ['message' => 'Great work!', 'class' => 'bg-success text-white', 'icon' => 'bi-hand-thumbs-up-fill'];
        } elseif ($average >= 80) {
            return ['message' => 'Good job!', 'class' => 'bg-info text-white', 'icon' => 'bi-check-circle-fill'];
        } elseif ($average >= 75) {
            return ['message' => 'Keep improving!', 'class' => 'bg-warning text-white', 'icon' => 'bi-graph-up-arrow'];
        } else {
            return ['message' => 'Need more effort!', 'class' => 'bg-danger text-white', 'icon' => 'bi-lightning-charge-fill'];
        }
    }

    public function index()
    {
        // TEMPORARY: Bypass authentication to test if the issue is with auth or the view
        try {
            // Try to get auth status without redirecting
            $isLoggedIn = $this->auth->loggedIn();
            $authStatus = $isLoggedIn ? 'Logged in' : 'Not logged in';
        } catch (\Throwable $e) {
            $authStatus = 'Auth error: ' . $e->getMessage();
        }

        // Get recent notifications for current user
        $notificationModel = new NotificationModel();
        $notifications = $notificationModel->where('user_id', $this->auth->id() ?? 1)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();
        
        $unreadCount = $notificationModel->where('user_id', $this->auth->id() ?? 1)
            ->where('is_read', false)
            ->countAllResults();

        // Try to get real student data if possible
        $student = null;
        $termAverage = null;
        $currentTerm = $this->getCurrentTerm();
        $schoolYear = get_current_school_year();

        try {
            if ($this->auth->loggedIn() && $this->auth->user()->inGroup('student')) {
                $studentModel = new \App\Models\StudentModel();
                $gradeModel = new \App\Models\GradeModel();

                $student = $studentModel->where('user_id', $this->auth->id())->first();

                if ($student) {
                    $termAverage = $gradeModel->getTermAverage($student['id'], $schoolYear, $currentTerm);
                }
            }
        } catch (\Throwable $e) {
            // Fall back to test data if there's an error
        }

        // If no authenticated student, redirect to login
        if (!$student) {
            return redirect()->to(base_url('login'));
        }

        // Featured poster for student dashboard
        helper('asset');
        $studentPoster = featured_dashboard_poster('student');

        return view('student/dashboard', [
            'title' => 'Student Dashboard - CSCS SMS',
            'student' => $student,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'recentGrades' => [],
            'termAverage' => $termAverage,
            'currentTerm' => $currentTerm,
            'performanceMessage' => $this->getPerformanceMessage($termAverage),
            'featuredPosterStudent'    => $studentPoster['path'],
            'featuredPosterStudentUrl' => $studentPoster['url'],
            'nutrition_profile_incomplete' => ! StudentModel::isNutritionProfileComplete($student),
        ]);
    }

    public function profile()
    {
        // Check if user is authenticated
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }

        $studentModel = new StudentModel();
        $studentWithSection = $studentModel->getStudentWithSection($student['id']);

        return view('student/profile', [
            'title' => 'My Profile - CSCS SMS',
            'student' => $studentWithSection
        ]);
    }

    public function grades()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }

        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();

        $schoolYear = $this->request->getGet('school_year') ?? get_current_school_year();
        $term = (int) ($this->request->getGet('term') ?? $this->getCurrentTerm());

        // Get subjects assigned to student's section
        $subjects = $student['section_id'] ? $subjectModel->getSectionSubjects($student['section_id']) : [];

        // Get grades for the selected term
        $grades = [];
        foreach ($subjects as $subject) {
            $grade = $gradeModel->where('student_id', $student['id'])
                ->where('subject_id', $subject['id'])
                ->where('school_year', $schoolYear)
                ->where('term', $term)
                ->first();

            $grades[] = [
                'subject' => $subject,
                'grade' => $grade
            ];
        }

        $termAverage = $gradeModel->getTermAverage($student['id'], $schoolYear, $term);
        $gwa = $gradeModel->getFinalAverage($student['id'], $schoolYear);

        $allTermGrades = [];
        for ($t = 1; $t <= 3; $t++) {
            $allTermGrades[$t] = $gradeModel->getTermAverage($student['id'], $schoolYear, $t);
        }

        $canEnrollNextYear = ($gwa !== null && $gwa >= 75.0);

        if ($gwa === null) {
            $gwa = null;
            $allTermGrades = [1 => null, 2 => null, 3 => null];
            $canEnrollNextYear = false;
        }

        return view('student/grades', [
            'title' => 'My Grades - CSCS SMS',
            'student' => $student,
            'grades' => $grades,
            'schoolYear' => $schoolYear,
            'term' => $term,
            'termAverage' => $termAverage,
            'gwa' => $gwa,
            'canEnrollNextYear' => $canEnrollNextYear,
            'allTermGrades' => $allTermGrades,
        ]);
    }

    public function schedule()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }

        // Get class schedule for student's section
        $schedules = [];
        if ($student['section_id']) {
            $db = \Config\Database::connect();
            try {
                $classSchedules = $db->query("
                    SELECT ts.*, sub.subject_name, 
                           CONCAT(t.first_name, ' ', t.last_name) as teacher_name
                    FROM teacher_schedules ts
                    LEFT JOIN subjects sub ON sub.id = ts.subject_id
                    LEFT JOIN teachers t ON t.id = ts.teacher_id
                    WHERE ts.section_id = ? AND ts.school_year = ?
                    ORDER BY ts.day_of_week, ts.start_time
                ", [$student['section_id'], get_current_school_year()])->getResultArray();
                
                // Organize schedules by day and time
                foreach ($classSchedules as $schedule) {
                    $timeSlot = date('H:i', strtotime($schedule['start_time'])) . '-' . date('H:i', strtotime($schedule['end_time']));
                    $schedules[strtolower($schedule['day_of_week'])][$timeSlot] = $schedule;
                }
            } catch (\Exception $e) {
                $schedules = [];
            }
        }

        return view('student/schedule', [
            'title' => 'Class Schedule - CSCS SMS',
            'student' => $student,
            'schedules' => $schedules
        ]);
    }

    public function announcements()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        $db = \Config\Database::connect();
        $userId = $this->auth->user()->id;
        
        // Use Query Builder instead of raw SQL concatenation to prevent SQL injection
        $builder = $db->table('announcements a');
        $builder->select('a.*, CASE WHEN ar.id IS NOT NULL THEN 1 ELSE 0 END as is_read', false);
        $builder->join('announcement_reads ar', 'ar.announcement_id = a.id AND ar.user_id = ' . (int) $userId, 'left');
        $builder->groupBy('a.id');
        $builder->whereIn('a.target_roles', ['student', 'all']);
        
        if ($student) {
            $gradeLevel = (int) $student['grade_level'];
            $sectionId = (int) ($student['section_id'] ?? 0);
            
            // Use LIKE with escaped value to prevent injection
            $gradeRolePattern = 'grade_' . $gradeLevel;
            $builder->orWhere('a.target_roles', $gradeRolePattern);
            
            // Handle exact match (already added above) - no need for complex FIND_IN_SET
            
            if ($sectionId > 0) {
                $sectionRolePattern = 'section_' . $sectionId;
                $builder->orWhere('a.target_roles', $sectionRolePattern);
            }
        }
        
        $builder->orderBy('a.created_at', 'DESC');
        $announcements = $builder->get()->getResultArray();

        return view('student/announcements', [
            'title' => 'Announcements - CSCS SMS',
            'announcements' => $announcements
        ]);
    }

    public function viewAnnouncement($id)
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $announcementModel = new AnnouncementModel();
        $announcement = $announcementModel->find((int) $id);

        if (!$announcement) {
            return redirect()->to('student/announcements')->with('error', 'Announcement not found.');
        }

        $db = \Config\Database::connect();
        $userId = $this->auth->user()->id;
        $exists = $db->table('announcement_reads')->where(['announcement_id' => (int) $id, 'user_id' => $userId])->get()->getRow();
        if (!$exists) {
            $db->table('announcement_reads')->insert(['announcement_id' => (int) $id, 'user_id' => $userId, 'read_at' => date('Y-m-d H:i:s')]);
        }

        return view('student/announcement_view', [
            'title' => $announcement['title'] . ' - CSCS SMS',
            'announcement' => $announcement
        ]);
    }

    public function updateProfile()
    {
        // Check if user is authenticated
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Student role required.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }

        $rules = [
            'contact_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty',
            'emergency_contact_name' => 'permit_empty|max_length[255]',
            'emergency_contact_number' => 'permit_empty|max_length[20]',
            'emergency_contact_relationship' => 'permit_empty|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentModel = new StudentModel();
        $updateData = [
            'contact_number' => $this->request->getPost('contact_number'),
            'address' => $this->request->getPost('address'),
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
            'emergency_contact_number' => $this->request->getPost('emergency_contact_number'),
            'emergency_contact_relationship' => $this->request->getPost('emergency_contact_relationship')
        ];

        if ($studentModel->update($student['id'], $updateData)) {
            return redirect()->to(base_url('student/profile'))->with('success', 'Profile updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update profile.');
    }

    /**
     * Simple test method without authentication
     */
    public function testSimple()
    {
        return $this->response->setJSON([
            'message' => 'Student Dashboard test route working',
            'timestamp' => date('Y-m-d H:i:s'),
            'controller' => 'Student\Dashboard'
        ]);
    }

    /**
     * Get current term from system settings
     */
    private function getCurrentTerm(): int
    {
        $db = \Config\Database::connect();
        $termSetting = $db->table('system_settings')
            ->where('setting_key', 'current_term')
            ->get()->getRowArray();

        return (int) ($termSetting['setting_value'] ?? 1);
    }

    /**
     * Determine if app is in development/test mode to allow UI preview without auth
     */
    private function isDevTestMode(): bool
    {
        return defined('ENVIRONMENT') && ENVIRONMENT !== 'production';
    }


    public function viewReportCard()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        if (!$this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied.');
        }

        $student = $this->getStudentRecord();
        if (!$student) {
            return redirect()->to(base_url('/'))->with('error', 'Student record not found.');
        }
        
        // Check if student can view report card
        if (($student['can_view_report_card'] ?? 0) == 0) {
            return redirect()->to(base_url('student/grades'))->with('error', 'Report card access has been disabled by your teacher.');
        }
        
        // Get student with section and teacher information
        $studentModel = new StudentModel();
        $student = $studentModel->select('students.*, sections.section_name, sections.adviser_id, CONCAT(teachers.first_name, " ", teachers.last_name) as adviser_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->join('teachers', 'teachers.id = sections.adviser_id', 'left')
            ->where('students.id', $student['id'])
            ->first();

        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();
        
        $schoolYear = get_current_school_year();
        
        // Get subjects assigned to student's section
        $subjects = $student['section_id'] ? $subjectModel->getSectionSubjects($student['section_id']) : [];
        
        // Get grades for all terms
        $grades = [];
        $termAverages = [];

        for ($term = 1; $term <= 3; $term++) {
            $termGrades = [];
            foreach ($subjects as $subject) {
                $grade = $gradeModel->where('student_id', $student['id'])
                    ->where('subject_id', $subject['id'])
                    ->where('school_year', $schoolYear)
                    ->where('term', $term)
                    ->first();

                $termGrades[$subject['id']] = $grade ? $grade['grade'] : null;
            }
            $grades[$term] = $termGrades;

            $validGrades = array_filter($termGrades, function($g) { return $g !== null; });
            $termAverages[$term] = !empty($validGrades) ? array_sum($validGrades) / count($validGrades) : null;
        }

        $validTerms = array_filter($termAverages, function($avg) { return $avg !== null; });
        $finalAverage = !empty($validTerms) ? array_sum($validTerms) / count($validTerms) : null;

        $data = [
            'student' => $student,
            'subjects' => $subjects,
            'grades' => $grades,
            'termAverages' => $termAverages,
            'finalAverage' => $finalAverage,
            'schoolYear' => $schoolYear,
            'reportDate' => date('F j, Y'),
            'logoBase64' => school_logo_base64(),
        ];
        
        $html = view('student/report_card_pdf', $data);
        
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Times');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'LPHS_Report_Card_' . $student['first_name'] . '_' . $student['last_name'] . '_' . date('Y-m-d') . '.pdf';

        return $this->sendPdfInline($dompdf, $filename);
    }
}