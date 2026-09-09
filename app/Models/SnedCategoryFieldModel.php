<?php
namespace App\Models;

use CodeIgniter\Model;

class SnedCategoryFieldModel extends Model
{
    protected $table = 'sned_category_fields';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category_id', 'field_name', 'display_order', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'category_id' => 'required|integer',
        'field_name' => 'required|max_length[255]',
    ];

    public function getCategoryFields($categoryId)
    {
        return $this->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('display_order', 'ASC')
            ->findAll();
    }

    public function getNextDisplayOrder($categoryId)
    {
        $max = $this->selectMax('display_order')
            ->where('category_id', $categoryId)
            ->first();
        return ($max['display_order'] ?? 0) + 1;
    }
}