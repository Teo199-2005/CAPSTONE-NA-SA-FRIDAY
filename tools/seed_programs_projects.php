<?php
/**
 * One-time seed script to insert Programs & Projects data into system_settings.
 * Run from the project root: php tools/seed_programs_projects.php
 */

// Bootstrap CodeIgniter environment
$_SERVER['CI_ENVIRONMENT'] = 'development';
chdir(__DIR__ . '/..');
require_once 'app/Helpers/programs_projects_helper.php';

// Load system dependencies
$db = \Config\Database::connect();
$model = new \App\Models\SystemSettingModel();

// Save hero data
$hero = [
    'image' => 'uploads/programs-projects/1782790503_f4fe51ac6d5fc08c7ae5.png',
    'title' => 'dasda',
    'description' => 'sdasdasd',
    'position' => '50% 50%',
    'scale' => 1.0
];

$model->setSetting(
    'programs_projects_hero_programs',
    json_encode($hero, JSON_UNESCAPED_SLASHES),
    'Programs/Projects hero section for tab: programs'
);

// Save sections with the uploaded image references
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

$model->setSetting(
    'programs_projects_sections_programs',
    json_encode($sections, JSON_UNESCAPED_SLASHES),
    'Programs/Projects content sections for tab: programs'
);

echo "Data saved successfully!\n";
echo "Hero: programs_projects_hero_programs\n";
echo "Sections: programs_projects_sections_programs\n";