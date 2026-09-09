<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdminColumnsToPasswordResetRequests extends Migration
{
    public function up()
    {
        $this->forge->addColumn('password_reset_requests', [
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'used_at'
            ],
            'admin_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'approved_by'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('password_reset_requests', ['approved_by', 'admin_notes']);
    }
}