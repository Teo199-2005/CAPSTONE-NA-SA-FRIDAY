<?php
// Simple script to check grading symbols in database
require_once __DIR__ . '/vendor/autoload.php';

$db = \Config\Database::connect();

// Check sections table
echo "=== SECTIONS ===\n";
$sections = $db->table('sections')->get()->getResultArray();
foreach ($sections as $section) {
    echo "ID: {$section['id']}, Name: {$section['section_name']}, Grading Type: {$section['grading_type']}\n";
}

// Check section_grading_symbols table
echo "\n=== SECTION GRADING SYMBOLS ===\n";
$symbols = $db->table('section_grading_symbols')->get()->getResultArray();
if (empty($symbols)) {
    echo "NO SYMBOLS FOUND IN DATABASE!\n";
} else {
    foreach ($symbols as $symbol) {
        echo "Section ID: {$symbol['section_id']}, Symbol: {$symbol['symbol']}, Label: {$symbol['label']}, Active: {$symbol['is_active']}\n";
    }
}

// Check if table exists
echo "\n=== TABLE CHECK ===\n";
$tables = $db->listTables();
if (in_array('section_grading_symbols', $tables)) {
    echo "Table 'section_grading_symbols' EXISTS\n";
} else {
    echo "Table 'section_grading_symbols' DOES NOT EXIST!\n";
}