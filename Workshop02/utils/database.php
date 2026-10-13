<?php

try {
    $conn = new PDO('mysql:host=localhost;dbname=workshop2;charset=utf8mb4', 'root', '', [
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