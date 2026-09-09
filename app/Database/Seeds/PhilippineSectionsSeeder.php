<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PhilippineSectionsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        if ($db->table('sections')->countAllResults() > 0) {
            $db->query('SET FOREIGN_KEY_CHECKS = 0');
            $db->query('TRUNCATE TABLE sections');
            $db->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $sectionNames = [
            0 => ['K-A', 'K-B'],
        ];
        foreach (range(1, 6) as $grade) {
            $sectionNames[$grade] = [
                $grade . '-A',
                $grade . '-B',
            ];
        }

        $sectionsData = [];
        $currentYear = date('Y');
        $schoolYear = $currentYear . '-' . ($currentYear + 1);

        foreach ($sectionNames as $gradeLevel => $sections) {
            foreach ($sections as $sectionName) {
                $sectionsData[] = [
                    'section_name'       => $sectionName,
                    'grade_level'        => $gradeLevel,
                    'school_year'        => $schoolYear,
                    'adviser_id'         => null,
                    'max_capacity'       => 40,
                    'current_enrollment' => 0,
                    'is_active'          => true,
                    'created_at'         => date('Y-m-d H:i:s'),
                    'updated_at'         => date('Y-m-d H:i:s'),
                ];
            }
        }

        $db->table('sections')->insertBatch($sectionsData);

        echo "Elementary school sections created successfully!\n";
        echo 'Created ' . count($sectionsData) . " sections across Kindergarten and Grades 1-6.\n";

        echo "\nSections created:\n";
        foreach ($sectionNames as $gradeLevel => $sections) {
            $label = $gradeLevel === 0 ? 'Kindergarten' : "Grade {$gradeLevel}";
            echo "{$label}:\n";
            foreach ($sections as $sectionName) {
                echo "  - {$sectionName}\n";
            }
        }
    }
}
