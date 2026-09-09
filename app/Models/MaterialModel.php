<?php
namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'title',
        'description',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'category',
        'uploaded_by',
        'uploaded_by_type',
        'student_id',
        'is_public',
        'show_on_website',
        'sort_order',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getMaterialsByCategory($category = null, $limit = null)
    {
        $builder = $this->select('materials.*, 
                                 CASE 
                                     WHEN materials.uploaded_by_type = "admin" THEN "Admin"
                                     WHEN materials.uploaded_by_type = "teacher" THEN CONCAT(teachers.first_name, " ", teachers.last_name)
                                     ELSE "Unknown"
                                 END as uploader_name')
                        ->join('teachers', 'teachers.user_id = materials.uploaded_by AND materials.uploaded_by_type = "teacher"', 'left')
                        ->orderBy('created_at', 'DESC');

        if ($category) {
            $builder->where('category', $category);
        }

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function getStudentMaterials($studentId)
    {
        return $this->select('materials.*, 
                             CASE 
                                 WHEN materials.uploaded_by_type = "admin" THEN "Admin"
                                 WHEN materials.uploaded_by_type = "teacher" THEN CONCAT(teachers.first_name, " ", teachers.last_name)
                                 ELSE "Unknown"
                             END as uploader_name')
                    ->join('teachers', 'teachers.user_id = materials.uploaded_by AND materials.uploaded_by_type = "teacher"', 'left')
                    ->where('student_id', $studentId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getPublicMaterials($limit = null)
    {
        $builder = $this->select('materials.*, 
                                 CASE 
                                     WHEN materials.uploaded_by_type = "admin" THEN "Admin"
                                     WHEN materials.uploaded_by_type = "teacher" THEN CONCAT(teachers.first_name, " ", teachers.last_name)
                                     ELSE "Unknown"
                                 END as uploader_name')
                        ->join('teachers', 'teachers.user_id = materials.uploaded_by AND materials.uploaded_by_type = "teacher"', 'left')
                        ->where('is_public', 1)
                        ->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Admin-managed materials for the public school website.
     *
     * @return list<object>
     */
    public function getWebsiteMaterials()
    {
        return $this->where('uploaded_by_type', 'admin')
            ->where('show_on_website', 1)
            ->where('is_public', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * All materials uploaded by school administrators.
     *
     * @return list<object>
     */
    public function getAdminMaterials()
    {
        return $this->where('uploaded_by_type', 'admin')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function findWebsiteMaterial(int $id): ?object
    {
        return $this->where('id', $id)
            ->where('uploaded_by_type', 'admin')
            ->where('show_on_website', 1)
            ->where('is_public', 1)
            ->first();
    }
}