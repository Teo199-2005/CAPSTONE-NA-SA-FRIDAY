<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;

    protected $allowedFields = [
        'user_id', 'type', 'title', 'message', 'data', 'is_read', 'read_at', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected array $casts = [
        'is_read' => 'boolean',
        'data' => '?array',
    ];

    protected $validationRules = [
        'user_id' => 'required|integer',
        'type' => 'required|max_length[50]',
        'title' => 'required|max_length[255]',
        'message' => 'required',
    ];
}


