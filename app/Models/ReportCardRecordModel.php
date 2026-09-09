<?php
namespace App\Models;

use CodeIgniter\Model;

class ReportCardRecordModel extends Model
{
    protected $table = 'report_card_records';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'student_id', 'school_year', 'grade_level', 'section_id',
        'term1_average', 'term2_average', 'term3_average',
        'final_average', 'status', 'archived_by'
    ];
    protected $useTimestamps = false;

    public function getRecordsBySchoolYear($schoolYear)
    {
        return $this->select('report_card_records.*, students.first_name, students.last_name, students.lrn, sections.section_name')
            ->join('students', 'students.id = report_card_records.student_id')
            ->join('sections', 'sections.id = report_card_records.section_id', 'left')
            ->where('report_card_records.school_year', $schoolYear)
            ->orderBy('report_card_records.grade_level', 'ASC')
            ->orderBy('sections.section_name', 'ASC')
            ->orderBy('students.last_name', 'ASC')
            ->findAll();
    }

    public function archiveStudentRecord($studentId, $schoolYear)
    {
        $gradeModel = new GradeModel();
        $studentModel = new StudentModel();
        
        $student = $studentModel->find($studentId);
        if (!$student) return false;

        $averages = [];
        for ($t = 1; $t <= 3; $t++) {
            $averages["term{$t}_average"] = $gradeModel->getTermAverage($studentId, $schoolYear, $t);
        }
        
        $averages['final_average'] = $gradeModel->getFinalAverage($studentId, $schoolYear);

        $data = array_merge([
            'student_id' => $studentId,
            'school_year' => $schoolYear,
            'grade_level' => $student['grade_level'],
            'section_id' => $student['section_id'],
            'archived_by' => auth()->id()
        ], $averages);

        return $this->insert($data);
    }
}
