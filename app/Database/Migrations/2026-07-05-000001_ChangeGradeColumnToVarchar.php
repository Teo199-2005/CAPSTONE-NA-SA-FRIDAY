<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeGradeColumnToVarchar extends Migration
{
    public function up()
    {
        // Change grade column from DECIMAL to VARCHAR to support both numeric grades and symbols
        $this->forge->modifyColumn('grades', [
            'grade' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
        ]);
        
        log_message('info', 'Migration: Changed grades.grade column from DECIMAL to VARCHAR');
    }

    public function down()
    {
        // Revert back to DECIMAL - note: this will fail if there are non-numeric values stored
        $this->forge->modifyColumn('grades', [
            'grade' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
        ]);
    }
}