# Workshop 2 - Registro, login con sesión y perfil (PHP + MySQL)

## Descripción
Aplicación web donde un usuario puede registrarse eligiendo su provincia, iniciar sesión y
ver y actualizar sus datos en una pantalla de Perfil protegida por sesión.

- **Registro:** username, contraseña y provincia (dropdown cargado desde la base de datos).
- **Login:** valida las credenciales y abre una sesión (`$_SESSION`).
- **Perfil:** muestra el usuario de la sesión y permite cambiar el username y la provincia.
- **Acceso protegido:** sin una sesión válida y activa no se puede entrar al Perfil, ni siquiera escribiendo la URL.
- **Logout:** destruye la sesión.

## Tecnologías
HTML, Bootstrap 4.5.2 (CDN), PHP 8.2, Apache 2.4 (XAMPP), MariaDB/MySQL, PDO.

## Estructura del proyecto
```
Workshop02/
├── index.php                  Login
├── workshop2.sql              Base de datos (tablas y provincias)
├── actions/                   Scripts que procesan formularios y redirigen
│   ├── login.php
│   ├── register.php
│   ├── update_profile.php
│   └── logout.php
├── pages/                     Pantallas
│   ├── register.php
│   ├── profile.php
│   └── common/                Header y footer reutilizables
│       ├── header.php
│       └── footer.php
└── utils/
    ├── database.php           Conexión PDO
    └── auth.php               Funciones de sesión (iniciar, proteger, cerrar)
```

## Base de datos
Base de datos `workshop2`, creada con `workshop2.sql`.

**provincias**

| Columna | Tipo | Notas |
|---|---|---|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| nombre | VARCHAR(50) | UNIQUE |

**users**

| Columna | Tipo | Notas |
|---|---|---|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| username | VARCHAR(50) | UNIQUE |
| password_hash | VARCHAR(255) | Hash de la contraseña, nunca la contraseña |
| provincia_id | BIGINT UNSIGNED | FK a `provincias.id` |
| created_at / updated_at | TIMESTAMP | Los llena MySQL automáticamente |

## Flujo
1. `pages/register.php` carga las provincias y envía el formulario por POST a `actions/register.php`.
2. `actions/register.php` valida, guarda el hash de la contraseña y redirige al login con un mensaje de éxito.
3. `index.php` envía las credenciales por POST a `actions/login.php`.
4. `actions/login.php` busca al usuario, verifica la contraseña con `password_verify()`, abre la sesión y redirige al Perfil.
5. `pages/profile.php` llama a `requireLogin()`, muestra los datos y envía los cambios por POST a `actions/update_profile.php`.
6. `actions/logout.php` destruye la sesión y vuelve al login.

## Seguridad aplicada
| Aspecto | Qué se hizo |
|---|---|
| Contraseñas | `password_hash()` al registrar y `password_verify()` al iniciar sesión |
| SQL injection | Consultas preparadas con PDO en todas las consultas |
| Acceso por URL | `requireLogin()` al inicio de las páginas y acciones protegidas |
| Sesión válida | Si el usuario ya no existe en la base de datos, la sesión se destruye |
| Sesión activa | Caduca tras 30 minutos de inactividad (`SESSION_TIMEOUT`) |
| Fijación de sesión | `session_regenerate_id(true)` al iniciar sesión |
| Cookie de sesión | `HttpOnly` y `SameSite=Lax` (frena CSRF y el robo por JavaScript) |
| XSS | `htmlspecialchars()` en todo lo que se imprime |
| Edición de otros usuarios | El id sale de la sesión, nunca del formulario |
| Enumeración de usuarios | Mismo mensaje "Credenciales inválidas" si falla el usuario o la contraseña |
| Entradas manipuladas | Validación de tipo y largo en el servidor; la provincia se valida con la llave foránea |
| Logout | Por POST y destruyendo la sesión en el servidor |

## Pruebas realizadas
- Registro válido, username repetido, provincia inválida y contraseña corta.
- Login correcto, contraseña incorrecta y usuario inexistente.
- Acceso al Perfil sin sesión, con cookie falsa y tras cerrar sesión: redirige al login.
- Actualización de username y provincia, incluyendo un username que ya existe.
- Sesión caducada por inactividad y usuario eliminado con la sesión abierta.

## Limitaciones conocidas
- No hay tokens CSRF; la protección depende de `SameSite=Lax`.
- La cookie de sesión no lleva el atributo `Secure` porque el entorno local usa HTTP.
- No hay límite de intentos de login (fuerza bruta).
- El Perfil no permite cambiar la contraseña.
- Los usernames no distinguen mayúsculas (`Ana` y `ana` son el mismo).
- La conexión usa `root` sin contraseña, válido solo en XAMPP local.