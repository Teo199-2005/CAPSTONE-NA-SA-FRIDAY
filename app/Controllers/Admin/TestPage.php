<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class TestPage extends BaseController
{
    public function index(): string
    {
        return view('admin/test_page');
    }
}