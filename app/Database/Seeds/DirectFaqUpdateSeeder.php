<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DirectFaqUpdateSeeder extends Seeder
{
    public function run()
    {
        // Direct SQL update to fix the specific FAQ entry
        $this->db->query("
            UPDATE faq 
            SET answer = 'Required documents for enrollment: 1) Birth Certificate (PSA copy), 2) Report Card/Form 138. Both documents must be in PDF, JPG, or PNG format (Max: 5MB each). Upload digitally through the enrollment portal.',
                keywords = 'documents,requirements,birth certificate,report card,form 138,enrollment,upload,PDF,JPG,PNG',
                updated_at = NOW()
            WHERE question = 'What documents do I need for enrollment?'
        ");

        // Also update any similar questions
        $this->db->query("
            UPDATE faq 
            SET answer = 'Required documents for enrollment: 1) Birth Certificate (PSA copy), 2) Report Card/Form 138. Both documents must be in PDF, JPG, or PNG format (Max: 5MB each). Upload digitally through the enrollment portal.',
                keywords = 'documents,requirements,birth certificate,report card,form 138,enrollment,upload,PDF,JPG,PNG',
                updated_at = NOW()
            WHERE question LIKE '%documents%required%' 
               OR question LIKE '%What documents%'
               OR question LIKE '%document requirements%'
        ");

        // Clear any cached results
        $this->db->query("UPDATE faq SET view_count = 0 WHERE question LIKE '%documents%'");
    }
}