<?php
namespace App\Models;

use CodeIgniter\Model;

class StudentParentModel extends Model
{
    protected $table = 'student_parents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'student_id', 'parent_id', 'relationship', 'is_primary', 'is_emergency_contact'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'is_primary' => 'boolean',
        'is_emergency_contact' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'student_id' => 'required|integer',
        'parent_id' => 'required|integer',
        'relationship' => 'required|in_list[Father,Mother,Guardian,Stepfather,Stepmother,Grandfather,Grandmother,Uncle,Aunt,Other]'
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
}
