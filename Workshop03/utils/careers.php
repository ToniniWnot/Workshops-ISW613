<?php
// Funciones de datos de las carreras.

// Devuelve todas las carreras (id y nombre) para llenar el dropdown del formulario de cursos
function getCareers(PDO $conn): array
{
    return $conn->query('SELECT id, nombre FROM carreras ORDER BY nombre')->fetchAll();
}