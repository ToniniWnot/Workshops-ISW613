<?php
// Se incluye al inicio de cada archivo de public/: carga las clases solas y define e().

// Autoload, cuando el código usa una clase que aún no se cargó, PHP la busca en estas carpetas
spl_autoload_register(function (string $class): void {
    foreach (['Core', 'Controllers', 'Models'] as $folder) {
        $file = __DIR__ . '/' . $folder . '/' . $class . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

// Escapa un valor antes de imprimirlo en HTML (evita XSS). Las vistas lo usan en todo dato dinámico.
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}