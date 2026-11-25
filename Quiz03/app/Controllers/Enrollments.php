<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Enrollments extends BaseController
{
    protected $helpers = ['form', 'url'];

    private EnrollmentModel $enrollments;

    public function __construct()
    {
        $this->enrollments = new EnrollmentModel();
    }

    // Página principal: formulario de matrícula
    public function index()
    {
        return view('enrollments/form', [
            'title'   => 'Matrícula',
            'courses' => (new CourseModel())->withCareer()->orderBy('courses.code')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'student_name' => ['label' => 'Nombre del estudiante', 'rules' => 'required|min_length[3]|max_length[100]'],
            'student_card' => ['label' => 'Carné', 'rules' => 'required|alpha_dash|max_length[20]'],
            'course_id'    => ['label' => 'Curso', 'rules' => 'required|is_not_unique[courses.id]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput();
        }

        $data = $this->request->getPost(['student_name', 'student_card', 'course_id']);

        // Evita matricular dos veces al mismo estudiante en el mismo curso
        $exists = $this->enrollments
            ->where('student_card', $data['student_card'])
            ->where('course_id', $data['course_id'])
            ->first();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Este estudiante ya está matriculado en ese curso.');
        }

        $id = $this->enrollments->insert($data);

        return redirect()->to("enrollments/{$id}")->with('message', '¡Matrícula registrada con éxito!');
    }

    // Muestra los datos del registro
    public function show(int $id)
    {
        $enrollment = $this->enrollments->findWithDetails($id) ?? throw PageNotFoundException::forPageNotFound();

        return view('enrollments/show', [
            'title'      => 'Comprobante de matrícula',
            'enrollment' => $enrollment,
        ]);
    }
}
