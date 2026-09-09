<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Idempotent seeder for existing deployments.
 * Run once after deploy: php spark db:seed AddKindergartenDataSeeder
 */
class AddKindergartenDataSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;
        $currentYear = (int) date('Y');
        $schoolYear  = $currentYear . '-' . ($currentYear + 1);

        $existingK = $db->table('sections')
            ->where('grade_level', 0)
            ->where('school_year', $schoolYear)
            ->countAllResults();

        if ($existingK === 0) {
            $sections = [];
            foreach (['K-A', 'K-B'] as $sectionName) {
                $sections[] = [
                    'section_name'       => $sectionName,
                    'grade_level'        => 0,
                    'school_year'        => $schoolYear,
                    'adviser_id'         => null,
                    'max_capacity'       => 40,
                    'current_enrollment' => 0,
                    'is_active'          => true,
                    'created_at'         => date('Y-m-d H:i:s'),
                    'updated_at'         => date('Y-m-d H:i:s'),
                ];
            }
            $db->table('sections')->insertBatch($sections);
            echo "Inserted " . count($sections) . " Kindergarten sections ({$schoolYear}).\n";
        } else {
            echo "Kindergarten sections already exist for {$schoolYear}.\n";
        }

        $kSubjects = [
            ['subject_code' => 'LANGK', 'subject_name' => 'Language (Kindergarten)', 'grade_level' => 0, 'units' => 1.0],
            ['subject_code' => 'MATHK', 'subject_name' => 'Mathematics (Kindergarten)', 'grade_level' => 0, 'units' => 1.0],
            ['subject_code' => 'SCIK', 'subject_name' => 'Science (Kindergarten)', 'grade_level' => 0, 'units' => 1.0],
            ['subject_code' => 'APK', 'subject_name' => 'Araling Panlipunan (Kindergarten)', 'grade_level' => 0, 'units' => 1.0],
            ['subject_code' => 'MAPEHK', 'subject_name' => 'MAPEH (Kindergarten)', 'grade_level' => 0, 'units' => 1.0],
            ['subject_code' => 'ESPK', 'subject_name' => 'Edukasyon sa Pagpapakatao (Kindergarten)', 'grade_level' => 0, 'units' => 1.0],
        ];

        $inserted = 0;
        foreach ($kSubjects as $subject) {
            $exists = $db->table('subjects')
                ->where('subject_code', $subject['subject_code'])
                ->countAllResults();
            if ($exists > 0) {
                continue;
            }
            $subject['is_active']  = 1;
            $subject['is_core']    = 1;
            $subject['created_at'] = date('Y-m-d H:i:s');
            $subject['updated_at'] = date('Y-m-d H:i:s');
            $db->table('subjects')->insert($subject);
            $inserted++;
        }

        echo "Inserted {$inserted} Kindergarten subject(s).\n";
    }
}
