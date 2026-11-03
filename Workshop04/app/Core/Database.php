<?php
// Conexión única a la base de datos. Los modelos la piden con Database::pdo().

final class Database {
    // Guarda la conexión para crearla una sola vez por petición
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['name'] . ';charset=' . $config['charset'];

            try {
                self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
                    // Si una consulta falla, se lanza una excepción en lugar de fallar en silencio
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // Cada fila llega como arreglo con los nombres de las columnas: $user['username']
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $exception) {
                // El detalle del error va al log del server, no a la pantalla del usuario
                error_log('Database connection error: ' . $exception->getMessage());
                exit('No se pudo conectar con la base de datos.');
            }
        }

        return self::$pdo;
    }
}