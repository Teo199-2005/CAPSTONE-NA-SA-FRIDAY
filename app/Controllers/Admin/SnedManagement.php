<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SnedCategoryModel;
use App\Models\SnedCategoryFieldModel;

class SnedManagement extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function getCategories()
    {
        if (!$this->auth->loggedIn() || !user_is_any_admin($this->auth->user())) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $categoryModel = new SnedCategoryModel();
        $categories = $categoryModel->getCategoriesWithFieldCounts();

        return $this->response->setJSON([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    public function addCategory()
    {
        if (!$this->auth->loggedIn() || !user_is_any_admin($this->auth->user())) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        if (!$name) {
            return $this->response->setJSON(['success' => false, 'error' => 'Name is required']);
        }

        $categoryModel = new SnedCategoryModel();
        $categoryModel->insert([
            'name' => $name,
            'description' => $description ?: null,
            'display_order' => $categoryModel->countAll() + 1,
            'is_active' => 1,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Category created']);
    }

    public function deleteCategory($categoryId)
    {
        if (!$this->auth->loggedIn() || !user_is_any_admin($this->auth->user())) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $categoryModel = new SnedCategoryModel();
        $category = $categoryModel->find($categoryId);

        if (!$category) {
            return $this->response->setJSON(['success' => false, 'error' => 'Category not found']);
        }

        // Soft delete: deactivate instead of hard delete
        $categoryModel->update($categoryId, ['is_active' => 0]);

        // Also deactivate all fields
        $fieldModel = new SnedCategoryFieldModel();
        $fieldModel->where('category_id', $categoryId)->set(['is_active' => 0])->update();

        return $this->response->setJSON(['success' => true, 'message' => 'Category deactivated']);
    }

    public function getFields($categoryId)
    {
        if (!$this->auth->loggedIn() || !user_is_any_admin($this->auth->user())) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $fieldModel = new SnedCategoryFieldModel();
        $fields = $fieldModel->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('display_order', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'fields' => $fields,
        ]);
    }

    public function addField()
    {
        if (!$this->auth->loggedIn() || !user_is_any_admin($this->auth->user())) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $categoryId = $this->request->getPost('category_id');
        $fieldName = $this->request->getPost('field_name');

        if (!$categoryId || !$fieldName) {
            return $this->response->setJSON(['success' => false, 'error' => 'Category ID and field name are required']);
        }

        $fieldModel = new SnedCategoryFieldModel();
        $fieldModel->insert([
            'category_id' => $categoryId,
            'field_name' => $fieldName,
            'display_order' => $fieldModel->getNextDisplayOrder($categoryId),
            'is_active' => 1,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Field added']);
    }

    public function deleteField($fieldId)
    {
        if (!$this->auth->loggedIn() || !user_is_any_admin($this->auth->user())) {
            return $this->response->setJSON(['success' => false, 'error' => 'Unauthorized']);
        }

        $fieldModel = new SnedCategoryFieldModel();
        $field = $fieldModel->find($fieldId);

        if (!$field) {
            return $this->response->setJSON(['success' => false, 'error' => 'Field not found']);
        }

        $fieldModel->update($fieldId, ['is_active' => 0]);

        return $this->response->setJSON(['success' => true, 'message' => 'Field deactivated']);
    }
}