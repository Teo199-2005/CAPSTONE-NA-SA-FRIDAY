<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        if (!auth()->loggedIn() || ! is_any_admin()) {
            return redirect()->to(base_url('login'));
        }

        $user = auth()->user();
        $db = \Config\Database::connect();
        
        // Get fresh user data from database
        $userData = $db->table('users')->where('id', $user->id)->get()->getRow();
        
        return view('admin/profile', [
            'title' => 'My Profile - CSCS SMS',
            'admin' => [
                'id' => $userData->id,
                'email' => $userData->email,
                'first_name' => $userData->first_name ?? '',
                'last_name' => $userData->last_name ?? ''
            ]
        ]);
    }

    public function update()
    {
        if (!auth()->loggedIn() || ! is_any_admin()) {
            return redirect()->to(base_url('login'));
        }

        $rules = [
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please fill in all required fields correctly.');
        }

        $user = auth()->user();
        $db = \Config\Database::connect();

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($db->table('users')->where('id', $user->id)->update($data)) {
            return redirect()->to(base_url('admin/profile'))->with('success', 'Profile updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update profile.');
    }

    public function changePassword()
    {
        if (!auth()->loggedIn() || ! is_any_admin()) {
            return redirect()->to(base_url('login'));
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return redirect()->back()->with('error', 'All password fields are required.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'New passwords do not match.');
        }

        if (strlen($newPassword) < 8) {
            return redirect()->back()->with('error', 'New password must be at least 8 characters.');
        }

        $user = auth()->user();
        $db = \Config\Database::connect();

        $identity = $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->get()->getRow();

        if (!$identity) {
            return redirect()->back()->with('error', 'Authentication identity not found.');
        }

        $currentPassword = trim($currentPassword);

        $passwordValid = false;
        if (! empty($identity->secret) && password_verify($currentPassword, $identity->secret)) {
            $passwordValid = true;
        } elseif (! empty($identity->secret2) && password_verify($currentPassword, $identity->secret2)) {
            $passwordValid = true;
        }

        if (! $passwordValid) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $db->table('auth_identities')
            ->where('user_id', $user->id)
            ->where('type', 'email_password')
            ->update([
                'secret'  => $newHash,
                'secret2' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to(base_url('admin/profile'))->with('success', 'Password changed successfully.');
    }
}

