<?php
namespace App\Models;

use CodeIgniter\Model;

class SnedCategoryModel extends Model
{
    protected $table = 'sned_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'description', 'display_order', 'is_active', 'section_id'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|max_length[255]',
        'display_order' => 'permit_empty|integer',
    ];

    public function getActiveCategories($sectionId = null)
    {
        $builder = $this->where('is_active', 1);
        if ($sectionId !== null) {
            $builder->where('section_id', $sectionId);
        }
        return $builder->orderBy('display_order', 'ASC')
            ->findAll();
    }

    public function getCategoriesWithFieldCounts($sectionId = null)
    {
        $db = \Config\Database::connect();
        $where = "c.is_active = 1";
        $params = [];
        if ($sectionId !== null) {
            $where .= " AND c.section_id = ?";
            $params[] = $sectionId;
        }
        return $db->query("
            SELECT c.*, COUNT(cf.id) as field_count
            FROM sned_categories c
            LEFT JOIN sned_category_fields cf ON cf.category_id = c.id AND cf.is_active = 1
            WHERE {$where}
            GROUP BY c.id
            ORDER BY c.display_order ASC
        ", $params)->getResultArray();
    }

    public function getCategoryWithFields($categoryId)
    {
        $category = $this->find($categoryId);
        if (!$category) {
            return null;
        }

        $fieldModel = new SnedCategoryFieldModel();
        $category['fields'] = $fieldModel->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('display_order', 'ASC')
            ->findAll();

        return $category;
    }

    public function getAllCategoriesWithFields($sectionId = null)
    {
        $categories = $this->getActiveCategories($sectionId);
        $fieldModel = new SnedCategoryFieldModel();

        foreach ($categories as &$category) {
            $category['fields'] = $fieldModel->where('category_id', $category['id'])
                ->where('is_active', 1)
                ->orderBy('display_order', 'ASC')
                ->findAll();
        }

        return $categories;
    }
}