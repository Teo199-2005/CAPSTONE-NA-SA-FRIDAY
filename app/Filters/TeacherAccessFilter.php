<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Restricts teacher portal routes to teachers or admins (admins need export-pdf with teacher_id).
 */
class TeacherAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('auth');

        if (! $auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        helper('admin_access');

        $user = $auth->user();
        if ($user->inGroup('teacher') || is_any_admin()) {
            return null;
        }

        return redirect()->to(base_url('/'))->with('error', 'Access denied.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
