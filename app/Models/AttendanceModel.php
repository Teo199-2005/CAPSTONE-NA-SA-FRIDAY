<?php
namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['student_id', 'teacher_id', 'date', 'status', 'remarks'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function getAttendanceByDate($teacherId, $date)
    {
        return $this->select('attendance.*, students.first_name, students.last_name, students.lrn')
                    ->join('students', 'students.id = attendance.student_id')
                    ->where('attendance.teacher_id', $teacherId)
                    ->where('attendance.date', $date)
                    ->findAll();
    }

    public function getAttendanceStats($teacherId, $startDate = null, $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('status, COUNT(*) as count')
                ->where('teacher_id', $teacherId);
        
        if ($startDate) {
            $builder->where('date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('date <=', $endDate);
        }
        
        return $builder->groupBy('status')->get()->getResultArray();
    }

    public function markAttendance($data)
    {
        $existing = $this->where('student_id', $data['student_id'])
                         ->where('date', $data['date'])
                         ->first();
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }
}