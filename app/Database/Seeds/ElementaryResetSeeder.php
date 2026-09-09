<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * One-shot reset for the elementary build:
 * truncate the curriculum/enrollment tables, then reseed sections, subjects,
 * FAQ, announcements, and the Grade 6 demo accounts in a deterministic order.
 *
 * Run with:
 *     php spark db:seed ElementaryResetSeeder
 */
class ElementaryResetSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $tables = [
            'grades',
            'section_subjects',
            'subjects',
            'enrollment_documents',
            'student_parents',
            'attendance',
            'teacher_schedules',
            'report_card_records',
            'quizzes',
            'students',
            'sections',
        ];

        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                $db->query("TRUNCATE TABLE {$table}");
                echo "Truncated {$table}\n";
            }
        }
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        $this->call('PhilippineSectionsSeeder');
        $this->call('SubjectsSeeder');
        $this->call('DemoAccountsSeeder');
        $this->call('CreateDemoTeacherWithStudentsSeeder');
        $this->call('CreateDemoStudentSeeder');

        echo "Elementary reset complete.\n";
    }
}
