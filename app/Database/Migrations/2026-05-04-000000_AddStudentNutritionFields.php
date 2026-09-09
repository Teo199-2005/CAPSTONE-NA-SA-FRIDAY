<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentNutritionFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('students', [
            'height_cm' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'after'      => 'gender',
            ],
            'weight_kg' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'after'      => 'height_cm',
            ],
            'ethnicity' => [
                'type'       => 'VARCHAR',
                'constraint' => '120',
                'null'       => true,
                'after'      => 'weight_kg',
            ],
            'bmi' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'after'      => 'ethnicity',
            ],
            'nutrition_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '40',
                'null'       => true,
                'after'      => 'bmi',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('students', ['height_cm', 'weight_kg', 'ethnicity', 'bmi', 'nutrition_status']);
    }
}
