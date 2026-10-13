<?php
// Procesa el formulario de login: valida, busca al usuario, verifica la contraseña y abre la sesión.
require_once __DIR__ . '/../utils/database.php';
require_once __DIR__ . '/../utils/auth.php';

// Solo se acepta POST; si alguien abre esta URL directamente, lo mandamos al login
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;

// Validamos que sean texto (un atacante podría enviar arreglos), que no estén vacíos y que el username no sea demasiado largo
if (
    !is_string($username)
    || !is_string($password)
    || trim($username) === ''
    || strlen($username) > 50
    || $password === ''
) {
    header('Location: /index.php?error=1');
    exit;
}

try {
    // Consulta preparada: los valores viajan separados del SQL (evita SQL injection)
    $statement = $conn->prepare(
        'SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1'
    );
    $statement->execute(['username' => trim($username)]);
    $user = $statement->fetch();
} catch (PDOException $exception) {
    // Si la base falla, se registra el detalle en el log y el usuario solo vuelve al login
    error_log('Login database error: ' . $exception->getMessage());
    header('Location: /index.php?error=1');
    exit;
}

// Mismo mensaje si el usuario no existe o si la contraseña es incorrecta (no revelamos cuál falló)
if ($user === false || !password_verify($password, $user['password_hash'])) {
    header('Location: /index.php?error=1');
    exit;
}

// Credenciales válidas: recién ahora se abre la sesión, con un id nuevo para evitar la fijación de sesión
startSession();
session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['last_activity'] = time();

header('Location: /pages/profile.php');
exit;