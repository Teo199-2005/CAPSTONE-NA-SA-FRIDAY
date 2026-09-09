<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class StudentAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('auth');
        
        // Check if user is logged in
        if (!$auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        $user = $auth->user();
        
        // Check if user is a student
        if (!$user->inGroup('student')) {
            return redirect()->to(base_url('login'))->with('error', 'Access denied.');
        }
        
        // Check student enrollment status
        try {
            $studentModel = new \App\Models\StudentModel();
            $student = $studentModel->where('user_id', $user->id)->first();
            
            if (!$student) {
                // Student record not found - logout and redirect
                $auth->logout();
                return redirect()->to(base_url('login'))
                    ->with('error', 'Student record not found. Please contact the administration.');
            }
            
            // Check enrollment status
            if ($student['enrollment_status'] === 'pending') {
                // Student is pending approval
                $auth->logout();
                return redirect()->to(base_url('login'))
                    ->with('error', 'Your enrollment is still pending approval. Please wait for admin approval before accessing the system.');
            } elseif ($student['enrollment_status'] === 'rejected') {
                // Student was rejected
                $auth->logout();
                return redirect()->to(base_url('login'))
                    ->with('error', 'Your enrollment application has been rejected. Please contact the administration for more information.');
            } elseif ($student['enrollment_status'] !== 'approved' && $student['enrollment_status'] !== 'enrolled') {
                // Student has invalid status
                $auth->logout();
                return redirect()->to(base_url('login'))
                    ->with('error', 'Your account status is invalid. Please contact the administration.');
            }
            
            // Student is approved or enrolled - allow access
            return null;
            
        } catch (\Throwable $e) {
            // Error checking student status
            $auth->logout();
            return redirect()->to(base_url('login'))
                ->with('error', 'Unable to verify your enrollment status. Please try again later.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after request
    }
}
