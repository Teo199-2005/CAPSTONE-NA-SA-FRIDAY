<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTeacherIdToEmployeeId extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE teachers CHANGE teacher_id employee_id VARCHAR(20)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE teachers CHANGE employee_id teacher_id VARCHAR(20)');
    }
}
