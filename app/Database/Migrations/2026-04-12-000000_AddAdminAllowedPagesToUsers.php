<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Nullable JSON (stored as TEXT) of allowed admin portal page keys for admin_staff users.
 * NULL = not used (master admins in group "admin" have full access regardless of this column).
 */
class AddAdminAllowedPagesToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'admin_allowed_pages' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of page keys for admin_staff; NULL for unrestricted masters',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'admin_allowed_pages');
    }
}
