<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\PasswordResetRequestModel;
use CodeIgniter\Shield\Models\UserModel;

class PasswordReset extends BaseController
{
    public function index()
    {
        return view('auth/forgot_password', [
            'title' => 'Reset Password - CSCS SMS'
        ]);
    }

    public function verify()
    {
        $identifier = $this->request->getPost('identifier');
        
        if (!$identifier) {
            return redirect()->back()->with('error', 'Please enter your PRC License Number or LRN.');
        }

        // Check if it's a teacher (PRC License) or student (LRN)
        $teacherModel = new TeacherModel();
        $studentModel = new StudentModel();
        $userModel = new UserModel();
        
        $user = null;
        $userType = null;
        
        // Check teachers first
        $teacher = $teacherModel->where('license_number', $identifier)->first();
        if ($teacher) {
            $user = $userModel->find($teacher['user_id']);
            $userType = 'teacher';
        } else {
            // Check students
            $student = $studentModel->where('lrn', $identifier)->first();
            if ($student) {
                if ($student['user_id']) {
                    $user = $userModel->find($student['user_id']);
                    $userType = 'student';
                } else {
                    // Student exists but no user account - create one
                    $userData = [
                        'email' => $student['email'] ?: $student['lrn'] . '@student.lphs.edu',
                        'password' => 'temp123',
                        'active' => 1
                    ];
                    
                    $userId = $userModel->insert($userData);
                    if ($userId) {
                        // Update student with user_id
                        $studentModel->update($student['id'], ['user_id' => $userId]);
                        $user = $userModel->find($userId);
                        $userType = 'student';
                    }
                }
            }
        }
        
        if (!$user) {
            return redirect()->back()->with('error', 'No account found with that PRC License Number or LRN.');
        }

        // Get user ID safely
        $userId = null;
        if (is_object($user)) {
            $userId = $user->id ?? null;
        } elseif (is_array($user)) {
            $userId = $user['id'] ?? null;
        }
        
        if (!$userId) {
            return redirect()->back()->with('error', 'Unable to process password reset request.');
        }

        // Create password reset request
        $resetRequestModel = new PasswordResetRequestModel();
        $token = bin2hex(random_bytes(32));
        
        // Get user email - prioritize student/teacher email over auth_identities
        $userEmail = null;
        
        if ($userType === 'student' && $student) {
            $userEmail = $student['email'] ?: $identifier . '@student.lphs.edu';
        } elseif ($userType === 'teacher' && $teacher) {
            $userEmail = $teacher['email'] ?: $identifier . '@teacher.lphs.edu';
        }
        
        if (!$userEmail) {
            // Try to get email from auth_identities
            $db = \Config\Database::connect();
            $identity = $db->table('auth_identities')
                ->where('user_id', $userId)
                ->where('type', 'email_password')
                ->get()
                ->getRowArray();
            $userEmail = $identity['secret'] ?? $identifier . '@student.lphs.edu';
        }
        
        $resetData = [
            'user_id' => $userId,
            'email' => $userEmail,
            'token' => $token,
            'status' => 'pending',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours')),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $resetRequestModel->insert($resetData);

        return redirect()->to(base_url('forgot-password'))->with('success', 'Password reset request submitted. Please wait for admin approval.');
    }

    public function adminApprove($resetId)
    {
        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')->where('id', $resetId)->get()->getRowArray();
        
        if (!$reset || $reset['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Invalid or already processed reset request.');
        }

        // Update status to approved
        $db->table('password_resets')->where('id', $resetId)->update([
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success' => true,
            'resetId' => $resetId,
            'token' => $reset['token']
        ]);
    }

    public function changePassword()
    {
        try {
            $resetId = $this->request->getPost('reset_id');
            $newPassword = $this->request->getPost('new_password');
            $confirmPassword = $this->request->getPost('confirm_password');

            log_message('info', 'Password change attempt for reset ID: ' . $resetId);

            if (!$resetId || !$newPassword || !$confirmPassword) {
                return redirect()->back()->with('error', 'All fields are required.');
            }

            if ($newPassword !== $confirmPassword) {
                return redirect()->back()->with('error', 'Passwords do not match.');
            }

            if (strlen($newPassword) < 6) {
                return redirect()->back()->with('error', 'Password must be at least 6 characters long.');
            }

            $db = \Config\Database::connect();
            $reset = $db->table('password_resets')->where('id', $resetId)->get()->getRowArray();
            
            if (!$reset) {
                log_message('error', 'Reset request not found for ID: ' . $resetId);
                return redirect()->back()->with('error', 'Reset request not found.');
            }

            log_message('info', 'Found reset request for user ID: ' . $reset['user_id']);

            // Update password directly in auth_identities table
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Check if auth_identity exists
            $existingIdentity = $db->table('auth_identities')
                ->where('user_id', $reset['user_id'])
                ->where('type', 'email_password')
                ->get()
                ->getRowArray();
            
            if ($existingIdentity) {
                // Update existing password
                $saved = $db->table('auth_identities')
                    ->where('user_id', $reset['user_id'])
                    ->where('type', 'email_password')
                    ->update([
                        'secret2' => $hashedPassword,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            } else {
                // Get user email for new identity
                $userRecord = $db->table('users')->where('id', $reset['user_id'])->get()->getRowArray();
                $userEmail = $userRecord['email'] ?? $reset['identifier'] . '@student.lphs.edu';
                
                // Create new auth identity
                $saved = $db->table('auth_identities')->insert([
                    'user_id' => $reset['user_id'],
                    'type' => 'email_password',
                    'name' => '',
                    'secret' => $userEmail,
                    'secret2' => $hashedPassword,
                    'expires' => null,
                    'extra' => null,
                    'force_reset' => 0,
                    'last_used_at' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            if (!$saved) {
                log_message('error', 'Failed to save password for user ID: ' . $reset['user_id']);
                log_message('error', 'Validation errors: ' . json_encode($userModel->errors()));
                return redirect()->back()->with('error', 'Failed to update password. Please try again.');
            }

            // Mark reset as used
            $db->table('password_resets')->where('id', $resetId)->update([
                'status' => 'used'
            ]);

            return redirect()->to('admin/password-resets')->with('success', 'Password changed successfully! The user can now login with their new password.');
            
        } catch (\Exception $e) {
            log_message('error', 'Password change error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while changing the password. Please try again.');
        }
    }

    public function changePage($resetId)
    {
        $db = \Config\Database::connect();
        $reset = $db->table('password_resets pr')
            ->select('pr.*, u.email')
            ->join('users u', 'u.id = pr.user_id')
            ->where('pr.id', $resetId)
            ->get()
            ->getRowArray();
        
        if (!$reset) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Reset request not found');
        }
        
        return view('admin/password_reset_change', [
            'title' => 'Change Password - CSCS SMS',
            'reset' => $reset
        ]);
    }

    public function adminList()
    {
        // Redirect to the new admin password resets page
        return redirect()->to('admin/password-resets');
    }
}
