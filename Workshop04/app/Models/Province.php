<?php
// Modelo de provincias: consulta SQL para llenar los dropdowns de registro y perfil.
final class Province
{
    // Devuelve todas las provincias (id y nombre) ordenadas por nombre
    public static function all(): array
    {
        return Database::pdo()->query('SELECT id, nombre FROM provincias ORDER BY nombre')->fetchAll();
    }
}