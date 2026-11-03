<?php
// Sesión de usuario: iniciar, proteger páginas y cerrar. Se usa como Auth::requireLogin().
final class Auth
{
    // Segundos de inactividad tras los cuales la sesión deja de ser válida (30 minutos)
    private const SESSION_TIMEOUT = 1800;

    // Inicia la sesión con una cookie más segura
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'httponly' => true,  // JavaScript no puede leer la cookie (protege contra robo por XSS)
                'samesite' => 'Lax', // el navegador no envía la cookie en un POST desde otro sitio (frena CSRF)
            ]);
            session_start();
        }
    }

    // Abre la sesión de un usuario ya verificado, con un id nuevo para evitar la fijación de sesión
    public static function login(int $userId, string $username): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['last_activity'] = time();
    }

    // Cierra la sesión por completo: vacía los datos, borra la cookie y destruye la sesión en el servidor
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'],
        ]);
        session_destroy();
    }

    // Guardia de acceso: se llama al inicio de cada acción protegida
    public static function requireLogin(): void
    {
        self::start();

        // 1. Debe existir una sesión de usuario
        if (!isset($_SESSION['user_id'])) {
            header('Location: /index.php');
            exit;
        }

        // 2. La sesión debe estar activa: si pasó demasiado tiempo sin actividad, se cierra
        if (time() - ($_SESSION['last_activity'] ?? time()) > self::SESSION_TIMEOUT) {
            self::destroy();
            header('Location: /index.php?expired=1');
            exit;
        }

        // Cada visita renueva el contador de inactividad
        $_SESSION['last_activity'] = time();
    }

    // Id del usuario con sesión iniciada (solo usar después de requireLogin)
    public static function userId(): int
    {
        return (int) $_SESSION['user_id'];
    }

    // Nombre del usuario con sesión iniciada
    public static function username(): string
    {
        return (string) $_SESSION['username'];
    }

    // La sesión guarda el username, así que hay que mantenerlo al día si el usuario lo cambia
    public static function setUsername(string $username): void
    {
        $_SESSION['username'] = $username;
    }
}