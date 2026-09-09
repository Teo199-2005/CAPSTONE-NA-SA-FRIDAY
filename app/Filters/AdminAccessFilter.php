<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('admin_access');

        $auth = auth();
        if (! $auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }

        $user = $auth->user();
        if (! is_any_admin()) {
            return redirect()->to(base_url('/'))->with('error', 'Access denied.');
        }

        if (is_master_admin()) {
            return null;
        }

        $segs = explode('/', trim($request->getUri()->getPath(), '/'));
        $adminPos = array_search('admin', $segs, true);
        if ($adminPos === false) {
            return redirect()->to(base_url('/'))->with('error', 'Invalid admin request.');
        }
        $path = implode('/', array_slice($segs, $adminPos));

        $pageKey = admin_page_key_from_path($path);
        if ($pageKey === null || ! admin_staff_has_page((int) $user->id, $pageKey)) {
            if ($request->isAJAX()) {
                return service('response')->setStatusCode(403)->setJSON([
                    'success' => false,
                    'error' => 'You do not have access to this admin area.',
                ]);
            }

            return redirect()->to(base_url('/'))->with('error', 'You do not have access to this admin area.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
