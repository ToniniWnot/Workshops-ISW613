<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('careers')->insertBatch([
            ['name' => 'Ingeniería del Software',        'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tecnologías de la Información',  'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Administración de Empresas',     'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Contabilidad y Finanzas',        'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}