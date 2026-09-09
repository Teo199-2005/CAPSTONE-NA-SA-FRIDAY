<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DebugTeachers extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        echo "=== TEACHERS TABLE ===\n";
        $teachers = $db->table('teachers')->select('id, first_name, last_name, user_id')->limit(3)->get()->getResultArray();
        foreach ($teachers as $teacher) {
            echo "ID: {$teacher['id']}, Name: {$teacher['first_name']} {$teacher['last_name']}, user_id: " . ($teacher['user_id'] ?? 'NULL') . "\n";
        }
        
        echo "\n=== USERS TABLE ===\n";
        $users = $db->table('users')->select('id, email')->like('email', '@lphs.edu.ph')->limit(3)->get()->getResultArray();
        foreach ($users as $user) {
            echo "ID: {$user['id']}, Email: {$user['email']}\n";
        }
        
        echo "\n=== JOIN TEST ===\n";
        $result = $db->table('teachers t')
            ->select('t.first_name, t.last_name, t.user_id, u.email')
            ->join('users u', 'u.id = t.user_id', 'left')
            ->limit(3)
            ->get()
            ->getResultArray();
            
        foreach ($result as $row) {
            echo "Name: {$row['first_name']} {$row['last_name']}, user_id: " . ($row['user_id'] ?? 'NULL') . ", Email: " . ($row['email'] ?? 'NULL') . "\n";
        }
    }
}