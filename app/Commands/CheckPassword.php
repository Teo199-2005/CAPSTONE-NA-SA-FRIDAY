<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckPassword extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'check:password';
    protected $description = 'Check student password storage';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $lrn = $params[0] ?? '614965027083';
        
        CLI::write("=== CHECKING PASSWORD FOR LRN: {$lrn} ===", 'yellow');
        
        // Get student
        $student = $db->query("SELECT * FROM students WHERE lrn = ?", [$lrn])->getRow();
        if (!$student) {
            CLI::write("Student not found");
            return;
        }
        
        // Get auth identity
        $identity = $db->query("SELECT * FROM auth_identities WHERE user_id = ?", [$student->user_id])->getRow();
        if ($identity) {
            CLI::write("Password hash: " . substr($identity->secret, 0, 20) . "...");
            CLI::write("Hash type: " . (password_get_info($identity->secret)['algoName'] ?? 'Unknown'));
            
            // Test if it matches common passwords
            $testPasswords = ['Demo123!', 'student123', 'password123', 'LPHS2024!'];
            foreach ($testPasswords as $test) {
                if (password_verify($test, $identity->secret)) {
                    CLI::write("✓ Password matches: {$test}", 'green');
                    return;
                }
            }
            CLI::write("✗ Password doesn't match common test passwords", 'red');
        } else {
            CLI::write("No auth identity found");
        }
    }
}