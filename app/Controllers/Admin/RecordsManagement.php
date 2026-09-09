<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReportCardRecordModel;
use App\Models\GradeModel;
use App\Models\StudentModel;

class RecordsManagement extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get school year from system settings
        $systemSettingModel = new \App\Models\SystemSettingModel();
        $systemSchoolYear = $systemSettingModel->getSetting('current_school_year');
        
        // Get available school years from sections table
        $schoolYears = $db->query("SELECT DISTINCT school_year FROM sections ORDER BY school_year DESC")->getResultArray();
        
        // Add system school year to list if not present
        if ($systemSchoolYear) {
            $found = false;
            foreach ($schoolYears as $year) {
                if ($year['school_year'] === $systemSchoolYear) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_unshift($schoolYears, ['school_year' => $systemSchoolYear]);
            }
        }
        
        $selectedYear = $this->request->getGet('year') ?? ($systemSchoolYear ?: (count($schoolYears) > 0 ? $schoolYears[0]['school_year'] : get_current_school_year()));
        
        // Get all sections from database - check if any exist for selected year first
        $allSections = $db->query("
            SELECT grade_level, section_name
            FROM sections
            WHERE school_year = ?
            ORDER BY grade_level ASC, section_name ASC
        ", [$selectedYear])->getResultArray();
        
        // If no sections found for selected year, get all sections and use grades school_year
        if (empty($allSections)) {
            $allSections = $db->query("
                SELECT DISTINCT s.grade_level, sec.section_name
                FROM students s
                JOIN sections sec ON sec.id = s.section_id
                JOIN grades g ON g.student_id = s.id
                WHERE g.school_year = ?
                ORDER BY s.grade_level ASC, sec.section_name ASC
            ", [$selectedYear])->getResultArray();
        }
        
        // Initialize structure with all sections
        $groupedRecords = [];
        foreach ($allSections as $section) {
            $grade = $section['grade_level'];
            $sectionName = $section['section_name'];
            $groupedRecords[$grade][$sectionName] = [];
        }
        
        // Get students with grades (only those with sections)
        $students = $db->query("
            SELECT DISTINCT s.id, s.first_name, s.last_name, s.lrn, s.grade_level, sec.section_name
            FROM students s
            JOIN grades g ON g.student_id = s.id
            JOIN sections sec ON sec.id = s.section_id
            WHERE g.school_year = ? AND s.enrollment_status = 'enrolled'
            ORDER BY s.grade_level ASC, sec.section_name ASC, s.last_name ASC
        ", [$selectedYear])->getResultArray();
        
        // Add students to their sections
        foreach ($students as $student) {
            $grade = $student['grade_level'];
            $section = $student['section_name'];
            if (isset($groupedRecords[$grade][$section])) {
                $groupedRecords[$grade][$section][] = $student;
            }
        }
        
        // Remove grades with no sections
        foreach ($groupedRecords as $grade => $sections) {
            if (empty($sections)) {
                unset($groupedRecords[$grade]);
            }
        }
        
        return view('admin/records_management', [
            'title' => 'Records Management - CSCS SMS',
            'schoolYears' => $schoolYears,
            'selectedYear' => $selectedYear,
            'groupedRecords' => $groupedRecords
        ]);
    }

    public function viewSection($gradeLevel, $sectionName)
    {
        $db = \Config\Database::connect();
        $systemSettingModel = new \App\Models\SystemSettingModel();
        $systemSchoolYear = $systemSettingModel->getSetting('current_school_year');
        $schoolYear = $this->request->getGet('year') ?? ($systemSchoolYear ?: get_current_school_year());
        
        // Get students in this section with grades
        $students = $db->query("
            SELECT DISTINCT s.id, s.first_name, s.last_name, s.lrn
            FROM students s
            JOIN grades g ON g.student_id = s.id
            JOIN sections sec ON sec.id = s.section_id
            WHERE s.grade_level = ? AND sec.section_name = ? AND g.school_year = ? AND s.enrollment_status = 'enrolled'
            ORDER BY s.last_name ASC
        ", [$gradeLevel, $sectionName, $schoolYear])->getResultArray();
        
        return view('admin/records_section', [
            'title' => 'Records Management - CSCS SMS',
            'gradeLevel' => $gradeLevel,
            'sectionName' => $sectionName,
            'students' => $students,
            'schoolYear' => $schoolYear
        ]);
    }

    public function archiveCompleted()
    {
        $systemSettingModel = new \App\Models\SystemSettingModel();
        $systemSchoolYear = $systemSettingModel->getSetting('current_school_year');
        $schoolYear = $this->request->getPost('school_year') ?? ($systemSchoolYear ?: get_current_school_year());
        $gradeModel = new GradeModel();
        $studentModel = new StudentModel();
        $recordModel = new ReportCardRecordModel();
        
        // Get all students with grades from the final term of the year
        $db = \Config\Database::connect();
        $studentsWithFinalTerm = $db->query("
            SELECT DISTINCT student_id
            FROM grades
            WHERE school_year = ? AND term = 3
        ", [$schoolYear])->getResultArray();

        $archived = 0;
        foreach ($studentsWithFinalTerm as $row) {
            // Check if already archived
            $exists = $recordModel->where('student_id', $row['student_id'])
                ->where('school_year', $schoolYear)
                ->first();
            
            if (!$exists) {
                if ($recordModel->archiveStudentRecord($row['student_id'], $schoolYear)) {
                    $archived++;
                }
            }
        }
        
        return redirect()->back()->with('success', "Archived {$archived} student records for {$schoolYear}");
    }

    public function viewRecord($studentId)
    {
        $gradeModel = new GradeModel();
        $studentModel = new StudentModel();
        $subjectModel = new \App\Models\SubjectModel();
        $db = \Config\Database::connect();
        
        $systemSettingModel = new \App\Models\SystemSettingModel();
        $systemSchoolYear = $systemSettingModel->getSetting('current_school_year');
        $schoolYear = $this->request->getGet('year') ?? ($systemSchoolYear ?: get_current_school_year());
        
        $student = $db->query("
            SELECT s.*, sec.section_name, sec.adviser_id, CONCAT(t.first_name, ' ', t.last_name) as adviser_name
            FROM students s
            LEFT JOIN sections sec ON sec.id = s.section_id
            LEFT JOIN teachers t ON t.id = sec.adviser_id
            WHERE s.id = ?
        ", [$studentId])->getRowArray();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }
        
        // Get subjects assigned to student's section
        $subjects = $student['section_id'] ? $subjectModel->getSectionSubjects($student['section_id']) : [];
        
        // Get grades for all terms
        $grades = [];
        $termAverages = [];

        for ($t = 1; $t <= 3; $t++) {
            $termGrades = [];
            foreach ($subjects as $subject) {
                $grade = $gradeModel->where('student_id', $studentId)
                    ->where('subject_id', $subject['id'])
                    ->where('school_year', $schoolYear)
                    ->where('term', $t)
                    ->first();
                $termGrades[$subject['id']] = $grade ? $grade['grade'] : null;
            }
            $grades[$t] = $termGrades;

            $validGrades = array_filter($termGrades, function($g) { return $g !== null; });
            $termAverages[$t] = !empty($validGrades) ? array_sum($validGrades) / count($validGrades) : null;
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
        
        try {
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
            
            $filename = 'LPHS_Report_Card_' . $student['first_name'] . '_' . $student['last_name'] . '_' . $schoolYear . '.pdf';

            return $this->sendPdfInline($dompdf, $filename);
        } catch (\Exception $e) {
            log_message('error', 'PDF generation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate report card: ' . $e->getMessage());
        }
    }
}

