<?php
// Funciones de sesión reutilizables: iniciar, proteger páginas y cerrar sesión.

// Segundos de inactividad tras los cuales la sesión deja de ser válida (30 minutos)
const SESSION_TIMEOUT = 1800;

// Inicia la sesión con una cookie más segura
function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,  // JavaScript no puede leer la cookie (protege contra robo por XSS)
            'samesite' => 'Lax', // el navegador no envía la cookie en un POST desde otro sitio (frena CSRF)
        ]);
        session_start();
    }
}

// Cierra la sesión por completo: vacía los datos, borra la cookie y destruye la sesión en el servidor
function destroySession(): void
{
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

// Guardia de acceso: se llama al inicio de cada página protegida
function requireLogin(): void
{
    startSession();

    // 1. Debe existir una sesión de usuario
    if (!isset($_SESSION['user_id'])) {
        header('Location: /index.php');
        exit;
    }

    // 2. La sesión debe estar activa: si pasó demasiado tiempo sin actividad, se cierra
    if (time() - ($_SESSION['last_activity'] ?? time()) > SESSION_TIMEOUT) {
        destroySession();
        header('Location: /index.php?expired=1');
        exit;
    }

    // Cada visita renueva el contador de inactividad
    $_SESSION['last_activity'] = time();
}