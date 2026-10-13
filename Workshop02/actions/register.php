<?php
// Procesa el formulario de registro: valida, guarda el usuario con la contraseña hasheada y redirige.
require_once __DIR__ . '/../utils/database.php';

// Solo se acepta POST; si alguien abre esta URL directamente, lo mandamos al formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/register.php');
    exit;
}

$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;
$provinciaId = $_POST['provincia_id'] ?? null;

// Validamos tipo y largo. La contraseña va de 8 a 72 caracteres porque bcrypt ignora lo que pase de 72.
if (
    !is_string($username)
    || !is_string($password)
    || !is_string($provinciaId)
    || trim($username) === ''
    || strlen($username) > 50
    || strlen($password) < 8
    || strlen($password) > 72
    || filter_var($provinciaId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false
) {
    header('Location: /pages/register.php?error=invalid_input');
    exit;
}

try {
    // Consulta preparada. Solo se guarda el hash de la contraseña, nunca la contraseña.
    $statement = $conn->prepare(
        'INSERT INTO users (username, password_hash, provincia_id)
         VALUES (:username, :password_hash, :provincia_id)'
    );
    $statement->execute([
        'username' => trim($username),
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'provincia_id' => (int) $provinciaId,
    ]);
} catch (PDOException $exception) {
    // El código de error de MySQL (errorInfo[1]) indica qué restricción de la tabla falló
    $mysqlCode = $exception->errorInfo[1] ?? 0;

    if ($mysqlCode === 1062) { // UNIQUE: el username ya existe
        header('Location: /pages/register.php?error=username_taken');
        exit;
    }

    if ($mysqlCode === 1452) { // FOREIGN KEY: la provincia no existe
        header('Location: /pages/register.php?error=invalid_input');
        exit;
    }

    error_log('Register database error: ' . $exception->getMessage());
    header('Location: /pages/register.php?error=server_error');
    exit;
}

// Registro correcto: volvemos al login con un mensaje de éxito
header('Location: /index.php?registered=1');
exit;