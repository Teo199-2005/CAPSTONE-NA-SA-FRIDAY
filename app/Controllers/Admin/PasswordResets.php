<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PasswordResetRequestModel;
use CodeIgniter\Shield\Models\UserModel;

class PasswordResets extends BaseController
{
    protected $resetRequestModel;
    protected $userModel;

    public function __construct()
    {
        $this->resetRequestModel = model(PasswordResetRequestModel::class);
        $this->userModel = model(UserModel::class);
    }

    /**
     * Display password reset requests
     */
    public function index()
    {
        try {
            $this->resetRequestModel->markExpiredRequests();
            $requests = $this->resetRequestModel->getAllRequestsWithDetails();
        } catch (\Exception $e) {
            $requests = [];
        }
        
        return view('admin/password_resets', [
            'title' => 'Password Reset Requests - CSCS SMS',
            'requests' => $requests,
            'table_missing' => false
        ]);
    }

    /**
     * Approve a password reset request
     */
    public function approve($requestId)
    {
        // Check if user is admin
        try {
            if (!auth()->loggedIn() || ! is_any_admin()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $notes = $this->request->getPost('notes');
        $adminId = auth()->user()->id;

        // Get the request details first
        $request = $this->resetRequestModel
            ->select('password_reset_requests.*, students.id as student_id')
            ->join('users', 'users.id = password_reset_requests.user_id')
            ->join('students', 'students.user_id = users.id', 'left')
            ->find($requestId);

        if (!$request) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Request not found']);
        }

        $success = $this->resetRequestModel->approveRequest($requestId, $adminId, $notes);

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'redirect' => base_url("admin/password-resets/change/{$requestId}"),
                'message' => 'Password reset request approved. Redirecting to change password...',
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to approve password reset request.',
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Reject a password reset request
     */
    public function reject($requestId)
    {
        // Check if user is admin
        try {
            if (!auth()->loggedIn() || ! is_any_admin()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $notes = $this->request->getPost('notes');
        $adminId = auth()->user()->id;

        $success = $this->resetRequestModel->rejectRequest($requestId, $adminId, $notes);

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password reset request rejected.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to reject password reset request.',
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Get request details for modal
     */
    public function getRequestDetails($requestId)
    {
        // Check if user is admin
        try {
            if (!auth()->loggedIn() || ! is_any_admin()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $request = $this->resetRequestModel
            ->select('password_reset_requests.*, users.email as user_email, students.first_name, students.last_name, students.student_id, students.contact_number, admin_users.email as approved_by_email')
            ->join('users', 'users.id = password_reset_requests.user_id')
            ->join('students', 'students.user_id = users.id', 'left')
            ->join('users as admin_users', 'admin_users.id = password_reset_requests.approved_by', 'left')
            ->find($requestId);

        if (!$request) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Request not found']);
        }

        return $this->response->setJSON($request);
    }

    /**
     * Generate reset link for approved request
     */
    public function generateResetLink($requestId)
    {
        // Check if user is admin
        try {
            if (!auth()->loggedIn() || ! is_any_admin()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $request = $this->resetRequestModel->find($requestId);

        if (!$request || $request['status'] !== 'approved') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Request not found or not approved']);
        }

        $resetLink = base_url('reset-password/' . $request['token']);

        return $this->response->setJSON([
            'success' => true,
            'reset_link' => $resetLink,
            'expires_at' => $request['expires_at']
        ]);
    }

    /**
     * Get count of pending password reset requests
     */
    public function getCount()
    {
        // Check if user is admin
        try {
            if (!auth()->loggedIn() || ! is_any_admin()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        try {
            // Mark expired requests first
            $this->resetRequestModel->markExpiredRequests();

            // Get count of pending requests
            $count = $this->resetRequestModel
                ->where('status', 'pending')
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->countAllResults();

            return $this->response->setJSON(['count' => $count]);
        } catch (\Exception $e) {
            // If table doesn't exist, return 0 count
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                return $this->response->setJSON(['count' => 0]);
            }
            throw $e;
        }
    }

    /**
     * Approve all pending password reset requests
     */
    public function approveAll()
    {
        // Check if user is admin
        try {
            if (!auth()->loggedIn() || ! is_any_admin()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $adminId = auth()->user()->id;
        
        $success = $this->resetRequestModel
            ->where('status', 'pending')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->set([
                'status' => 'approved',
                'approved_by' => $adminId,
                'admin_notes' => 'Bulk approved by admin',
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update();

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All pending requests approved successfully.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to approve all requests.',
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Reject all pending password reset requests
     */
    public function rejectAll()
    {
        // Check if user is admin
        try {
            if (!auth()->loggedIn() || ! is_any_admin()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $adminId = auth()->user()->id;
        
        $success = $this->resetRequestModel
            ->where('status', 'pending')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->set([
                'status' => 'rejected',
                'approved_by' => $adminId,
                'admin_notes' => 'Bulk rejected by admin',
                'updated_at' => date('Y-m-d H:i:s')
            ])
            ->update();

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All pending requests rejected successfully.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to reject all requests.',
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Delete a password reset request
     */
    public function delete($requestId)
    {
        // Check if user is admin
        try {
            if (!auth()->loggedIn() || ! is_any_admin()) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $success = $this->resetRequestModel->delete($requestId);

        if ($success) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password reset request deleted successfully.',
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Failed to delete password reset request.',
            'csrf_hash' => csrf_hash()
        ]);
    }

    /**
     * Show password change page for approved request
     */
    public function change($requestId)
    {
        $db = \Config\Database::connect();
        $request = $db->table('password_reset_requests')
            ->where('id', $requestId)
            ->get()
            ->getRowArray();
        
        if (!$request) {
            return redirect()->to('admin/password-resets')->with('error', 'Request not found');
        }
        
        return view('admin/password_reset_change', [
            'title' => 'Change Password - CSCS SMS',
            'reset' => $request
        ]);
    }

    /**
     * Process password change
     */
    public function changePassword()
    {
        $resetId = $this->request->getPost('reset_id');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!$resetId || !$newPassword || !$confirmPassword) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Passwords do not match.');
        }

        if (strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'Password must be at least 6 characters long.');
        }

        $request = $this->resetRequestModel->find($resetId);
        if (!$request) {
            return redirect()->back()->with('error', 'Reset request not found.');
        }

        // Update password in auth_identities table
        $db = \Config\Database::connect();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Delete ALL existing auth_identities for this user (all types)
        $db->table('auth_identities')
            ->where('user_id', $request['user_id'])
            ->delete();
        
        // Also delete from auth_tokens and auth_remember_tokens if they exist
        try {
            $db->table('auth_tokens')->where('user_id', $request['user_id'])->delete();
            $db->table('auth_remember_tokens')->where('user_id', $request['user_id'])->delete();
        } catch (\Exception $e) {
            // Tables might not exist, ignore
        }
        
        // Get clean email without mailto: prefixes
        $cleanEmail = str_replace('mailto:', '', $request['email']);
        
        // Create new auth identity with the new password
        $updated = $db->table('auth_identities')->insert([
            'user_id' => $request['user_id'],
            'type' => 'email_password',
            'name' => $cleanEmail,
            'secret' => $hashedPassword,
            'secret2' => null,
            'expires' => null,
            'extra' => null,
            'force_reset' => 0,
            'last_used_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($updated) {
            // Mark request as used
            $this->resetRequestModel->update($resetId, ['status' => 'used']);
            
            return redirect()->to('admin/password-resets')->with('success', 'Password changed successfully!');
        }
        
        return redirect()->back()->with('error', 'Failed to update password.');
    }

    /**
     * Debug method to show all requests - restricted to master admin only
     */
    public function debug()
    {
        helper('admin_access');
        if (! function_exists('is_master_admin') || ! is_master_admin()) {
            return redirect()->to(base_url('/'));
        }
        
        $requests = $this->resetRequestModel->findAll();
        echo '<h3>All Password Reset Requests:</h3>';
        echo '<pre>' . print_r($requests, true) . '</pre>';
        die();
    }
}

