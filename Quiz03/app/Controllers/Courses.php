<?php

namespace App\Controllers;

use App\Models\CareerModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Courses extends BaseController
{
    protected $helpers = ['form', 'url'];

    private CourseModel $courses;
    private CareerModel $careers;

    public function __construct()
    {
        $this->courses = new CourseModel();
        $this->careers = new CareerModel();
    }

    private function rules(?int $id = null): array
    {
        $unique = $id ? "is_unique[courses.code,id,{$id}]" : 'is_unique[courses.code]';

        return [
            'name'      => ['label' => 'Nombre del curso', 'rules' => 'required|min_length[3]|max_length[100]'],
            'code'      => ['label' => 'Código del curso', 'rules' => "required|alpha_dash|max_length[20]|{$unique}"],
            'credits'   => ['label' => 'Créditos', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[10]'],
            'career_id' => ['label' => 'Carrera', 'rules' => 'required|is_not_unique[careers.id]'],
        ];
    }

    public function index()
    {
        return view('courses/index', [
            'title'   => 'Cursos',
            'courses' => $this->courses->withCareer()->orderBy('courses.code')->findAll(),
        ]);
    }

    public function create()
    {
        return view('courses/form', [
            'title'   => 'Nuevo curso',
            'course'  => null,
            'careers' => $this->careers->orderBy('name')->findAll(),
        ]);
    }

    public function store()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput();
        }

        $this->courses->insert($this->request->getPost(['name', 'code', 'credits', 'career_id']));

        return redirect()->to('/courses')->with('message', 'Curso creado correctamente.');
    }

    public function edit(int $id)
    {
        $course = $this->courses->find($id) ?? throw PageNotFoundException::forPageNotFound();

        return view('courses/form', [
            'title'   => 'Editar curso',
            'course'  => $course,
            'careers' => $this->careers->orderBy('name')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $this->courses->find($id) ?? throw PageNotFoundException::forPageNotFound();

        if (! $this->validate($this->rules($id))) {
            return redirect()->back()->withInput();
        }

        $this->courses->update($id, $this->request->getPost(['name', 'code', 'credits', 'career_id']));

        return redirect()->to('/courses')->with('message', 'Curso actualizado correctamente.');
    }

    public function delete(int $id)
    {
        $hasEnrollments = (new EnrollmentModel())->where('course_id', $id)->countAllResults() > 0;

        if ($hasEnrollments) {
            return redirect()->to('/courses')->with('error', 'No se puede eliminar: el curso tiene matrículas registradas.');
        }

        $this->courses->delete($id);

        return redirect()->to('/courses')->with('message', 'Curso eliminado.');
    }
}