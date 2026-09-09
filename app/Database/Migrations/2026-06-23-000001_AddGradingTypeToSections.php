<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGradingTypeToSections extends Migration
{
    public function up()
    {
        // Add grading_type column to sections table
        $this->forge->addColumn('sections', [
            'grading_type' => [
                'type'       => 'ENUM',
                'constraint' => ['numerical', 'non_numerical'],
                'default'    => 'numerical',
                'after'      => 'school_year',
            ],
        ]);

        // Set existing Grade 7 (SNED) sections to non_numerical
        $this->db->query("UPDATE sections SET grading_type = 'non_numerical' WHERE grade_level = 7");
        
        // Set existing grades 0-6 sections to numerical (default, but explicit for clarity)
        $this->db->query("UPDATE sections SET grading_type = 'numerical' WHERE grade_level != 7 OR grade_level IS NULL");
    }

    public function down()
    {
        $this->forge->dropColumn('sections', 'grading_type');
    }
}