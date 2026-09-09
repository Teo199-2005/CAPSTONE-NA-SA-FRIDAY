<?php
namespace App\Controllers;

class FixUser extends BaseController
{
    public function fixEmail()
    {
        $email = $this->request->getGet('email') ?: 'maylinpaet19@gmail.com';
        
        $db = \Config\Database::connect();
        
        // Get identity
        $identity = $db->table('auth_identities')
            ->where('type', 'email_password')
            ->where('secret', $email)
            ->get()
            ->getRowArray();
        
        if (!$identity) {
            return $this->response->setJSON(['error' => 'Identity not found']);
        }
        
        // Update users table with correct email
        $result = $db->table('users')
            ->where('id', $identity['user_id'])
            ->update(['email' => $email]);
        
        return $this->response->setJSON([
            'success' => $result,
            'message' => $result ? 'Email fixed successfully' : 'Failed to update email',
            'user_id' => $identity['user_id'],
            'email' => $email
        ]);
    }
}