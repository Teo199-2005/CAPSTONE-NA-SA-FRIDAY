<?php
namespace App\Models;

use CodeIgniter\Model;

class ParentModel extends Model
{
    protected $table = 'parents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'first_name', 'middle_name', 'last_name', 'suffix',
        'gender', 'contact_number', 'email', 'address', 'occupation',
        'workplace', 'monthly_income'
    ];

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
    protected $validationRules = [
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'email' => 'required|valid_email|is_unique[parents.email,id,{id}]',
        'gender' => 'permit_empty|in_list[Male,Female]',
        'monthly_income' => 'permit_empty|decimal'
    ];
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

    /**
     * Get full name of parent
     */
    public function getFullName($parent): string
    {
        $name = $parent['first_name'];
        if (!empty($parent['middle_name'])) {
            $name .= ' ' . $parent['middle_name'];
        }
        $name .= ' ' . $parent['last_name'];
        if (!empty($parent['suffix'])) {
            $name .= ' ' . $parent['suffix'];
        }
        return $name;
    }

    /**
     * Get parent with their children
     */
    public function getParentWithChildren($parentId)
    {
        return $this->select('parents.*, students.id as student_id, students.first_name as student_first_name, 
                             students.last_name as student_last_name, students.student_id as student_number,
                             student_parents.relationship')
            ->join('student_parents', 'student_parents.parent_id = parents.id')
            ->join('students', 'students.id = student_parents.student_id')
            ->where('parents.id', $parentId)
            ->findAll();
    }

    /**
     * Get parents of a specific student
     */
    public function getStudentParents($studentId)
    {
        return $this->select('parents.*, student_parents.relationship, student_parents.is_primary, student_parents.is_emergency_contact')
            ->join('student_parents', 'student_parents.parent_id = parents.id')
            ->where('student_parents.student_id', $studentId)
            ->findAll();
    }

    /**
     * Link parent to student
     */
    public function linkToStudent($parentId, $studentId, $relationship = 'Guardian', $isPrimary = false, $isEmergencyContact = false)
    {
        $studentParentModel = new \App\Models\StudentParentModel();
        
        return $studentParentModel->insert([
            'parent_id' => $parentId,
            'student_id' => $studentId,
            'relationship' => $relationship,
            'is_primary' => $isPrimary,
            'is_emergency_contact' => $isEmergencyContact
        ]);
    }
}
