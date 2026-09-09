<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomGradeLevelToSections extends Migration
{
    public function up()
    {
        // Add custom grade level column to sections table
        $this->forge->addColumn('sections', [
            'grade_level_custom' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'grade_level',
            ],
        ]);

        // Alter grading_type enum to include 'custom'
        $this->db->query("ALTER TABLE sections MODIFY COLUMN grading_type ENUM('numerical','non_numerical','custom') NOT NULL DEFAULT 'numerical'");
    }

    public function down()
    {
        $this->forge->dropColumn('sections', 'grade_level_custom');
        $this->db->query("ALTER TABLE sections MODIFY COLUMN grading_type ENUM('numerical','non_numerical') NOT NULL DEFAULT 'numerical'");
    }
}