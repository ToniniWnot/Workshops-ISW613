<?php
// Base de todos los controladores: carga vistas, redirige y lee parámetros de la petición.
abstract class Controller
{
    // Carga una vista de app/Views y le entrega los datos como variables (['x' => 1] llega como $x)
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    // Redirige a otra URL y detiene el script
    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    // Solo se acepta POST; si alguien abre la URL directamente, lo mandamos a otra página
    protected function requirePost(string $redirectTo): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($redirectTo);
        }
    }

    // Termina la petición con un error 500 y un mensaje sencillo
    protected function serverError(string $message): never
    {
        http_response_code(500);
        exit($message);
    }

    // Convierte un código de la URL (?error=..., ?success=...) en su mensaje fijo, o null si no existe
    protected function messageFor(string $param, array $messages): ?string
    {
        $code = $_GET[$param] ?? '';

        return is_string($code) ? ($messages[$code] ?? null) : null;
    }

    // Devuelve el valor como entero positivo, o false si no lo es (también rechaza arreglos)
    protected function positiveInt(mixed $value): int|false
    {
        return is_string($value)
            ? filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
            : false;
    }
}