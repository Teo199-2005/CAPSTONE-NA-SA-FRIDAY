<?php
namespace App\Models;

use CodeIgniter\Model;

class PasswordResetRequestModel extends Model
{
    protected $table = 'password_reset_requests';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'email',
        'token',
        'expires_at',
        'status',
        'used_at',
        'approved_by',
        'admin_notes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

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
     * Get all password reset requests with user details
     */
    public function getAllRequestsWithDetails()
    {
        return $this->select('password_reset_requests.*, password_reset_requests.email as user_email, students.first_name, students.last_name, students.lrn as student_id')
            ->join('users', 'users.id = password_reset_requests.user_id')
            ->join('students', 'students.user_id = users.id', 'left')
            ->orderBy('password_reset_requests.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Mark expired requests as expired
     */
    public function markExpiredRequests()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))
            ->where('status', 'pending')
            ->set('status', 'expired')
            ->update();
    }

    /**
     * Approve a password reset request
     */
    public function approveRequest($requestId, $adminId, $notes = null)
    {
        return $this->update($requestId, [
            'status' => 'approved',
            'approved_by' => $adminId,
            'admin_notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject a password reset request
     */
    public function rejectRequest($requestId, $adminId, $notes = null)
    {
        return $this->update($requestId, [
            'status' => 'rejected',
            'approved_by' => $adminId,
            'admin_notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}