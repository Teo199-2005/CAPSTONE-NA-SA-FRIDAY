<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ResetElementaryData extends Migration
{
    public function up()
    {
        $db = $this->db;

        $tables = [
            'grades',
            'section_subjects',
            'subjects',
            'enrollment_documents',
            'student_parents',
            'attendance',
            'teacher_schedules',
            'report_card_records',
            'students',
            'sections',
        ];

        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                $db->query("TRUNCATE TABLE {$table}");
            }
        }
        $db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        // Destructive reset has no reverse.
    }
}
