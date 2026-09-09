<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckDemoAccounts extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'check:demo';
    protected $description = 'Check demo accounts status';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('=== EXISTING DEMO ACCOUNTS ===', 'yellow');
        $query = $db->query("SELECT u.id, u.username, u.email, ai.secret FROM users u LEFT JOIN auth_identities ai ON u.id = ai.user_id WHERE u.email LIKE '%demo%' OR u.email LIKE '%admin%' OR u.email LIKE '%student%'");
        
        foreach($query->getResult() as $row) {
            CLI::write("ID: {$row->id}, Username: {$row->username}, Email: {$row->email}, Has Password: " . (!empty($row->secret) ? 'Yes' : 'No'));
        }
        
        CLI::write('=== AUTH GROUPS ===', 'yellow');
        $query = $db->query("SELECT agu.user_id, ag.title FROM auth_groups_users agu JOIN auth_groups ag ON agu.group = ag.title");
        foreach($query->getResult() as $row) {
            CLI::write("User ID: {$row->user_id}, Group: {$row->title}");
        }
    }
}