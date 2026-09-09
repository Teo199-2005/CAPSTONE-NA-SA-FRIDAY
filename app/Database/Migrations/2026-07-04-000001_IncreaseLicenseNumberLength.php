<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IncreaseLicenseNumberLength extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE teachers MODIFY COLUMN license_number VARCHAR(20)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE teachers MODIFY COLUMN license_number VARCHAR(10)');
    }
}