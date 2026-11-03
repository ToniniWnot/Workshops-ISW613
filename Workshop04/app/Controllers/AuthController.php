<?php
// Controlador de autenticación: login, logout y registro.
// No contiene SQL ni HTML: usa el modelo User, la clase Auth y las vistas de auth/.
final class AuthController extends Controller
{
    // Mensajes de error del registro, que llegan en el parámetro ?error=
    private const REGISTER_ERRORS = [
        'invalid_input' => 'Datos inválidos. El usuario puede tener hasta 50 caracteres y la contraseña entre 8 y 72.',
        'username_taken' => 'Ese usuario ya está en uso. Elige otro.',
        'server_error' => 'No se pudo completar el registro. Inténtalo más tarde.',
    ];

    // Pantalla de login, es la página de inicio del sitio (GET /index.php)
    public function showLogin(): void
    {
        $this->render('auth/login', [
            'error' => isset($_GET['error']),
            'registered' => isset($_GET['registered']),
            'expired' => isset($_GET['expired']),
        ]);
    }

    // Procesa el formulario de login: valida, verifica la contraseña y abre la sesión (POST)
    public function login(): void
    {
        $this->requirePost('/index.php');

        $data = User::validateLogin($_POST);

        if ($data === null) {
            $this->redirect('/index.php?error=1');
        }

        try {
            $user = User::authenticate($data['username'], $data['password']);
        } catch (PDOException $exception) {
            // Si la base falla, se registra el detalle en el log y el usuario solo vuelve al login
            error_log('Login database error: ' . $exception->getMessage());
            $this->redirect('/index.php?error=1');
        }

        // Mismo mensaje si el usuario no existe o si la contraseña es incorrecta
        if ($user === null) {
            $this->redirect('/index.php?error=1');
        }

        // Credenciales válidas: recién ahora se abre la sesión
        Auth::login($user['id'], $user['username']);
        $this->redirect('/pages/profile.php');
    }

    // Cierra la sesión del usuario y lo manda al login (POST, desde el botón "Cerrar sesión")
    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Auth::destroy();
        }

        $this->redirect('/index.php');
    }


    // Pantalla de registro con username, contraseña y provincia (GET /pages/register.php)
    public function showRegister(): void
    {
        $errorMessage = $this->messageFor('error', self::REGISTER_ERRORS);

        // Provincias que llenan el dropdown
        try {
            $provincias = Province::all();
        } catch (PDOException $exception) {
            error_log('Register provinces error: ' . $exception->getMessage());
            $provincias = [];
            $errorMessage = $errorMessage ?? 'No se pudieron cargar las provincias. Inténtalo más tarde.';
        }

        $this->render('auth/register', [
            'errorMessage' => $errorMessage,
            'provincias' => $provincias,
        ]);
    }

    // Procesa el formulario de registro: valida, guarda el usuario y vuelve al login (POST)
    public function register(): void
    {
        $this->requirePost('/pages/register.php');

        $data = User::validateRegister($_POST);

        if ($data === null) {
            $this->redirect('/pages/register.php?error=invalid_input');
        }

        try {
            User::create($data);
        } catch (PDOException $exception) {
            // User::errorCode() distingue usuario repetido, provincia inexistente y error del servidor
            $this->redirect('/pages/register.php?error=' . User::errorCode($exception));
        }

        // Registro correcto: volvemos al login con un mensaje de éxito
        $this->redirect('/index.php?registered=1');
    }
}