<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixFaqDocumentRequirementsSeeder extends Seeder
{
    public function run()
    {
        // Update FAQ entries that still mention outdated document requirements
        $this->db->table('faq')
            ->where('question LIKE', '%documents%')
            ->where('question LIKE', '%enrollment%')
            ->update([
                'answer' => 'Required documents for enrollment: 1) Birth Certificate (PSA copy), 2) Report Card/Form 138. Both documents must be in PDF, JPG, or PNG format (Max: 5MB each). Upload digitally through the enrollment portal.',
                'keywords' => 'documents,requirements,birth certificate,report card,form 138,enrollment,upload,PDF,JPG,PNG',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // Also update any other variations that mention good moral, medical cert, or photo
        $this->db->table('faq')
            ->groupStart()
                ->like('answer', 'Good Moral Certificate')
                ->orLike('answer', 'Medical Certificate')
                ->orLike('answer', '2x2 ID Photo')
            ->groupEnd()
            ->update([
                'answer' => 'Required documents for enrollment: 1) Birth Certificate (PSA copy), 2) Report Card/Form 138. Both documents must be in PDF, JPG, or PNG format (Max: 5MB each). Upload digitally through the enrollment portal.',
                'keywords' => 'documents,requirements,birth certificate,report card,form 138,enrollment,upload,PDF,JPG,PNG',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }
}