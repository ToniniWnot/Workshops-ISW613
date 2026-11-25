<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $courses = [
            ['ISW-101', 'Fundamentos de Programación',   4, 1],
            ['ISW-213', 'Bases de Datos I',              4, 1],
            ['ISW-412', 'Ingeniería de Requerimientos',  3, 1],
            ['TI-201',  'Redes de Computadoras',         4, 2],
            ['TI-305',  'Seguridad Informática',         3, 2],
            ['ADM-101', 'Fundamentos de Administración', 3, 3],
            ['CON-110', 'Contabilidad General',          4, 4],
        ];

        $data = array_map(fn ($c) => [
            'code'       => $c[0],
            'name'       => $c[1],
            'credits'    => $c[2],
            'career_id'  => $c[3],
            'created_at' => $now,
            'updated_at' => $now,
        ], $courses);

        $this->db->table('courses')->insertBatch($data);
    }
}