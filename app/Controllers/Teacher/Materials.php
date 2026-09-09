<?php
namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use App\Models\StudentModel;

class Materials extends BaseController
{
    protected $auth;
    protected $materialModel;
    protected $studentModel;

    public function __construct()
    {
        $this->auth = auth();
        $this->materialModel = new MaterialModel();
        $this->studentModel = new StudentModel();
    }

    public function index()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        $materials = $this->materialModel->where('uploaded_by', $this->auth->user()->id)
                                        ->where('uploaded_by_type', 'teacher')
                                        ->orderBy('created_at', 'DESC')
                                        ->findAll();
        
        $students = $this->studentModel->where('enrollment_status', 'enrolled')->findAll();

        return view('teacher/materials', [
            'title' => 'Learning Materials - CSCS SMS',
            'materials' => $materials,
            'students' => $students
        ]);
    }

    public function upload()
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        $file = $this->request->getFile('material');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file.');
        }

        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'zip', 'rar'];
        if (!in_array($file->getExtension(), $allowedTypes)) {
            return redirect()->back()->with('error', 'File type not allowed.');
        }

        $uploadPath = WRITEPATH . 'uploads/materials/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileName = $file->getRandomName();
        $file->move($uploadPath, $fileName);

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'file_name' => $file->getName(),
            'file_path' => 'materials/' . $fileName,
            'file_size' => $file->getSize(),
            'file_type' => $file->getExtension(),
            'category' => $this->request->getPost('category'),
            'uploaded_by' => $this->auth->user()->id,
            'uploaded_by_type' => 'teacher',
            'student_id' => $this->request->getPost('student_id') ?: null,
            'is_public' => $this->request->getPost('is_public') ? 1 : 0
        ];

        if ($this->materialModel->insert($data)) {
            return redirect()->back()->with('success', 'Material uploaded successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to upload material.');
        }
    }

    public function delete($id)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        $material = $this->materialModel->where('id', $id)
                                       ->where('uploaded_by', $this->auth->user()->id)
                                       ->where('uploaded_by_type', 'teacher')
                                       ->first();
        
        if (!$material) {
            return redirect()->back()->with('error', 'Material not found or access denied.');
        }

        // Delete file from filesystem
        $filePath = WRITEPATH . 'uploads/' . $material->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($this->materialModel->delete($id)) {
            return redirect()->back()->with('success', 'Material deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete material.');
        }
    }

    public function download($id)
    {
        if (!$this->auth->user()->inGroup('teacher')) {
            return redirect()->to(base_url('/'));
        }

        $material = $this->materialModel->find($id);
        if (!$material) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material not found');
        }

        $filePath = WRITEPATH . 'uploads/' . $material->file_path;
        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        return $this->response->download($filePath, null);
    }
}
