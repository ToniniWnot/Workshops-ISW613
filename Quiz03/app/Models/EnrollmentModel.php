<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table         = 'enrollments';
    protected $returnType    = 'array';
    protected $allowedFields = ['student_name', 'student_card', 'course_id'];
    protected $useTimestamps = true;

    // Trae una matrícula con los datos del curso y la carrera
    public function findWithDetails(int $id): ?array
    {
        return $this->select('enrollments.*, courses.name AS course_name, courses.code AS course_code,
                              courses.credits, careers.name AS career_name')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('careers', 'careers.id = courses.career_id')
                    ->find($id);
    }
}