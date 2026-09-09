<?php
namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table            = 'announcements';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'title',
        'slug',
        'body',
        'target_roles', // comma-separated: admin,teacher,student,parent or 'all'
        'published_at',
        'created_by',
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules      = [
        'title' => 'required|min_length[3]|max_length[255]',
        'slug'  => 'required|min_length[3]|max_length[255]',
        'body'  => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}

