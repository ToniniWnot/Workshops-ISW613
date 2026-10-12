# Workshop 1 - Login con PHP y MySQL

## Descripción
Pantalla de login que valida usuario y contraseña contra una tabla de MySQL.
- Credenciales correctas: redirige a `welcome.php` ("El usuario existe").
- Usuario inexistente o contraseña incorrecta: vuelve a `index.php` con el mensaje "Credenciales inválidas".

## Tecnologías
PHP, MySQL (mysqli), Apache (XAMPP), HTML.

## Base de datos
Base de datos `workshop1`, tabla `users`:

| Columna  | Tipo         | Notas                   |
|----------|--------------|-------------------------|
| id       | INT          | PK, AUTO_INCREMENT      |
| username | VARCHAR(50)  |                         |
| password | VARCHAR(255) |                         |

## Archivos
- `index.php`: formulario de login y mensaje de error (lee `$_GET['error']`).
- `login.php`: conexión a la BD, consulta preparada y redirección.
- `welcome.php`: muestra "El usuario existe".

## Flujo
1. El formulario envía username y password por POST a `login.php`.
2. `login.php` ejecuta una consulta preparada (`WHERE username = ? AND password = ?`).
3. Si devuelve filas, redirige a `welcome.php`; si no, a `index.php?error=1`.

## Pruebas realizadas
- Credenciales correctas: muestra "El usuario existe".
- Contraseña incorrecta: muestra "Credenciales inválidas".
- Usuario inexistente: muestra "Credenciales inválidas".

## Limitaciones conocidas
- La contraseña se guarda en texto plano (el enunciado pide compararla directamente).
- `welcome.php` no verifica sesión: se puede abrir escribiendo la URL.