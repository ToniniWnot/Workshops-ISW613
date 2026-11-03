# Workshop 4 - Gestión de cursos (PHP + MySQL)

## Descripción
Aplicación web con registro, login por sesión, perfil y una sección **Cursos** donde un usuario
autenticado puede listar, crear, editar y eliminar cursos. Parte del código del Workshop 2.

- **Menú de navegación:** Mi perfil, Cursos y Cerrar sesión (solo visible con sesión iniciada).
- **Listado de cursos** en una tabla con Nombre, Código, Carrera, Créditos y Descripción.
- **Editar** y **Eliminar** por cada curso. Eliminar usa **POST** y pide confirmación.
- **Crear Curso:** al guardar, redirige al listado.
- Todas las páginas y acciones de cursos exigen una sesión válida y activa.

## Tecnologías
HTML, Bootstrap 4.5.2 (CDN), PHP 8.2, Apache 2.4 (XAMPP), MariaDB/MySQL, PDO.

## Estructura del proyecto
```
Workshop04/
├── index.php                  Login
├── workshop4.sql              Base de datos completa (tablas y datos iniciales)
├── actions/                   Scripts que procesan formularios y redirigen
│   ├── login.php, logout.php, register.php, update_profile.php
│   ├── create_course.php
│   ├── update_course.php
│   └── delete_course.php
├── pages/                     Pantallas
│   ├── register.php, profile.php
│   ├── courses.php            Listado de cursos
│   ├── course_form.php        Formulario para crear y editar
│   └── common/                Partes reutilizables
│       ├── header.php, footer.php
│       └── navbar.php         Menú de navegación
└── utils/
    ├── database.php           Conexión PDO
    ├── auth.php               Funciones de sesión
    ├── courses.php            Consultas, validación y errores de cursos
    └── careers.php            Consulta de carreras
```

## Base de datos
Base de datos `workshop4`, creada con `workshop4.sql` (incluye 4 carreras y 3 cursos de ejemplo).

**cursos**

| Columna | Tipo | Notas |
|---|---|---|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| nombre | VARCHAR(100) | |
| codigo | VARCHAR(20) | UNIQUE |
| carrera_id | BIGINT UNSIGNED | FK a `carreras.id` |
| creditos | TINYINT UNSIGNED | CHECK entre 1 y 20 |
| descripcion | TEXT | |
| created_at / updated_at | TIMESTAMP | Los llena MySQL automáticamente |

**carreras:** `id` (PK) y `nombre` (UNIQUE). **users** y **provincias** se mantienen del Workshop 2.

Relaciones: un curso pertenece a una carrera (`cursos.carrera_id`) y un usuario pertenece a una
provincia (`users.provincia_id`). Una carrera con cursos no se puede borrar (llave foránea).

## Flujo de la sección Cursos
1. **Listar:** `pages/courses.php` llama a `getCourses()` y dibuja la tabla.
2. **Crear:** "Crear Curso" abre `pages/course_form.php`, que envía por POST a
   `actions/create_course.php`; al guardar redirige al listado.
3. **Editar:** "Editar" abre `pages/course_form.php?id=N` con los datos cargados, que envía por POST a
   `actions/update_course.php`.
4. **Eliminar:** cada fila tiene un formulario POST con el id oculto que envía a
   `actions/delete_course.php`; luego vuelve al listado.

## Seguridad aplicada
| Aspecto | Qué se hizo |
|---|---|
| XSS almacenado | Todo dato que viene de la base de datos se imprime con `htmlspecialchars()`; el mensaje de confirmación de eliminar es fijo para no meter datos del usuario en JavaScript |
| Eliminar | Solo por POST; un GET a `delete_course.php` no borra nada |
| SQL injection | Consultas preparadas con PDO en todas las consultas |
| Acceso | `requireLogin()` en las páginas **y** en las acciones, así que un POST directo sin sesión tampoco funciona |
| Validación | `validateCourseInput()` valida tipo, largo, rango de créditos y formato del código en el servidor |
| Ids manipulados | El id de un curso se valida como entero positivo, y se comprueba que el curso exista antes de editar o eliminar |
| Mensajes | La URL solo lleva códigos (`?success=`, `?error=`); el texto sale de una lista fija |
| Integridad en la BD | `UNIQUE` en el código, llave foránea en la carrera y `CHECK` en los créditos |
| Sesión y contraseñas | Igual que el Workshop 2: `password_hash`, cookie `HttpOnly` y `SameSite=Lax`, caducidad por inactividad |

## Decisiones de diseño
- **Tabla `carreras` con llave foránea** en lugar de un número escrito a mano: garantiza que el
  "Id de la Carrera" siempre exista. El listado muestra el nombre y el id.
- **Eliminar es un `DELETE` real** (el enunciado lo pide así). Como consecuencia, el código de un
  curso eliminado puede reutilizarse.
- **Un solo formulario** para crear y editar, para no duplicar el HTML.
- **Funciones de datos en `utils/`** (estilo de la semana 4 del profesor), reutilizadas por las
  tres acciones.

## Pruebas realizadas
- Crear, editar y eliminar cursos desde el navegador.
- Código de curso repetido (en mayúsculas y minúsculas), carrera inexistente, créditos fuera de
  rango, campos vacíos y entradas manipuladas (arreglos, ids inválidos).
- Eliminar con un GET no borra; eliminar un curso que ya no existe muestra un aviso.
- Texto con `<script>` y comillas en nombre y descripción: se muestra como texto, no se ejecuta.
- Acceso a Cursos sin sesión: redirige al login.

## Limitaciones conocidas
- Cualquier usuario con sesión puede editar o eliminar cualquier curso (no hay roles).
- Eliminar es definitivo, no hay papelera.
- Si el servidor rechaza un formulario, los campos se vacían.
- El listado no tiene paginación ni búsqueda.
- No hay tokens CSRF; la protección depende de `SameSite=Lax`.
- La cookie de sesión no lleva `Secure` porque el entorno local usa HTTP.