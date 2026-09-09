<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SnedCategoryModel;
use App\Models\SnedCategoryFieldModel;
use App\Models\SnedGradeModel;

class SnedTest extends BaseCommand
{
    protected $group       = 'SNED';
    protected $name        = 'sned:test';
    protected $description = 'Verifies SNED module setup';
    protected $usage       = 'sned:test';
    protected $arguments   = [];
    protected $options     = [];

    public function run(array $params): int
    {
        $categoryModel = new SnedCategoryModel();
        $fieldModel = new SnedCategoryFieldModel();
        $gradeModel = new SnedGradeModel();
        $db = \Config\Database::connect();

        $cats = $categoryModel->getActiveCategories();
        CLI::write("Categories found: " . count($cats), 'green');
        foreach ($cats as $c) {
            CLI::write(" - " . $c['name'], 'white');
        }

        CLI::write("Total fields: " . $fieldModel->countAll(), 'green');
        CLI::write("Total grades: " . $gradeModel->countAll(), 'green');

        $section = $db->table('sections')->where('grade_level', 7)->get()->getRowArray();
        CLI::write("SNED Section: " . ($section['section_name'] ?? 'NONE') . " (ID: " . ($section['id'] ?? 'N/A') . ")", 'green');

        $teacher = $db->table('teachers')->where('position', 'SNED Teacher')->get()->getRowArray();
        CLI::write("SNED Teacher: " . ($teacher['first_name'] ?? 'NONE') . " " . ($teacher['last_name'] ?? ''), 'green');

        $student = $db->table('students')->where('grade_level', 7)->get()->getRowArray();
        CLI::write("SNED Student: " . ($student['first_name'] ?? 'NONE') . " " . ($student['last_name'] ?? ''), 'green');

        CLI::newLine();
        CLI::write("GRADING SYMBOLS:", 'yellow');
        CLI::write(" P = Proficient", 'white');
        CLI::write(" AP = Approaching Proficiency", 'white');
        CLI::write(" D = Developing", 'white');
        CLI::write(" B = Beginning", 'white');
        CLI::write(" NO/NA = Not Observed / Not Applicable", 'white');
        CLI::newLine();
        CLI::write("QUARTERS 1-4 (independent from admin term setting)", 'cyan');
        CLI::newLine();

        CLI::write("Login credentials:", 'green');
        $teacherEmail = $teacher['email'] ?? 'maria.santos@deped.gov.ph';
        $studentEmail = $student['email'] ?? 'sned.student@cscs.edu';
        CLI::write(" SNED Teacher: {$teacherEmail} / DemoPass123!", 'white');
        CLI::write(" SNED Student: {$studentEmail} / DemoPass123!", 'white');

        return 0;
    }
}