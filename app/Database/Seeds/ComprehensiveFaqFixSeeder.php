<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ComprehensiveFaqFixSeeder extends Seeder
{
    public function run()
    {
        // Update all FAQ entries that mention document requirements
        $this->db->query("
            UPDATE faq 
            SET answer = 'Required documents for enrollment: 1) Birth Certificate (PSA copy), 2) Report Card/Form 138. Both documents must be in PDF, JPG, or PNG format (Max: 5MB each). Upload digitally through the enrollment portal.',
                keywords = 'documents,requirements,birth certificate,report card,form 138,enrollment,upload,PDF,JPG,PNG',
                updated_at = NOW()
            WHERE answer LIKE '%Good Moral%' 
               OR answer LIKE '%Medical Certificate%' 
               OR answer LIKE '%2x2 ID Photo%'
               OR answer LIKE '%documents%'
               OR question LIKE '%documents%'
        ");

        // Also update enrollment process questions
        $this->db->query("
            UPDATE faq 
            SET answer = 'To enroll at CSCS: 1) Fill out the online enrollment form, 2) Upload required documents (Birth Certificate and Report Card only), 3) Submit application, 4) Wait for admin approval and section assignment.',
                keywords = 'enroll,enrollment,process,application,documents,birth certificate,report card',
                updated_at = NOW()
            WHERE question LIKE '%enroll%' 
               AND answer LIKE '%good moral%'
        ");
    }
}
