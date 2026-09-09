<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Models\UserModel;

/**
 * Admin user management UI was removed; this controller only handles
 * master-only admin staff page permissions (routes under admin/dashboard/…).
 */
class Users extends BaseController
{
    protected $auth;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->auth = auth();
        $this->userModel = new UserModel();
    }

    public function editStaffPermissions($id)
    {
        helper('admin_access');
        if (! is_master_admin()) {
            return redirect()->to(base_url('/'))->with('error', 'Only a master administrator can edit staff page access.');
        }

        $user = $this->userModel->findById((int) $id);
        if (! $user || ! $user->inGroup('admin_staff')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'That account is not admin staff.');
        }

        return view('admin/users_staff_permissions', [
            'title' => 'Staff page access - CSCS SMS',
            'targetUser' => $user,
            'selectedPages' => admin_staff_pages_from_db((int) $id),
        ]);
    }

    public function updateStaffPermissions($id)
    {
        helper('admin_access');
        if (! is_master_admin()) {
            return redirect()->to(base_url('/'))->with('error', 'Only a master administrator can update staff page access.');
        }

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to(base_url('admin/dashboard'));
        }

        $user = $this->userModel->findById((int) $id);
        if (! $user || ! $user->inGroup('admin_staff')) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'That account is not admin staff.');
        }

        $pages = $this->request->getPost('pages');
        $pageList = is_array($pages) ? $pages : [];
        $pageList = array_values(array_intersect(admin_valid_page_keys(), $pageList));
        if ($pageList === []) {
            return redirect()->back()->withInput()->with('error', 'Select at least one page.');
        }

        admin_save_allowed_pages((int) $id, $pageList);

        return redirect()->to(base_url('admin/dashboard'))->with('success', 'Page access updated.');
    }
}

