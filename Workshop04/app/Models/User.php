<?php
// Modelo de usuarios: consultas SQL, validación, verificación de contraseña y traducción de errores.
// No imprime HTML, no lee $_POST ni $_SESSION: recibe los datos que le entrega el controlador.
final class User
{
    // Verifica las credenciales. Devuelve el usuario (id y username) o null si no son correctas.
    public static function authenticate(string $username, string $password): ?array
    {
        $statement = Database::pdo()->prepare(
            'SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1'
        );
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        // Mismo resultado si el usuario no existe o si la contraseña es incorrecta (no revelamos cuál falló)
        if ($user === false || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        return ['id' => (int) $user['id'], 'username' => $user['username']];
    }

    // Busca un usuario por id (username y provincia). Devuelve null si no existe.
    public static function find(int $id): ?array
    {
        $statement = Database::pdo()->prepare(
            'SELECT username, provincia_id FROM users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);

        return $statement->fetch() ?: null;
    }

    // Inserta un usuario nuevo y devuelve su id. Solo se guarda el hash de la contraseña, nunca la contraseña.
    public static function create(array $data): int
    {
        $pdo = Database::pdo();
        $statement = $pdo->prepare(
            'INSERT INTO users (username, password_hash, provincia_id)
             VALUES (:username, :password_hash, :provincia_id)'
        );
        $statement->execute([
            'username' => $data['username'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'provincia_id' => $data['provincia_id'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    // Actualiza username y provincia de un usuario. $data viene de User::validateProfile().
    public static function update(int $id, array $data): void
    {
        $statement = Database::pdo()->prepare(
            'UPDATE users SET username = :username, provincia_id = :provincia_id WHERE id = :id'
        );
        $statement->execute([
            'username' => $data['username'],
            'provincia_id' => $data['provincia_id'],
            'id' => $id,
        ]);
    }


    // Valida el formulario de login. Devuelve los datos limpios o null si algo no es válido.
    public static function validateLogin(array $input): ?array
    {
        $username = $input['username'] ?? null;
        $password = $input['password'] ?? null;

        // Deben ser texto (un atacante podría enviar arreglos), no estar vacíos y el username no ser demasiado largo
        if (
            !is_string($username)
            || !is_string($password)
            || trim($username) === ''
            || strlen($username) > 50
            || $password === ''
        ) {
            return null;
        }

        return ['username' => trim($username), 'password' => $password];
    }

    // Valida username y provincia. Lo comparten el registro y el perfil.
    public static function validateProfile(array $input): ?array
    {
        $username = $input['username'] ?? null;
        $provinciaId = $input['provincia_id'] ?? null;

        if (
            !is_string($username)
            || !is_string($provinciaId)
            || trim($username) === ''
            || strlen($username) > 50
        ) {
            return null;
        }

        $provinciaId = filter_var($provinciaId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($provinciaId === false) {
            return null;
        }

        return ['username' => trim($username), 'provincia_id' => $provinciaId];
    }

    // Valida el formulario de registro: username y provincia, más la contraseña.
    public static function validateRegister(array $input): ?array
    {
        $data = self::validateProfile($input);
        $password = $input['password'] ?? null;

        // La contraseña va de 8 a 72 caracteres porque bcrypt ignora lo que pase de 72
        if ($data === null || !is_string($password) || strlen($password) < 8 || strlen($password) > 72) {
            return null;
        }

        return $data + ['password' => $password];
    }

    // Traduce un error de MySQL al código de error que mostrará la página
    public static function errorCode(PDOException $exception): string
    {
        $mysqlCode = $exception->errorInfo[1] ?? 0;

        if ($mysqlCode === 1062) { // UNIQUE: el username ya existe
            return 'username_taken';
        }

        if ($mysqlCode === 1452) { // FOREIGN KEY: la provincia no existe
            return 'invalid_input';
        }

        error_log('User database error: ' . $exception->getMessage());
        return 'server_error';
    }
}