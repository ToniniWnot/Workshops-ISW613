<?php
// Cierra la sesión del usuario y lo manda al login.
require_once __DIR__ . '/../utils/auth.php';

// Solo se acepta POST (el botón "Cerrar sesión" del perfil)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    startSession();
    destroySession();
}

header('Location: /index.php');
exit;