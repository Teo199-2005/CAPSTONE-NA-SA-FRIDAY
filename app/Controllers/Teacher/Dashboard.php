<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\GradeModel;
use App\Models\StudentModel;
use App\Models\SubjectModel;
use App\Models\AttendanceModel;
use App\Models\SectionModel;

class Dashboard extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    /**
     * Verify that a teacher has a legitimate relationship with a student.
     * Teacher must be either the section adviser OR have a teaching schedule for the student's section.
     */
    private function verifyTeacherStudentRelationship(int $teacherRecordId, int $studentId): bool
    {
        $db = \Config\Database::connect();
        helper('school_year');
        $schoolYear = get_current_school_year();
        
        // Get student's section
        $student = $db->table('students')
            ->select('section_id')
            ->where('id', $studentId)
            ->where('enrollment_status', 'enrolled')
            ->get()
            ->getRow();
            
        if (!$student || !$student->section_id) {
            return false;
        }
        
        $sectionId = (int) $student->section_id;
        
        // Check 1: Is teacher the section adviser?
        $isAdviser = $db->table('sections')
            ->where('id', $sectionId)
            ->where('adviser_id', $teacherRecordId)
            ->countAllResults();
        
        if ($isAdviser > 0) {
            return true;
        }
        
        // Check 2: Does teacher have a teaching schedule for this section?
        $hasSchedule = $db->table('teacher_schedules')
            ->where('teacher_id', $teacherRecordId)
            ->where('section_id', $sectionId)
            ->where('school_year', $schoolYear)
            ->countAllResults();
        
        return $hasSchedule > 0;
    }

    public function index()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        helper('asset');

        $teacherId = $this->auth->id();
        $gradeModel = new GradeModel();
        $studentModel = new StudentModel();
        $subjectModel = new SubjectModel();

        // Get teacher's basic info
        $teacherModel = new \App\Models\TeacherModel();
        $teacher = $teacherModel->where('user_id', $teacherId)->first();

        $db = \Config\Database::connect();

        // Initialize default values
        $myStudents = [];
        $mySubjects = [];
        $recentGrades = [];
        $classAverages = [];
        $gradeDistribution = ['excellent' => 0, 'very_good' => 0, 'good' => 0, 'fair' => 0, 'failing' => 0];
        $quarterPerformance = [0, 0, 0, 0];

        // Determine if teacher has any SNED sections (grade_level = 7)
        $hasSnedSection = false;
        if ($teacher) {
            $hasSnedSection = $db->table('sections')
                ->where('adviser_id', $teacher['id'])
                ->where('grade_level', 7)
                ->where('is_active', 1)
                ->countAllResults() > 0;
        }

        if ($teacher) {
            // Get students from sections where this teacher is adviser
            $myStudents = $studentModel->select('students.id, students.first_name, students.last_name, students.grade_level, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->limit(10)
                ->findAll();
            
            // If no students from advised sections, get students from grades table
            if (empty($myStudents)) {
                $myStudents = $gradeModel->db->query("
                    SELECT DISTINCT s.id, s.first_name, s.last_name, s.grade_level, sec.section_name
                    FROM grades g
                    JOIN students s ON s.id = g.student_id
                    LEFT JOIN sections sec ON sec.id = s.section_id
                    WHERE g.teacher_id = ? AND g.school_year = ?
                    LIMIT 10
                ", [$teacher['id'], get_current_school_year()])->getResultArray();
            }

            // Get subjects taught by this teacher
            $mySubjects = $gradeModel->db->query("
                SELECT DISTINCT sub.id, sub.subject_name, sub.subject_code, sub.grade_level
                FROM grades g
                JOIN subjects sub ON sub.id = g.subject_id
                WHERE g.teacher_id = ? AND g.school_year = ?
            ", [$teacher['id'], get_current_school_year()])->getResultArray();

            // Get recent grades entered by this teacher
            $recentGrades = $gradeModel->db->query("
                SELECT g.*, s.first_name, s.last_name, sub.subject_name, g.created_at
                FROM grades g
                JOIN students s ON s.id = g.student_id
                JOIN subjects sub ON sub.id = g.subject_id
                WHERE g.teacher_id = ?
                ORDER BY g.created_at DESC
                LIMIT 5
            ", [$teacher['id']])->getResultArray();

            // Calculate class averages by subject
            foreach ($mySubjects as $subject) {
                $average = $gradeModel->db->query("
                    SELECT AVG(grade) as avg_grade
                    FROM grades
                    WHERE teacher_id = ? AND subject_id = ? AND school_year = ? AND grade IS NOT NULL
                ", [$teacher['id'], $subject['id'], get_current_school_year()])->getRowArray();

                $classAverages[] = [
                    'subject' => $subject['subject_name'],
                    'average' => $average['avg_grade'] ? round($average['avg_grade'], 2) : 0
                ];
            }

            // Get grade distribution
            $grades = $gradeModel->db->query("
                SELECT grade
                FROM grades
                WHERE teacher_id = ? AND school_year = ? AND grade IS NOT NULL
            ", [$teacher['id'], get_current_school_year()])->getResultArray();

            foreach ($grades as $grade) {
                $gradeValue = $grade['grade'];
                if ($gradeValue >= 90) {
                    $gradeDistribution['excellent']++;
                } elseif ($gradeValue >= 85) {
                    $gradeDistribution['very_good']++;
                } elseif ($gradeValue >= 80) {
                    $gradeDistribution['good']++;
                } elseif ($gradeValue >= 75) {
                    $gradeDistribution['fair']++;
                } else {
                    $gradeDistribution['failing']++;
                }
            }

            // Get quarter performance data
            for ($quarter = 1; $quarter <= 4; $quarter++) {
                $avg = $gradeModel->db->query("
                    SELECT AVG(grade) as avg_grade
                    FROM grades
                    WHERE teacher_id = ? AND school_year = ? AND term = ? AND grade IS NOT NULL
                ", [$teacher['id'], get_current_school_year(), $quarter])->getRowArray();

                $quarterPerformance[$quarter - 1] = $avg['avg_grade'] ? round($avg['avg_grade'], 2) : 0;
            }
        }

        // Featured poster for the teacher dashboard side column
        $teacherPoster = featured_dashboard_poster('teacher');

        return view('teacher/dashboard', [
            'title' => 'Teacher Dashboard - LPHS SMS',
            'teacher' => $teacher,
            'featuredPosterTeacher'    => $teacherPoster['path'],
            'featuredPosterTeacherUrl' => $teacherPoster['url'],
            'myStudents' => $myStudents,
            'mySubjects' => $mySubjects,
            'recentGrades' => $recentGrades,
            'classAverages' => $classAverages,
            'gradeDistribution' => $gradeDistribution,
            'quarterPerformance' => $quarterPerformance,
            'totalStudents' => count($myStudents),
            'totalSubjects' => count($mySubjects),
            'currentQuarter' => $this->getCurrentQuarter(),
            'hasSnedSection' => $hasSnedSection
        ]);
    }

    /**
     * Get current quarter from system settings
     */
    private function getCurrentQuarter()
    {
        $systemSettingModel = new \App\Models\SystemSettingModel();
        if (method_exists($systemSettingModel, 'getCurrentQuarter')) {
            return $systemSettingModel->getCurrentQuarter();
        }
        $month = (int) date('n');
        if ($month >= 6 && $month <= 8) return 1;
        if ($month >= 9 && $month <= 11) return 2;
        if ($month >= 12 || $month <= 3) return 3;
        return 4;
    }

    public function grades()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        $subjectModel = new SubjectModel();
        $gradeModel = new GradeModel();
        $db = \Config\Database::connect();
        
        // Get teacher record for logged-in user
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        $students = [];
        $subjects = [];
        $studentGrades = [];
        $totalPages = 1;
        $currentPage = 1;
        
        if ($teacher) {
            // Get pagination parameters
            $currentPage = (int) ($this->request->getGet('page') ?? 1);
            $perPage = 15;
            $offset = ($currentPage - 1) * $perPage;
            
            // Get total count for pagination
            $totalStudents = $studentModel->select('students.id')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->countAllResults();
            
            $totalPages = ceil($totalStudents / $perPage);
            
            // Get students from advised sections with pagination
            $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.gender, students.grade_level, students.enrollment_status, sections.section_name, sections.grading_type')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->orderBy('students.last_name', 'ASC')
                ->limit($perPage, $offset)
                ->findAll();
            
            // Get subjects for the grade levels of advised students
            if (!empty($students)) {
                $gradeLevels = array_unique(array_column($students, 'grade_level'));
                $subjects = $subjectModel->whereIn('grade_level', $gradeLevels)
                    ->orderBy('grade_level', 'ASC')
                    ->orderBy('subject_name', 'ASC')
                    ->findAll();
                
            // Get existing grades for current term
                $currentTerm = $this->getCurrentQuarter();
                foreach ($students as $student) {
                    foreach ($subjects as $subject) {
                        try {
                            $grade = $gradeModel->where('student_id', $student['id'])
                                ->where('subject_id', $subject['id'])
                                ->where('teacher_id', $teacher['id'])
                                ->where('term', $currentTerm)
                                ->where('school_year', get_current_school_year())
                                ->first();
                        } catch (\Throwable $e) {
                            $grade = null;
                        }
                        
                        $studentGrades[$student['id']][$subject['id']] = $grade;
                    }
                }
            }
        }
        
        // Get grading type for the advisory section
        $sectionGradingType = 'numerical';
        $gradingSymbols = [];
        $advisorySectionId = null;
        if ($teacher) {
            $advisorySection = $db->table('sections')
                ->select('id, grading_type, section_name')
                ->where('adviser_id', $teacher['id'])
                ->where('is_active', 1)
                ->get()
                ->getRowArray();
            
            log_message('info', "SECTION DEBUG - Advisory Section: " . json_encode($advisorySection));
            
            if ($advisorySection) {
                $advisorySectionId = $advisorySection['id'];
                $sectionGradingType = $advisorySection['grading_type'] ?? 'numerical';
                
                // Get grading symbols for non-numerical sections
                if ($sectionGradingType === 'non_numerical' && $advisorySectionId) {
                    $gradingSymbols = $db->table('section_grading_symbols')
                        ->where('section_id', $advisorySectionId)
                        ->where('is_active', 1)
                        ->orderBy('display_order', 'ASC')
                        ->get()
                        ->getResultArray();
                    
                    log_message('info', 'Grading symbols from DB for section ' . $advisorySectionId . ': ' . json_encode($gradingSymbols));
                    
                    // Fallback to default symbols if none found
                    if (empty($gradingSymbols)) {
                        log_message('warning', 'No grading symbols found in DB for section ' . $advisorySectionId . ', using fallback');
                        $gradingSymbols = [
                            ['symbol' => 'P', 'label' => 'Proficient', 'description' => 'The student consistently demonstrates the skill independently.', 'display_order' => 1],
                            ['symbol' => 'AP', 'label' => 'Approaching Proficiency', 'description' => 'The student is developing the skill with minimal assistance.', 'display_order' => 2],
                            ['symbol' => 'D', 'label' => 'Developing', 'description' => 'The student is beginning to develop the skill with guidance.', 'display_order' => 3],
                            ['symbol' => 'B', 'label' => 'Beginning', 'description' => 'The student needs significant support to develop the skill.', 'display_order' => 4],
                            ['symbol' => 'NO/NA', 'label' => 'Not Observed / Not Applicable', 'description' => 'The skill has not been observed or is not applicable at this time.', 'display_order' => 5],
                        ];
                    }
                    
                    log_message('info', 'Final grading symbols to pass to view: ' . json_encode($gradingSymbols));
                } else {
                    log_message('info', "NOT loading grading symbols - gradingType: {$sectionGradingType}, sectionId: " . ($advisorySectionId ?? 'null'));
                }
            }
        }
        
        return view('teacher/grades', [
            'title' => 'Enter Grades - LPHS SMS',
            'students' => $students,
            'subjects' => $subjects,
            'teacher' => $teacher,
            'studentGrades' => $studentGrades,
            'subjectSections' => [],
            'currentQuarter' => $this->getCurrentQuarter(),
            'gradingEnabled' => true,
            'currentTerm' => $this->getCurrentQuarter(),
            'isAdvisory' => true,
            'advisoryData' => [
                'students' => $students,
                'studentGrades' => $studentGrades
            ],
            'subjectSectionsData' => [],
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'sectionGradingType' => $sectionGradingType,
            'gradingSymbols' => $gradingSymbols
        ]);
    }

    public function saveGrades()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $rules = [
            'student_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'quarter' => 'required|integer|greater_than[0]|less_than[5]',
            'grade' => 'required|decimal|greater_than_equal_to[60]|less_than_equal_to[100]',
            'remarks' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please check your input and try again.');
        }

        // Get teacher record
        $teacherModel = new \App\Models\TeacherModel();
        $teacher = $teacherModel->where('user_id', $this->auth->id())->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found.');
        }

        $studentId = (int) $this->request->getPost('student_id');
        
        // SECURITY: Verify teacher-student relationship before allowing grade save
        if (!$this->verifyTeacherStudentRelationship((int) $teacher['id'], $studentId)) {
            return redirect()->back()->with('error', 'You are not authorized to save grades for this student.');
        }

        $data = [
            'student_id' => $studentId,
            'subject_id' => (int) $this->request->getPost('subject_id'),
            'teacher_id' => (int) $teacher['id'],
            'school_year' => get_current_school_year(),
            'quarter' => (int) $this->request->getPost('quarter'),
            'grade' => (float) $this->request->getPost('grade'),
            'remarks' => $this->request->getPost('remarks')
        ];

        $gradeModel = new GradeModel();
        $saved = $gradeModel->upsertGrade($data);

        if (!$saved) {
            return redirect()->back()->withInput()->with('error', 'Failed to save grade.');
        }

        return redirect()->back()->with('success', 'Grade saved successfully!');
    }

    public function saveBulkGrades()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherModel = new \App\Models\TeacherModel();
        $teacher = $teacherModel->where('user_id', $this->auth->id())->first();
        
        log_message('info', 'saveBulkGrades called by teacher ID: ' . ($teacher ? $teacher['id'] : 'NOT FOUND - checking user auth'));
        
        if (!$teacher) {
            log_message('error', 'Teacher record not found for user ID: ' . $this->auth->id());
            return redirect()->back()->with('error', 'Teacher record not found.');
        }

        // Log all POST data for debugging
        log_message('info', 'ALL POST DATA: ' . json_encode($_POST));
        log_message('info', 'ALL RAW POST DATA: ' . file_get_contents('php://input'));
        
        $grades = $this->request->getPost('grades');
        $term = $this->request->getPost('term');  
        $gradingType = $this->request->getPost('grading_type') ?? 'numerical';
        
        log_message('info', 'saveBulkGrades called with grades: ' . json_encode($grades) . ', term: ' . $term . ', gradingType: ' . $gradingType);
        log_message('info', 'Grades type: ' . gettype($grades) . ', Is array: ' . (is_array($grades) ? 'yes' : 'no'));
        
        if (!$grades || !is_array($grades)) {
            log_message('error', 'Invalid grade data - grades missing or empty. Posted data: ' . json_encode($_POST));
            return redirect()->back()->with('error', 'Invalid grade data - no grades submitted.');
        }
        
        if (!$term) {
            log_message('error', 'Invalid grade data - term missing. Posted data: ' . json_encode($_POST));
            return redirect()->back()->with('error', 'Invalid grade data - term missing.');
        }

        // Validate term
        $term = (int) $term;
        if ($term < 1 || $term > 4) {
            return redirect()->back()->with('error', 'Invalid term.');
        }

        $gradeModel = new GradeModel();
        $gradeModel->skipValidation(true);
        $savedCount = 0;
        $skippedCount = 0;
        $processedGrades = [];
        
        foreach ($grades as $studentId => $subjects) {
            log_message('info', "Processing student {$studentId}, subjects: " . json_encode($subjects));
            if (!is_array($subjects)) {
                log_message('warning', "Subjects is not an array for student {$studentId}: " . gettype($subjects));
                continue;
            }
            
            foreach ($subjects as $subjectId => $gradeValue) {
                log_message('info', "Processing subject {$subjectId}, value: '{$gradeValue}', empty: " . (empty($gradeValue) ? 'yes' : 'no'));
                
                // Skip empty values
                if ($gradeValue === '' || $gradeValue === null || $gradeValue === false) {
                    log_message('info', "Skipping empty value for student {$studentId}, subject {$subjectId}");
                    continue;
                }
                
                $processedGrades[] = ['student_id' => $studentId, 'subject_id' => $subjectId, 'value' => $gradeValue];
                
                $data = [
                    'student_id' => (int) $studentId,
                    'subject_id' => (int) $subjectId,
                    'teacher_id' => (int) $teacher['id'],
                    'school_year' => get_current_school_year(),
                    'term' => $term,
                    'remarks' => null
                ];
                
                // For non-numerical grading, store the symbol as-is
                // For numerical grading, validate and convert to float
                if ($gradingType === 'non_numerical') {
                    // Validate that the symbol is one of the allowed values
                    $allowedSymbols = ['P', 'AP', 'D', 'B', 'NO/NA'];
                    if (!in_array($gradeValue, $allowedSymbols)) {
                        log_message('warning', "Invalid symbol received: {$gradeValue}");
                        $skippedCount++;
                        continue;
                    }
                    $data['grade'] = $gradeValue;
                } else {
                    // Numerical grading - validate numeric value
                    if (!is_numeric($gradeValue)) {
                        log_message('warning', "Non-numeric grade in numerical mode: {$gradeValue}");
                        $skippedCount++;
                        continue;
                    }
                    $gradeFloat = (float) $gradeValue;
                    if ($gradeFloat < 0 || $gradeFloat > 100) {
                        log_message('warning', "Grade out of range: {$gradeFloat}");
                        $skippedCount++;
                        continue;
                    }
                    $data['grade'] = $gradeFloat;
                }
                
                if ($gradeModel->upsertGrade($data)) {
                    $savedCount++;
                } else {
                    $skippedCount++;
                }
            }
        }
        
        log_message('info', 'Final counts - Saved: ' . $savedCount . ', Skipped: ' . $skippedCount . ', Processed: ' . count($processedGrades));
        log_message('info', 'Processed grades details: ' . json_encode($processedGrades));
        
        if ($savedCount > 0) {
            return redirect()->back()->with('success', "Successfully saved {$savedCount} grade(s).");
        } else {
            $debugInfo = [
                'total_processed' => count($processedGrades),
                'saved' => $savedCount,
                'skipped' => $skippedCount,
                'term' => $term,
                'grading_type' => $gradingType,
                'first_few_grades' => array_slice($processedGrades, 0, 3),
                'all_post_grades' => is_array($grades) ? array_slice($grades, 0, 5) : 'not_array'
            ];
            return redirect()->back()->with('error', 'No grades saved. Details: ' . json_encode($debugInfo));
        }
    }

    public function students()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        $db = \Config\Database::connect();

        $teacher = $teacherModel->where('user_id', $teacherId)->first();

        $advisoryStudents = [];
        $advisorySection = null;
        $subjectSections = [];

        if ($teacher) {
            $advisorySection = $db->table('sections')
                ->select('id, section_name, grade_level')
                ->where('adviser_id', $teacher['id'])
                ->get()->getRowArray();

            if ($advisorySection) {
                $advisoryStudents = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.gender, students.grade_level, students.enrollment_status, sections.section_name, sections.grading_type')
                    ->join('sections', 'sections.id = students.section_id', 'left')
                    ->where('students.section_id', $advisorySection['id'])
                    ->where('students.enrollment_status', 'enrolled')
                    ->orderBy('students.last_name', 'ASC')
                    ->findAll();
            }

            $teachingSections = $db->query(
                "SELECT DISTINCT s.id, s.section_name, s.grade_level, sub.subject_name
                 FROM teacher_schedules ts
                 JOIN sections s ON s.id = ts.section_id
                 JOIN subjects sub ON sub.id = ts.subject_id
                 WHERE ts.teacher_id = ? AND (s.adviser_id != ? OR s.adviser_id IS NULL)
                 ORDER BY s.grade_level, s.section_name",
                [$teacher['id'], $teacher['id']]
            )->getResultArray();

            $sectionGroups = [];
            foreach ($teachingSections as $ts) {
                $sectionKey = $ts['id'];
                if (!isset($sectionGroups[$sectionKey])) {
                    $sectionGroups[$sectionKey] = [
                        'id' => $ts['id'],
                        'section_name' => $ts['section_name'],
                        'grade_level' => $ts['grade_level'],
                        'subjects' => []
                    ];
                }
                $sectionGroups[$sectionKey]['subjects'][] = $ts['subject_name'];
            }

            foreach ($sectionGroups as $section) {
                $sectionStudents = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.gender, students.grade_level, students.enrollment_status, sections.section_name, sections.grading_type')
                    ->join('sections', 'sections.id = students.section_id', 'left')
                    ->where('students.section_id', $section['id'])
                    ->where('students.enrollment_status', 'enrolled')
                    ->orderBy('students.last_name', 'ASC')
                    ->findAll();

                $subjectSections[] = [
                    'section' => $section,
                    'students' => $sectionStudents
                ];
            }
        }

        return view('teacher/students', [
            'title' => 'My Students - LPHS SMS',
            'advisoryStudents' => $advisoryStudents,
            'advisorySection' => $advisorySection,
            'subjectSections' => $subjectSections,
            'teacher' => $teacher
        ]);
    }

    public function schedule()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $scheduleModel = new \App\Models\TeacherScheduleModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $schedules = [];
        
        if ($teacher) {
            $schedules = $scheduleModel->getTeacherSchedule($teacher['id']);
        }
        
        $db = \Config\Database::connect();
        $sections = $db->table('sections')->select('id, section_name, grade_level')->orderBy('grade_level', 'ASC')->orderBy('section_name', 'ASC')->get()->getResultArray();
        $subjects = [];
        if ($teacher) {
            $subjects = $db->query('SELECT DISTINCT sub.id, sub.subject_name FROM subjects sub JOIN teacher_schedules ts ON ts.subject_id = sub.id WHERE ts.teacher_id = ?', [$teacher['id']])->getResultArray();
        }

        return view('teacher/schedule_view', [
            'title' => 'My Schedule - LPHS SMS',
            'teacher' => $teacher,
            'schedules' => $schedules,
            'subjects' => $subjects,
            'sections' => $sections
        ]);
    }

    public function manageSchedule()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $scheduleModel = new \App\Models\TeacherScheduleModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $schedules = [];
        
        if ($teacher) {
            $schedules = $scheduleModel->getTeacherSchedule($teacher['id']);
        }
        
        $db = \Config\Database::connect();
        $sections = $db->table('sections')->select('id, section_name, grade_level')->orderBy('grade_level', 'ASC')->orderBy('section_name', 'ASC')->get()->getResultArray();
        $subjects = [];
        if ($teacher) {
            $subjects = $db->query('SELECT DISTINCT sub.id, sub.subject_name FROM subjects sub JOIN teacher_schedules ts ON ts.subject_id = sub.id WHERE ts.teacher_id = ?', [$teacher['id']])->getResultArray();
        }

        return view('teacher/schedule', [
            'title' => 'Manage Schedule - LPHS SMS',
            'teacher' => $teacher,
            'schedules' => $schedules,
            'subjects' => $subjects,
            'sections' => $sections
        ]);
    }

    public function saveSchedule()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found.');
        }

        $scheduleData = $this->request->getPost('schedule');
        $scheduleModel = new \App\Models\TeacherScheduleModel();
        
        // Delete existing schedules
        $db = \Config\Database::connect();
        $db->table('teacher_schedules')->where('teacher_id', $teacher['id'])->delete();
        
        $saved = 0;
        if (!empty($scheduleData)) {
            foreach ($scheduleData as $day => $slots) {
                foreach ($slots as $timeSlot => $data) {
                    if (!empty($data['subject_id']) && !empty($data['section_id'])) {
                        $scheduleModel->insert([
                            'teacher_id' => $teacher['id'],
                            'section_id' => (int) $data['section_id'],
                            'subject_id' => (int) $data['subject_id'],
                            'day' => $day,
                            'time_slot' => $timeSlot,
                            'room' => $data['room'] ?? ''
                        ]);
                        $saved++;
                    }
                }
            }
        }
        
        return redirect()->to(base_url('teacher/schedule'))->with('success', "Schedule saved! {$saved} entries added.");
    }

    public function attendance()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        $attendanceModel = new AttendanceModel();
        
        // Get teacher record for logged-in user
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $students = [];
        $attendanceData = [];
        $selectedDate = $this->request->getGet('date') ?? date('Y-m-d');
        
        if ($teacher) {
            // Get students from advised sections
            $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.grade_level, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->orderBy('students.last_name', 'ASC')
                ->findAll();
            
            // Get attendance for selected date
            $attendanceRecords = $attendanceModel->getAttendanceByDate($teacher['id'], $selectedDate);
            foreach ($attendanceRecords as $record) {
                $attendanceData[$record['student_id']] = $record;
            }
        }
        
        return view('teacher/attendance', [
            'title' => 'Student Attendance - LPHS SMS',
            'students' => $students,
            'teacher' => $teacher,
            'attendanceData' => $attendanceData,
            'selectedDate' => $selectedDate
        ]);
    }

    public function saveAttendance()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $attendanceModel = new AttendanceModel();
        
        // Get teacher record for logged-in user
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found.');
        }
        
        $date = $this->request->getPost('date');
        $attendanceData = $this->request->getPost('attendance');
        
        if (!$date || !$attendanceData) {
            return redirect()->back()->with('error', 'Invalid attendance data.');
        }
        
        $saved = 0;
        foreach ($attendanceData as $studentId => $status) {
            $studentIdInt = (int) $studentId;
            
            // SECURITY: Verify teacher-student relationship before recording attendance
            if (!$this->verifyTeacherStudentRelationship((int) $teacher['id'], $studentIdInt)) {
                continue; // Skip students the teacher doesn't have a relationship with
            }
            
            $data = [
                'student_id' => $studentIdInt,
                'teacher_id' => (int) $teacher['id'],
                'date' => $date,
                'status' => $status,
                'remarks' => $this->request->getPost('remarks')[$studentId] ?? null
            ];
            
            if ($attendanceModel->markAttendance($data)) {
                $saved++;
            }
        }
        
        return redirect()->back()->with('success', "Attendance saved for {$saved} students.");
    }

    public function attendanceHistory()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $attendanceModel = new AttendanceModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher record not found']);
        }
        
        $fromDate = $this->request->getGet('from');
        $toDate = $this->request->getGet('to');
        
        if (!$fromDate || !$toDate) {
            return $this->response->setJSON(['success' => false, 'error' => 'From and to dates are required']);
        }
        
        // Get attendance history
        $db = \Config\Database::connect();
        $history = $db->query("
            SELECT a.date, a.status, a.remarks, 
                   CONCAT(s.first_name, ' ', s.last_name) as student_name,
                   s.lrn
            FROM attendance a
            JOIN students s ON s.id = a.student_id
            WHERE a.teacher_id = ? AND a.date BETWEEN ? AND ?
            ORDER BY a.date DESC, s.last_name ASC
        ", [$teacher['id'], $fromDate, $toDate])->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'history' => $history
        ]);
    }

    public function attendanceHistoryPage()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $students = [];
        
        if ($teacher) {
            // Get students from advised sections
            $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.grade_level, sections.section_name')
                ->join('sections', 'sections.id = students.section_id', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->where('students.enrollment_status', 'enrolled')
                ->orderBy('students.last_name', 'ASC')
                ->findAll();
        }
        
        return view('teacher/attendance_history', [
            'title' => 'Attendance History - LPHS SMS',
            'students' => $students,
            'teacher' => $teacher
        ]);
    }

    public function sections()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $sectionModel = new \App\Models\SectionModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        $sections = [];
        
        if ($teacher) {
            // Get sections where this teacher is adviser
            $sections = $sectionModel->select('sections.*, COUNT(students.id) as current_enrollment')
                ->join('students', 'students.section_id = sections.id AND students.enrollment_status = "enrolled"', 'left')
                ->where('sections.adviser_id', $teacher['id'])
                ->groupBy('sections.id')
                ->orderBy('sections.grade_level', 'ASC')
                ->orderBy('sections.section_name', 'ASC')
                ->findAll();
        }
        
        return view('teacher/sections', [
            'title' => 'My Sections - LPHS SMS',
            'sections' => $sections,
            'teacher' => $teacher
        ]);
    }

    public function getSectionStudents($sectionId)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $sectionModel = new \App\Models\SectionModel();
        $studentModel = new StudentModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher record not found']);
        }
        
        // Verify teacher is adviser of this section
        $section = $sectionModel->where('id', $sectionId)
            ->where('adviser_id', $teacher['id'])
            ->first();
        
        if (!$section) {
            return $this->response->setJSON(['success' => false, 'error' => 'Section not found or not assigned to you']);
        }
        
        // Get students in this section
        $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name, students.enrollment_status, students.created_at')
            ->where('students.section_id', $sectionId)
            ->where('students.enrollment_status', 'enrolled')
            ->orderBy('students.last_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'students' => $students
        ]);
    }

    public function getUnassignedStudents($gradeLevel)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher record not found']);
        }
        
        // Get unassigned students for this grade level
        $students = $studentModel->select('students.id, students.lrn, students.first_name, students.last_name')
            ->where('students.grade_level', $gradeLevel)
            ->where('students.enrollment_status', 'enrolled')
            ->where('(students.section_id IS NULL OR students.section_id = 0)')
            ->orderBy('students.last_name', 'ASC')
            ->findAll();
        
        if (empty($students)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No unassigned students found for Grade ' . $gradeLevel
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'students' => $students
        ]);
    }

    public function assignStudentsToSection($sectionId)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $sectionModel = new \App\Models\SectionModel();
        $studentModel = new StudentModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return $this->response->setJSON(['success' => false, 'error' => 'Teacher record not found']);
        }
        
        // Verify teacher is adviser of this section
        $section = $sectionModel->where('id', $sectionId)
            ->where('adviser_id', $teacher['id'])
            ->first();
        
        if (!$section) {
            return $this->response->setJSON(['success' => false, 'error' => 'Section not found or not assigned to you']);
        }
        
        $input = $this->request->getJSON(true);
        $studentIds = $input['student_ids'] ?? [];
        
        if (empty($studentIds)) {
            return $this->response->setJSON(['success' => false, 'error' => 'No students selected']);
        }
        
        $assigned = 0;
        foreach ($studentIds as $studentId) {
            if ($studentModel->update($studentId, ['section_id' => $sectionId])) {
                $assigned++;
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => "Successfully assigned {$assigned} student(s) to the section"
        ]);
    }

    public function generateReportCard($studentId)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }
        
        $teacherId = $this->auth->id();
        $teacherModel = new \App\Models\TeacherModel();
        $studentModel = new StudentModel();
        $gradeModel = new GradeModel();
        $subjectModel = new SubjectModel();
        
        // Get teacher record
        $teacher = $teacherModel->where('user_id', $teacherId)->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found');
        }
        
        // Get student and verify teacher has access
        $student = $studentModel->select('students.*, sections.section_name')
            ->join('sections', 'sections.id = students.section_id', 'left')
            ->where('students.id', $studentId)
            ->where('sections.adviser_id', $teacher['id'])
            ->first();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found or not in your section');
        }
        
        // Get subjects for student's grade level
        $subjects = $subjectModel->where('grade_level', $student['grade_level'])
            ->where('is_active', true)
            ->findAll();
        
        // Get grades for current school year
        $schoolYear = get_current_school_year();
        $grades = [];
        $quarterAverages = [];
        
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $quarterGrades = [];
            foreach ($subjects as $subject) {
                $grade = $gradeModel->where('student_id', $studentId)
                    ->where('subject_id', $subject['id'])
                    ->where('school_year', $schoolYear)
                    ->where('term', $quarter)
                    ->first();
                
                $quarterGrades[$subject['id']] = $grade ? $grade['grade'] : null;
            }
            $grades[$quarter] = $quarterGrades;
            
            // Calculate quarter average - only sum numeric grades
            $numericGrades = array_filter($quarterGrades, function($g) { return $g !== null && is_numeric($g); });
            $quarterAverages[$quarter] = !empty($numericGrades) ? array_sum($numericGrades) / count($numericGrades) : 0;
        }
        
        // Calculate final average - filter out non-numeric values
        $validQuarters = array_filter($quarterAverages, function($avg) { return is_numeric($avg) && $avg > 0; });
        $finalAverage = !empty($validQuarters) ? array_sum($validQuarters) / count($validQuarters) : 0;
        
        $data = [
            'student' => $student,
            'teacher' => $teacher,
            'subjects' => $subjects,
            'grades' => $grades,
            'quarterAverages' => $quarterAverages,
            'finalAverage' => $finalAverage,
            'schoolYear' => $schoolYear,
            'reportDate' => date('F j, Y')
        ];
        
        $html = view('teacher/report_card_pdf', $data);
        
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
        $dompdf->stream($filename, ['Attachment' => false]);
    }
}