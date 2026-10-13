<?php
// Cabecera común de todas las páginas: abre <html> y <head> y carga Bootstrap.
// Cada página define $pageTitle antes de incluir este archivo; si no lo hace, usa el título por defecto.
$pageTitle = $pageTitle ?? 'Workshop 2';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- htmlspecialchars evita que un título con caracteres especiales rompa el HTML (XSS) -->
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>