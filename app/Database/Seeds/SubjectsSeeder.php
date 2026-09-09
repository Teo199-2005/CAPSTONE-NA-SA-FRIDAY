<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SubjectsSeeder extends Seeder
{
    public function run()
    {
        $subjects = [];

        $subjects[] = ['subject_code' => 'LANGK', 'subject_name' => 'Language (Kindergarten)', 'grade_level' => 0, 'units' => 1.0];
        $subjects[] = ['subject_code' => 'MATHK', 'subject_name' => 'Mathematics (Kindergarten)', 'grade_level' => 0, 'units' => 1.0];
        $subjects[] = ['subject_code' => 'SCIK', 'subject_name' => 'Science (Kindergarten)', 'grade_level' => 0, 'units' => 1.0];
        $subjects[] = ['subject_code' => 'APK', 'subject_name' => 'Araling Panlipunan (Kindergarten)', 'grade_level' => 0, 'units' => 1.0];
        $subjects[] = ['subject_code' => 'MAPEHK', 'subject_name' => 'MAPEH (Kindergarten)', 'grade_level' => 0, 'units' => 1.0];
        $subjects[] = ['subject_code' => 'ESPK', 'subject_name' => 'Edukasyon sa Pagpapakatao (Kindergarten)', 'grade_level' => 0, 'units' => 1.0];

        // Grades 1-3 core (DepEd MELC): Mother Tongue + Filipino + English + Math + AP + MAPEH + ESP.
        // Science is added from Grade 3 onward.
        foreach ([1, 2, 3] as $grade) {
            $subjects[] = ['subject_code' => "MTB{$grade}", 'subject_name' => "Mother Tongue {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "FIL{$grade}", 'subject_name' => "Filipino {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "ENG{$grade}", 'subject_name' => "English {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "MATH{$grade}", 'subject_name' => "Mathematics {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "AP{$grade}", 'subject_name' => "Araling Panlipunan {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "MAPEH{$grade}", 'subject_name' => "MAPEH {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "ESP{$grade}", 'subject_name' => "Edukasyon sa Pagpapakatao {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            if ($grade === 3) {
                $subjects[] = ['subject_code' => "SCI{$grade}", 'subject_name' => "Science {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            }
        }

        // Grade 4-6 core: Filipino, English, Math, Science, AP, MAPEH, ESP, EPP (TLE).
        foreach ([4, 5, 6] as $grade) {
            $subjects[] = ['subject_code' => "FIL{$grade}", 'subject_name' => "Filipino {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "ENG{$grade}", 'subject_name' => "English {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "MATH{$grade}", 'subject_name' => "Mathematics {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "SCI{$grade}", 'subject_name' => "Science {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "AP{$grade}", 'subject_name' => "Araling Panlipunan {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "MAPEH{$grade}", 'subject_name' => "MAPEH {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "ESP{$grade}", 'subject_name' => "Edukasyon sa Pagpapakatao {$grade}", 'grade_level' => $grade, 'units' => 1.0];
            $subjects[] = ['subject_code' => "EPP{$grade}", 'subject_name' => "Edukasyong Pantahanan at Pangkabuhayan {$grade}", 'grade_level' => $grade, 'units' => 1.0];
        }

        foreach ($subjects as $subject) {
            $subject['is_active']  = 1;
            $subject['created_at'] = date('Y-m-d H:i:s');
            $subject['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('subjects')->insert($subject);
        }

        echo 'Inserted ' . count($subjects) . " elementary subjects (Kindergarten and Grades 1-6).\n";
    }
}
