<?php
namespace App\Controllers;

class DebugLogin extends BaseController
{
    public function checkUser()
    {
        $email = $this->request->getGet('email') ?: 'maylinpaet19@gmail.com';
        
        $db = \Config\Database::connect();
        
        // Check auth_identities table
        $identity = $db->table('auth_identities')
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->get()
            ->getRowArray();
        
        // Check users table
        $user = null;
        if ($identity) {
            $user = $db->table('users')
                ->where('id', $identity['user_id'])
                ->get()
                ->getRowArray();
        }
        
        // Check students table
        $student = $db->table('students')
            ->where('email', $email)
            ->get()
            ->getRowArray();
        
        // Check user groups
        $groups = [];
        if ($identity) {
            $groups = $db->table('auth_groups_users')
                ->where('user_id', $identity['user_id'])
                ->get()
                ->getResultArray();
        }
        
        return $this->response->setJSON([
            'email' => $email,
            'identity_exists' => $identity ? 'Yes' : 'No',
            'identity' => $identity,
            'user_exists' => $user ? 'Yes' : 'No',
            'user' => $user,
            'student_exists' => $student ? 'Yes' : 'No',
            'student' => $student,
            'groups' => $groups
        ]);
    }
    
    public function testPassword()
    {
        $email = $this->request->getGet('email') ?: 'maylinpaet19@gmail.com';
        $password = $this->request->getGet('password') ?: 'LPHS6051!';
        
        $db = \Config\Database::connect();
        
        $identity = $db->table('auth_identities')
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->get()
            ->getRowArray();
        
        if (!$identity) {
            return $this->response->setJSON([
                'error' => 'Identity not found',
                'email' => $email
            ]);
        }
        
        $passwords = service('passwords');
        $isValid = $passwords->verify($password, $identity['secret2']);
        
        return $this->response->setJSON([
            'email' => $email,
            'password_valid' => $isValid ? 'Yes' : 'No',
            'stored_hash' => substr($identity['secret2'], 0, 20) . '...',
            'test_password' => $password
        ]);
    }
}