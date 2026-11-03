<?php
// Controlador de cursos: lee la petición, llama a los modelos y elige la vista o la redirección.
// No contiene SQL ni HTML.
final class CourseController extends Controller
{
    // Mensajes que pueden enviar las acciones en los parámetros ?success= y ?error= del listado
    private const LIST_MESSAGES = [
        'success' => [
            'created' => 'El curso se creó correctamente.',
            'updated' => 'El curso se actualizó correctamente.',
            'deleted' => 'El curso se eliminó correctamente.',
        ],
        'error' => [
            'invalid_input' => 'La solicitud no es válida.',
            'not_found' => 'El curso no existe.',
            'server_error' => 'No se pudo completar la operación. Inténtalo más tarde.',
        ],
    ];

    // Mensajes de error del formulario, que llegan en el parámetro ?error=
    private const FORM_ERRORS = [
        'invalid_input' => 'Datos inválidos. Revisa que todos los campos estén completos y dentro de sus límites.',
        'code_taken' => 'Ese código de curso ya existe. Usa otro.',
        'invalid_career' => 'Selecciona una carrera válida.',
        'server_error' => 'No se pudo guardar el curso. Inténtalo más tarde.',
    ];

    // Listado de cursos (GET /pages/courses.php)
    public function index(): void
    {
        Auth::requireLogin();

        try {
            $courses = Course::all();
        } catch (PDOException $exception) {
            error_log('Courses list error: ' . $exception->getMessage());
            $this->serverError('No se pudieron cargar los cursos. Inténtalo más tarde.');
        }

        $this->render('courses/index', [
            'courses' => $courses,
            'successMessage' => $this->messageFor('success', self::LIST_MESSAGES['success']),
            'errorMessage' => $this->messageFor('error', self::LIST_MESSAGES['error']),
        ]);
    }

    // Formulario para crear (sin id en la URL) o editar (?id=...) un curso (GET /pages/course_form.php)
    public function form(): void
    {
        Auth::requireLogin();

        try {
            $course = null;

            // Con ?id=... estamos editando: validamos el id y cargamos el curso
            if (isset($_GET['id'])) {
                $id = $this->positiveInt($_GET['id']);

                if ($id === false) {
                    $this->redirect('/pages/courses.php?error=invalid_input');
                }

                $course = Course::find($id);

                if ($course === null) {
                    $this->redirect('/pages/courses.php?error=not_found');
                }
            }

            $careers = Career::all();
        } catch (PDOException $exception) {
            error_log('Course form error: ' . $exception->getMessage());
            $this->serverError('No se pudo cargar el formulario. Inténtalo más tarde.');
        }

        $this->render('courses/form', [
            'course' => $course,
            'careers' => $careers,
            'errorMessage' => $this->messageFor('error', self::FORM_ERRORS),
        ]);
    }


    // Procesa el formulario de "Crear curso": valida, guarda y redirige al listado (POST)
    public function create(): void
    {
        Auth::requireLogin();
        $this->requirePost('/pages/courses.php');

        // Validamos y limpiamos los datos; si algo no es válido, volvemos al formulario con un error
        $data = Course::validate($_POST);

        if ($data === null) {
            $this->redirect('/pages/course_form.php?error=invalid_input');
        }

        try {
            Course::create($data);
        } catch (PDOException $exception) {
            // Course::errorCode() distingue código repetido, carrera inexistente y error del servidor
            $this->redirect('/pages/course_form.php?error=' . Course::errorCode($exception));
        }

        $this->redirect('/pages/courses.php?success=created');
    }

    // Procesa el formulario de "Editar curso": valida, actualiza y redirige al listado (POST)
    public function update(): void
    {
        Auth::requireLogin();
        $this->requirePost('/pages/courses.php');

        // El id del curso llega en un campo oculto del formulario: debe ser un entero positivo
        $id = $this->positiveInt($_POST['id'] ?? null);

        if ($id === false) {
            $this->redirect('/pages/courses.php?error=invalid_input');
        }

        // Validamos y limpiamos los datos; si algo no es válido, volvemos al formulario de ese curso
        $data = Course::validate($_POST);

        if ($data === null) {
            $this->redirect('/pages/course_form.php?id=' . $id . '&error=invalid_input');
        }

        try {
            // Un UPDATE sobre un id inexistente no falla, así que comprobamos antes que el curso exista
            if (Course::find($id) === null) {
                $this->redirect('/pages/courses.php?error=not_found');
            }

            Course::update($id, $data);
        } catch (PDOException $exception) {
            // Course::errorCode() distingue código repetido, carrera inexistente y error del servidor
            $this->redirect('/pages/course_form.php?id=' . $id . '&error=' . Course::errorCode($exception));
        }

        $this->redirect('/pages/courses.php?success=updated');
    }

    // Procesa el botón "Eliminar" del listado: elimina el curso indicado y vuelve al listado (POST)
    public function delete(): void
    {
        Auth::requireLogin();
        $this->requirePost('/pages/courses.php');

        // El id del curso llega en un campo oculto del formulario: debe ser un entero positivo
        $id = $this->positiveInt($_POST['id'] ?? null);

        if ($id === false) {
            $this->redirect('/pages/courses.php?error=invalid_input');
        }

        try {
            // Si el curso ya no existe (por ejemplo, un doble clic), avisamos en vez de decir que se eliminó
            if (Course::find($id) === null) {
                $this->redirect('/pages/courses.php?error=not_found');
            }

            Course::delete($id);
        } catch (PDOException $exception) {
            error_log('Delete course error: ' . $exception->getMessage());
            $this->redirect('/pages/courses.php?error=server_error');
        }

        $this->redirect('/pages/courses.php?success=deleted');
    }
}