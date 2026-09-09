<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;

class Notifications extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth();
    }

    public function index()
    {
        if (! $this->auth->user()->inGroup('student')) {
            return redirect()->to(base_url('/'));
        }
        return view('student/notifications', [
            'title' => 'Notifications - CSCS SMS',
        ]);
    }
}





