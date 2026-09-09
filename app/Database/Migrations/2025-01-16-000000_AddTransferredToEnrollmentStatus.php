<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransferredToEnrollmentStatus extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('students', [
            'enrollment_status' => [
                'type' => "ENUM('pending','approved','rejected','enrolled','graduated','dropped','transferred')",
                'default' => 'pending',
                'null' => false
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('students', [
            'enrollment_status' => [
                'type' => "ENUM('pending','approved','rejected','enrolled','graduated','dropped')",
                'default' => 'pending',
                'null' => false
            ]
        ]);
    }
}