<?php
namespace App\Models;

use CodeIgniter\Model;

class SnedGradeModel extends Model
{
    protected $table = 'sned_grades';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_id', 'field_id', 'teacher_id', 'school_year',
        'quarter', 'grade_symbol', 'remarks'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'student_id' => 'required|integer',
        'field_id' => 'required|integer',
        'teacher_id' => 'required|integer',
        'school_year' => 'required|max_length[9]',
        'quarter' => 'required|integer|greater_than[0]|less_than[5]',
        'grade_symbol' => 'permit_empty|in_list[P,AP,D,B,NO/NA]',
    ];

    public function getStudentQuarterGrades($studentId, $schoolYear, $quarter)
    {
        return $this->where('student_id', $studentId)
            ->where('school_year', $schoolYear)
            ->where('quarter', $quarter)
            ->findAll();
    }

    public function getStudentAllGrades($studentId, $schoolYear)
    {
        $grades = $this->where('student_id', $studentId)
            ->where('school_year', $schoolYear)
            ->findAll();

        $grouped = [];
        foreach ($grades as $g) {
            $grouped[$g['field_id']][$g['quarter']] = $g;
        }
        return $grouped;
    }

    public function getStudentFieldGrades($studentId, $fieldId, $schoolYear)
    {
        return $this->where('student_id', $studentId)
            ->where('field_id', $fieldId)
            ->where('school_year', $schoolYear)
            ->orderBy('quarter', 'ASC')
            ->findAll();
    }

    public function upsertGrade($data)
    {
        $existing = $this->where('student_id', $data['student_id'])
            ->where('field_id', $data['field_id'])
            ->where('school_year', $data['school_year'])
            ->where('quarter', $data['quarter'])
            ->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'grade_symbol' => $data['grade_symbol'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'teacher_id' => $data['teacher_id'],
            ]);
        } else {
            return $this->insert($data);
        }
    }

    public function getStudentsWithGrades($studentIds, $schoolYear, $quarter)
    {
        if (empty($studentIds)) {
            return [];
        }

        $db = \Config\Database::connect();
        $builder = $db->table('sned_grades');
        $builder->whereIn('student_id', $studentIds);
        $builder->where('school_year', $schoolYear);
        $builder->where('quarter', $quarter);
        $grades = $builder->get()->getResultArray();

        $grouped = [];
        foreach ($grades as $g) {
            $grouped[$g['student_id']][$g['field_id']] = $g;
        }
        return $grouped;
    }

    public static function getGradeSymbols(): array
    {
        return [
            'P' => 'Proficient',
            'AP' => 'Approaching Proficiency',
            'D' => 'Developing',
            'B' => 'Beginning',
            'NO/NA' => 'Not Observed / Not Applicable',
        ];
    }
}