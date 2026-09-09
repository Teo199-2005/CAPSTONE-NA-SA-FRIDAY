<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateStudentEmailSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        
        // Update the Maria Santos student record to include email
        $updated = $db->table('students')
            ->where('first_name', 'Maria')
            ->where('last_name', 'Santos')
            ->where('grade_level', 6)
            ->update([
                'email' => 'mariasantos67@hotmail.com',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        
        if ($updated) {
            echo "Updated student record with email: mariasantos67@hotmail.com\n";
        } else {
            echo "No student record found to update\n";
        }
    }
}