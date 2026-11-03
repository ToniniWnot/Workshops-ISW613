<?php
// Controlador del perfil: muestra y actualiza los datos del usuario con sesión iniciada.
// No contiene SQL ni HTML: usa los modelos User y Province, la clase Auth y la vista profile/show.
final class ProfileController extends Controller
{
    // Mensajes de error del perfil, que llegan en el parámetro ?error=
    private const ERRORS = [
        'invalid_input' => 'Datos inválidos. El usuario puede tener hasta 50 caracteres y debes elegir una provincia.',
        'username_taken' => 'Ese usuario ya está en uso. Elige otro.',
        'server_error' => 'No se pudieron guardar los cambios. Inténtalo más tarde.',
    ];

    // Perfil del usuario (GET /pages/profile.php)
    public function show(): void
    {
        Auth::requireLogin();

        try {
            // El usuario se identifica por el id guardado en la sesión
            $user = User::find(Auth::userId());
            $provincias = Province::all();
        } catch (PDOException $exception) {
            error_log('Profile database error: ' . $exception->getMessage());
            $this->serverError('No se pudo cargar tu perfil. Inténtalo más tarde.');
        }

        // Si el usuario ya no existe en la base de datos, la sesión deja de ser válida
        if ($user === null) {
            Auth::destroy();
            $this->redirect('/index.php');
        }

        $this->render('profile/show', [
            'currentUsername' => Auth::username(),
            'user' => $user,
            'provincias' => $provincias,
            'errorMessage' => $this->messageFor('error', self::ERRORS),
            'updated' => isset($_GET['updated']),
        ]);
    }

    // Procesa el formulario del perfil: valida y actualiza username y provincia (POST)
    public function update(): void
    {
        Auth::requireLogin();
        $this->requirePost('/pages/profile.php');

        $data = User::validateProfile($_POST);

        if ($data === null) {
            $this->redirect('/pages/profile.php?error=invalid_input');
        }

        try {
            // El id sale de la sesión, nunca del formulario: así nadie puede editar a otro usuario
            User::update(Auth::userId(), $data);
        } catch (PDOException $exception) {
            $this->redirect('/pages/profile.php?error=' . User::errorCode($exception));
        }

        // La sesión guarda el username, así que hay que mantenerla al día
        Auth::setUsername($data['username']);

        $this->redirect('/pages/profile.php?updated=1');
    }
}