<?php
namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\HTTP\ResponseInterface;

class Announcements extends BaseController
{
    public function index()
    {
        $model = new AnnouncementModel();
        $announcements = $model->orderBy('published_at', 'DESC')->findAll(10);
        return view('announcements/index', [
            'title' => 'Announcements',
            'announcements' => $announcements,
        ]);
    }

    public function admin()
    {
        // Placeholder: protect with admin role once auth is integrated
        $model = new AnnouncementModel();
        $announcements = $model->orderBy('created_at', 'DESC')->findAll(50);
        return view('announcements/admin', [
            'title' => 'Manage Announcements',
            'announcements' => $announcements,
        ]);
    }

    public function create()
    {
        return view('announcements/create', ['title' => 'Create Announcement']);
    }

    public function store()
    {
        // Check if this is an AJAX request
        if ($this->request->isAJAX()) {
            return $this->storeAjax();
        }

        $data = $this->request->getPost([
            'title', 'slug', 'body', 'target_roles', 'published_at'
        ]);

        $model = new AnnouncementModel();
        if (!$model->save($data)) {
            return redirect()->back()->with('errors', $model->errors())->withInput();
        }
        return redirect()->to(base_url('announcements/admin'))->with('message', 'Announcement created');
    }

    /**
     * AJAX endpoint to store announcement
     */
    public function storeAjax()
    {
        // Check if user is admin
        if (!auth()->user() || ! is_any_admin()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $rules = [
            'title' => 'required|max_length[255]',
            'body' => 'required',
            'target_roles' => 'required|in_list[all,student,teacher,parent,parent,teacher]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => url_title($this->request->getPost('title'), '-', true),
            'body' => $this->request->getPost('body'),
            'target_roles' => $this->request->getPost('target_roles'),
            'published_at' => $this->request->getPost('published_at') ?: date('Y-m-d H:i:s')
        ];

        $model = new AnnouncementModel();
        if ($model->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement created successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create announcement',
                'errors' => $model->errors()
            ]);
        }
    }

    /**
     * AJAX endpoint to list announcements
     */
    public function listAjax()
    {
        // Check if user is admin
        if (!auth()->user() || ! is_any_admin()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $model = new AnnouncementModel();
        $announcements = $model->orderBy('created_at', 'DESC')->findAll();

        return $this->response->setJSON([
            'success' => true,
            'announcements' => $announcements
        ]);
    }

    /**
     * AJAX endpoint to get single announcement
     */
    public function getAjax($id)
    {
        // Check if user is admin
        if (!auth()->user() || ! is_any_admin()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $model = new AnnouncementModel();
        $announcement = $model->find($id);

        if (!$announcement) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Announcement not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'announcement' => $announcement
        ]);
    }

    /**
     * AJAX endpoint to update announcement
     */
    public function updateAjax($id)
    {
        // Check if user is admin
        if (!auth()->user() || ! is_any_admin()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $model = new AnnouncementModel();
        $announcement = $model->find($id);

        if (!$announcement) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Announcement not found'
            ]);
        }

        $rules = [
            'title' => 'required|max_length[255]',
            'body' => 'required',
            'target_roles' => 'required|in_list[all,student,teacher,parent,parent,teacher]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => url_title($this->request->getPost('title'), '-', true),
            'body' => $this->request->getPost('body'),
            'target_roles' => $this->request->getPost('target_roles'),
            'published_at' => $this->request->getPost('published_at') ?: $announcement['published_at']
        ];

        if ($model->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update announcement',
                'errors' => $model->errors()
            ]);
        }
    }

    /**
     * AJAX endpoint to delete announcement
     */
    public function deleteAjax($id)
    {
        // Check if user is admin
        if (!auth()->user() || ! is_any_admin()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $model = new AnnouncementModel();
        $announcement = $model->find($id);

        if (!$announcement) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Announcement not found'
            ]);
        }

        if ($model->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Announcement deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete announcement'
            ]);
        }
    }
}

