<?php
// Procesa el formulario del perfil: valida y actualiza username y provincia del usuario en sesión.
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/database.php';

// Solo usuarios con sesión válida y activa pueden llegar más allá de esta línea
requireLogin();

// Solo se acepta POST; si alguien abre esta URL directamente, lo mandamos al perfil
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/profile.php');
    exit;
}

$username = $_POST['username'] ?? null;
$provinciaId = $_POST['provincia_id'] ?? null;

// Validamos tipo y largo, igual que en el registro
if (
    !is_string($username)
    || !is_string($provinciaId)
    || trim($username) === ''
    || strlen($username) > 50
    || filter_var($provinciaId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false
) {
    header('Location: /pages/profile.php?error=invalid_input');
    exit;
}

try {
    // El id sale de la sesión, nunca del formulario: así nadie puede editar a otro usuario
    $statement = $conn->prepare(
        'UPDATE users SET username = :username, provincia_id = :provincia_id WHERE id = :id'
    );
    $statement->execute([
        'username' => trim($username),
        'provincia_id' => (int) $provinciaId,
        'id' => $_SESSION['user_id'],
    ]);
} catch (PDOException $exception) {
    // El código de error de MySQL (errorInfo[1]) indica qué restricción de la tabla falló
    $mysqlCode = $exception->errorInfo[1] ?? 0;

    if ($mysqlCode === 1062) { // UNIQUE: otro usuario ya tiene ese username
        header('Location: /pages/profile.php?error=username_taken');
        exit;
    }

    if ($mysqlCode === 1452) { // FOREIGN KEY: la provincia no existe
        header('Location: /pages/profile.php?error=invalid_input');
        exit;
    }

    error_log('Update profile database error: ' . $exception->getMessage());
    header('Location: /pages/profile.php?error=server_error');
    exit;
}

// La sesión guarda el username, así que hay que mantenerla al día
$_SESSION['username'] = trim($username);

header('Location: /pages/profile.php?updated=1');
exit;