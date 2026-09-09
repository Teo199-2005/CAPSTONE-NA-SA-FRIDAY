<?php
namespace App\Controllers\Teacher;

use App\Controllers\BaseController;

class Debug extends BaseController
{
    public function index()
    {
        // Restricted: only master admin can access debug info
        helper('admin_access');
        if (! function_exists('is_master_admin') || ! is_master_admin()) {
            return redirect()->to(base_url('/'));
        }

        $userId = auth()->id();
        $user = auth()->user();
        
        $teacherModel = new \App\Models\TeacherModel();
        $teacher = $teacherModel->where('user_id', $userId)->first();
        
        $data = [
            'user_id' => $userId,
            'user_email' => $user->email ?? 'N/A',
            'teacher_found' => $teacher ? 'YES' : 'NO',
            'teacher_id' => $teacher['id'] ?? 'N/A',
            'teacher_name' => $teacher ? ($teacher['first_name'] . ' ' . $teacher['last_name']) : 'N/A'
        ];
        
        return $this->response->setJSON($data);
    }
}