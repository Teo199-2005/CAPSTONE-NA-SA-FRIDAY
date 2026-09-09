<?php
namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\TeacherModel;
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

        $teacherModel = new TeacherModel();
        $teacher = $teacherModel->where('user_id', $this->auth->id())->first();

        if (!$teacher) {
            return redirect()->to(base_url('teacher/dashboard'))->with('error', 'Teacher profile not found.');
        }

        $db = \Config\Database::connect();
        $section = $db->table('sections')
            ->where('adviser_id', $teacher['id'])
            ->get()->getRow();
        
        $department = $section ? 'Grade ' . $section->grade_level : 'Not Assigned';

        return view('teacher/profile', [
            'title' => 'My Profile - CSCS SMS',
            'teacher' => $teacher,
            'department' => $department
        ]);
    }

    public function update()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        // Verify this is a POST request
        if (!$this->request->getMethod() === 'post') {
            return redirect()->back()->with('error', 'Invalid request method.');
        }

        $teacherModel = new TeacherModel();
        $teacher = $teacherModel->where('user_id', $this->auth->id())->first();
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }

        // Get current email to check if it's being changed
        $currentEmail = $teacher['email'];
        $newEmail = $this->request->getPost('email');
        
        // Build validation rules
        $rules = [
            'first_name' => 'required|max_length[100]',
            'middle_name' => 'permit_empty|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'contact_number' => 'permit_empty|max_length[20]',
            'address' => 'permit_empty|max_length[255]'
        ];
        
        // Only add email uniqueness validation if email is being changed
        if ($currentEmail !== $newEmail) {
            $rules['email'] = 'required|valid_email|max_length[255]|is_unique[teachers.email]';
        } else {
            $rules['email'] = 'required|valid_email|max_length[255]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'middle_name' => $this->request->getPost('middle_name') ?: null,
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'contact_number' => $this->request->getPost('contact_number') ?: null,
            'address' => $this->request->getPost('address') ?: null
        ];

        try {
            // Log the update attempt
            log_message('info', 'Attempting to update teacher profile for teacher ID: ' . $teacher['id']);
            log_message('info', 'Update data: ' . json_encode($data));
            
            // Skip model validation and update directly
            $teacherModel->skipValidation(true);
            if ($teacherModel->update($teacher['id'], $data)) {
                log_message('info', 'Teacher profile updated successfully for teacher ID: ' . $teacher['id']);
                return redirect()->back()->with('success', 'Profile updated successfully!');
            } else {
                $teacherModel->skipValidation(false);
                $errors = $teacherModel->errors();
                log_message('error', 'Teacher profile update failed. Validation errors: ' . json_encode($errors));
                
                if (!empty($errors)) {
                    return redirect()->back()->withInput()->with('errors', $errors);
                }
                return redirect()->back()->with('error', 'Failed to update profile.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Profile update exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'An error occurred while updating profile.');
        }
    }

    public function changePassword()
    {
        if (!$this->auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        try {
            $db = \Config\Database::connect();
            $userId = $this->auth->id();
            
            // Get current password hash from auth_identities
            $identity = $db->table('auth_identities')
                ->where('user_id', $userId)
                ->where('type', 'email_password')
                ->get()
                ->getRow();
                
            if (!$identity) {
                return redirect()->back()->with('error', 'User authentication record not found.');
            }

            $currentPassword = $this->request->getPost('current_password');
            
            // Verify current password - check both secret and secret2 fields for compatibility
            $passwordValid = false;
            
            // Check secret field first (new format)
            if ($identity->secret && password_verify($currentPassword, $identity->secret)) {
                $passwordValid = true;
            }
            // Check secret2 field (legacy format)
            elseif (isset($identity->secret2) && $identity->secret2 && password_verify($currentPassword, $identity->secret2)) {
                $passwordValid = true;
            }
            // Fallback: check if it's a plain text match (for very old records)
            elseif ($identity->secret && !str_starts_with($identity->secret, '$2y$') && $currentPassword === $identity->secret) {
                $passwordValid = true;
            }
            
            if (!$passwordValid) {
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }

            // Delete ALL existing auth_identities for this user to prevent multiple valid passwords
            $db->table('auth_identities')
                ->where('user_id', $userId)
                ->delete();
            
            // Create new auth identity with only the new password
            $newPasswordHash = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
            $updated = $db->table('auth_identities')->insert([
                'user_id' => $userId,
                'type' => 'email_password',
                'name' => $identity->name,
                'secret' => $newPasswordHash,
                'secret2' => null,
                'expires' => null,
                'extra' => null,
                'force_reset' => 0,
                'last_used_at' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
                
            if ($updated) {
                return redirect()->back()->with('success', 'Password changed successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to change password.');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Password change error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while changing password.');
        }
    }
}
