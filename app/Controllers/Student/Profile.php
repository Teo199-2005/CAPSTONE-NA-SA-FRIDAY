<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use CodeIgniter\Shield\Models\UserModel;

class Profile extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $studentModel = new StudentModel();
        $user = $this->auth->user();
        $student = $studentModel->where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->to(base_url('student/dashboard'))->with('error', 'Student profile not found.');
        }

        return view('student/profile', [
            'title' => 'My Profile - LPHS SMS',
            'student' => $student
        ]);
    }

    public function update()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $rules = [
            'first_name' => 'required|max_length[100]',
            'middle_name' => 'max_length[100]',
            'last_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[255]',
            'phone' => 'max_length[20]',
            'address' => 'max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentModel = new StudentModel();
        $user = $this->auth->user();
        $student = $studentModel->where('user_id', $user->id)->first();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];

        $updateResult = $studentModel->update($student['id'], $data);
        
        if ($updateResult) {
            // Also update the user's email in users table if email changed
            if ($data['email'] !== $student['email']) {
                $userModel = new UserModel();
                $userModel->update($user->id, ['email' => $data['email']]);
            }
            return redirect()->back()->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update profile.');
        }
    }

    public function changePassword()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        
        // Get current user
        $user = $this->auth->user();
        
        // Verify current password using Shield's method
        try {
            $db = \Config\Database::connect();
            $identity = $db->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->get()
                ->getRow();
                
            if (!$identity) {
                log_message('error', 'No identity found for user ID: ' . $user->id);
                return redirect()->back()->with('error', 'User authentication record not found.');
            }
            
            // Debug logging
            log_message('info', 'Password verification attempt for user ID: ' . $user->id);
            
            if (!password_verify($currentPassword, $identity->secret)) {
                log_message('warning', 'Password verification failed for user ID: ' . $user->id);
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Password verification error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during password verification.');
        }

        // Update password in auth_identities table
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $result = $db->table('auth_identities')
                ->where('user_id', $user->id)
                ->where('type', 'email_password')
                ->update([
                    'secret' => $hashedPassword,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if ($result) {
                log_message('info', 'Password updated successfully for user ID: ' . $user->id);
                return redirect()->back()->with('success', 'Password changed successfully!');
            } else {
                log_message('error', 'Failed to update password for user ID: ' . $user->id);
                return redirect()->back()->with('error', 'Failed to change password.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Password update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating password.');
        }
    }
}