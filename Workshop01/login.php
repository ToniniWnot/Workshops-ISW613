<?php

$conexion = mysqli_connect("localhost", "root", "", "workshop1");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ? AND password = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $password);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) > 0) {
    // Credenciales válidas, redirigir a la página de bienvenida
    header("Location: welcome.php");
    exit();
} else {
    // Credenciales inválidas, redirigir de vuelta al formulario de login con un mensaje de error
    header("Location: index.php?error=1");
    exit();
}   