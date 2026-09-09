<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProgramsProjectsSeed extends Seeder
{
    public function run()
    {
        helper('programs_projects');

        // Save hero data
        $hero = [
            'image' => 'uploads/programs-projects/1782790503_f4fe51ac6d5fc08c7ae5.png',
            'title' => 'dasda',
            'description' => 'sdasdasd',
            'position' => '50% 50%',
            'scale' => 1.0
        ];
        programs_projects_hero_save('programs', $hero);

        // Save sections
        $sections = [
            [
                'id' => 'section_1_programs',
                'title' => 'asdasd',
                'description' => 'dasd',
                'media_type' => 'image',
                'media_url' => 'uploads/programs-projects/1782790503_f4fe51ac6d5fc08c7ae5.png',
                'order' => 0,
                'position' => '50% 50%',
                'scale' => 1.0
            ],
            [
                'id' => 'section_2_programs',
                'title' => 'Section 2 Title',
                'description' => 'dasdas',
                'media_type' => 'image',
                'media_url' => 'uploads/programs-projects/1782790503_4216a941a5772362418f.png',
                'order' => 1,
                'position' => '50% 50%',
                'scale' => 1.0
            ],
            [
                'id' => 'section_3_programs',
                'title' => 'sdasdas',
                'description' => 'asdasd',
                'media_type' => 'image',
                'media_url' => 'uploads/programs-projects/1782790503_ec5f004ea916ac9ae301.png',
                'order' => 2,
                'position' => '50% 50%',
                'scale' => 1.0
            ]
        ];
        programs_projects_sections_save('programs', $sections);

        echo "Programs & Projects data seeded successfully!\n";
    }
}