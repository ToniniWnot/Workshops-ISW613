<?php
// Modelo de carreras: consulta SQL para llenar el dropdown del formulario de cursos.
final class Career
{
    // Devuelve todas las carreras (id y nombre) ordenadas por nombre
    public static function all(): array
    {
        return Database::pdo()->query('SELECT id, nombre FROM carreras ORDER BY nombre')->fetchAll();
    }
}