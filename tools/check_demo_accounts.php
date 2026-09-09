<?php
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap CodeIgniter
require __DIR__ . '/../app/Config/Paths.php';
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Boot.php';
\CodeIgniter\Boot::bootSpark($paths);

$db = \Config\Database::connect();

echo "=== Checking Demo Accounts in Database ===\n\n";

// Check users
$users = $db->table('users')
    ->select('id, email, username, active')
    ->where('email LIKE', '%demo%')
    ->orWhere('email LIKE', '%student%')
    ->orWhere('email LIKE', '%teacher%')
    ->orWhere('email LIKE', '%admin%')
    ->orWhere('email LIKE', '%parent%')
    ->get()
    ->getResult();

echo "USERS:\n";
foreach ($users as $user) {
    echo "  ID: {$user->id}, Email: {$user->email}, Username: {$user->username}, Active: {$user->active}\n";
}

echo "\nAUTH IDENTITIES:\n";
$identities = $db->table('auth_identities')
    ->select('user_id, type, name, secret')
    ->where('type', 'email_password')
    ->get()
    ->getResult();

foreach ($identities as $identity) {
    echo "  User ID: {$identity->user_id}, Name: {$identity->name}, Secret (hash): " . substr($identity->secret, 0, 30) . "...\n";
}

echo "\nTEACHERS:\n";
$teachers = $db->table('teachers')
    ->select('id, user_id, first_name, last_name, license_number, email')
    ->get()
    ->getResult();

foreach ($teachers as $teacher) {
    echo "  ID: {$teacher->id}, User ID: {$teacher->user_id}, Name: {$teacher->first_name} {$teacher->last_name}, License: {$teacher->license_number}, Email: {$teacher->email}\n";
}

echo "\nSECTIONS:\n";
$sections = $db->table('sections')
    ->select('id, section_name, grade_level, grading_type, adviser_id, school_year')
    ->get()
    ->getResult();

foreach ($sections as $section) {
    echo "  ID: {$section->id}, Name: {$section->section_name}, Grade: {$section->grade_level}, Type: {$section->grading_type}, Adviser ID: {$section->adviser_id}, School Year: {$section->school_year}\n";
}

echo "\nSTUDENTS:\n";
$students = $db->table('students')
    ->select('id, user_id, first_name, last_name, lrn, email, enrollment_status, section_id')
    ->get()
    ->getResult();

foreach ($students as $student) {
    echo "  ID: {$student->id}, User ID: {$student->user_id}, Name: {$student->first_name} {$student->last_name}, LRN: {$student->lrn}, Email: {$student->email}, Status: {$student->enrollment_status}, Section: {$student->section_id}\n";
}

echo "\n=== End of Report ===\n";