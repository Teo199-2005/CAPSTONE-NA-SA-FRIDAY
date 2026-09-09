<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'username',
        'email',
        'first_name',
        'last_name',
        'password',
        'role',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules      = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'first_name' => 'required|min_length[2]|max_length[50]',
        'last_name' => 'required|min_length[2]|max_length[50]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,teacher,student,parent]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
}