<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table         = 'courses';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'code', 'credits', 'career_id'];
    protected $useTimestamps = true;

    // Trae los cursos con el nombre de su carrera
    public function withCareer()
    {
        return $this->select('courses.*, careers.name AS career_name')
                    ->join('careers', 'careers.id = courses.career_id');
    }
}