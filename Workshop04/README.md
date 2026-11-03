# Workshop 4 - Gestión de cursos con patrón MVC (PHP + MySQL)

## Descripción
El Workshop 3 (registro, login por sesión, perfil y CRUD de cursos) reorganizado con el patrón
**Modelo-Vista-Controlador**. La aplicación hace exactamente lo mismo que antes y las URLs no cambiaron;
lo que cambió es **dónde vive cada pieza de código**: el SQL y las reglas en los modelos, el HTML en las
vistas, y la coordinación en los controladores.

El router **no se implementa** (el enunciado lo difiere): cada URL apunta a un archivo pequeño de
`public/` que llama a un método de un controlador.

## Tecnologías
HTML, Bootstrap 4.5.2 (CDN), PHP 8.2 (clases), Apache 2.4 (XAMPP), MariaDB/MySQL, PDO.

## El patrón MVC en este proyecto

| Capa | Hace | No hace | Dónde está |
|---|---|---|---|
| **Modelo** | Consultas SQL, validación de datos y reglas | No imprime HTML, no lee `$_POST` ni `$_SESSION` | `app/Models/` |
| **Vista** | Muestra los datos que recibe | No consulta la BD, no valida, no lee la petición | `app/Views/` |
| **Controlador** | Lee la petición, llama al modelo y elige vista o redirección | No tiene SQL ni HTML | `app/Controllers/` |

Además hay un **núcleo** (`app/Core/`) con lo que comparten todas las capas: la conexión (`Database`), la
sesión (`Auth`) y la clase base de los controladores (`Controller`).

## Estructura del proyecto
```
Workshop04/
├── public/                        Raíz del sitio: lo único accesible por URL
│   ├── index.php                  Login
│   ├── pages/                     register, profile, courses, course_form
│   └── actions/                   login, logout, register, update_profile,
│                                  create_course, update_course, delete_course
├── app/
│   ├── bootstrap.php              Autoload de clases y función e() para escapar HTML
│   ├── Core/
│   │   ├── Database.php           Conexión PDO única (Database::pdo())
│   │   ├── Auth.php               Sesión: start, login, destroy, requireLogin
│   │   └── Controller.php         Base de controladores: render, redirect, requirePost...
│   ├── Controllers/
│   │   ├── AuthController.php     Login, logout y registro
│   │   ├── ProfileController.php  Perfil
│   │   └── CourseController.php   Listado, formulario y acciones de cursos
│   ├── Models/
│   │   ├── User.php, Province.php
│   │   └── Course.php, Career.php
│   └── Views/
│       ├── layouts/               header, navbar, footer
│       ├── auth/                  login, register
│       ├── profile/               show
│       └── courses/               index, form
├── config/database.php            Datos de conexión a la base de datos
├── database/workshop4.sql         Base de datos completa (tablas y datos iniciales)
└── README.md
```
`app/`, `config/` y `database/` quedan **fuera de `public/`**: no se pueden abrir desde el navegador.

## Cómo viaja una petición
Ejemplo: `GET /pages/courses.php`

1. `public/pages/courses.php` carga `app/bootstrap.php` (autoload) y llama a `CourseController::index()`.
2. El controlador ejecuta `Auth::requireLogin()`: sin sesión redirige al login.
3. Pide los datos al modelo: `Course::all()`, que usa `Database::pdo()`.
4. Llama a `render('courses/index', [...])`, que carga la vista con esos datos como variables.
5. La vista dibuja la tabla con `foreach` y escapa cada dato con `e()`.

Las acciones (POST) siguen el mismo camino, pero terminan en una redirección: el controlador valida con el
modelo, guarda, y redirige al listado con un código (`?success=created`).

## De dónde sale cada pieza (Workshop 3 → Workshop 4)

| Workshop 3 | Workshop 4 |
|---|---|
| `utils/database.php` | `Core/Database.php` + `config/database.php` |
| `utils/auth.php` | `Core/Auth.php` |
| `utils/courses.php`, `utils/careers.php` | `Models/Course.php`, `Models/Career.php` |
| SQL suelto en login, registro y perfil | `Models/User.php`, `Models/Province.php` |
| HTML de `pages/*.php` y `index.php` | `Views/**` |
| PHP de `pages/*.php` y `actions/*.php` | `Controllers/*` |
| `pages/common/*` | `Views/layouts/*` |

## Base de datos
Base de datos `workshop4`, creada con `database/workshop4.sql` (incluye provincias, 4 carreras y 3 cursos de
ejemplo). Tablas: `users`, `provincias`, `carreras` y `cursos`. Es la misma del Workshop 3.

## Cómo ejecutarlo
1. Crear la base de datos:
   ```
   /Applications/XAMPP/xamppfiles/bin/mysql -u root < database/workshop4.sql
   ```
2. Apache con un virtual host cuya raíz sea la carpeta **`public`**:
   ```
   <VirtualHost *:80>
       DocumentRoot ".../Workshop04/public"
       ServerName workshop4.local
       <Directory ".../Workshop04/public">
           Require all granted
       </Directory>
   </VirtualHost>
   ```
   y la línea `127.0.0.1 workshop4.local` en `/etc/hosts`. Abrir `http://workshop4.local/`.
3. Alternativa sin Apache: `php -S localhost:8000 -t public` desde `Workshop04/`.
4. Los datos de conexión están en `config/database.php`.

## Seguridad aplicada
| Aspecto | Qué se hizo |
|---|---|
| Archivos internos | Solo `public/` es accesible; `/app/...`, `/config/...` y `/database/...` dan 404 |
| XSS | Todo dato dinámico se imprime con `e()` (`htmlspecialchars` con comillas y UTF-8); los mensajes salen de listas fijas, nunca de la URL |
| SQL injection | Consultas preparadas con PDO en todos los modelos |
| Acceso | `Auth::requireLogin()` en cada método protegido del controlador, tanto pantallas como acciones |
| Eliminar | Solo por POST; un GET a `delete_course.php` no borra nada |
| Validación | Cada modelo valida tipo, largo y rango (`Course::validate`, `User::validateRegister`...) y rechaza arreglos |
| Ids manipulados | Los ids se validan como enteros positivos y se comprueba que el curso exista; el perfil usa el id de la sesión, nunca del formulario |
| Contraseñas | `password_hash` al guardar y `password_verify` al entrar, con el mismo mensaje si falla el usuario o la clave |
| Sesión | Cookie `HttpOnly` y `SameSite=Lax`, id nuevo al iniciar sesión, caducidad a los 30 minutos de inactividad |

## Decisiones de diseño
- **Clases con métodos estáticos en los modelos** (`Course::all()`, `User::find()`), como en el ejemplo de la
  unidad. No se crean objetos de modelo porque no guardan estado.
- **Controladores como clases** que heredan de `Controller`, para compartir `render`, `redirect` y la validación de ids.
- **Autoload simple** (`spl_autoload_register`) en lugar de `require_once` encadenados; sin espacios de nombres, para no complicar.
- **Sin router**: los archivos de `public/` son el reemplazo manual; cuando se agregue un router, esos archivos desaparecen y los controladores no cambian.
- **Las vistas ponen su título y el menú activo**, porque son parte de la presentación; el controlador solo entrega datos.
- **La validación vive en el modelo**, porque son reglas sobre los datos (por ejemplo, créditos de 1 a 20).
- **Mismas URLs que el Workshop 3**, para no cambiar enlaces ni formularios.

## Pruebas realizadas
- Registro, inicio de sesión, perfil, actualización del perfil y cierre de sesión desde el navegador.
- Crear, editar y eliminar cursos desde el navegador.
- Sin sesión, perfil, cursos y las acciones POST redirigen al login y no hacen cambios.
- Código de curso repetido, carrera inexistente, créditos fuera de rango, campos vacíos y arreglos en el POST.
- Eliminar con GET no borra; eliminar un curso que ya no existe muestra un aviso.
- Texto con `<script>` en el nombre de un curso: se muestra como texto, no se ejecuta.
- Un `?error=` inventado en la URL no muestra ningún texto.
- Abrir `/app/Models/User.php` o `/config/database.php` por URL devuelve 404.

## Limitaciones conocidas
- No hay router ni front controller: cada URL tiene su archivo en `public/`.
- Cualquier usuario con sesión puede editar o eliminar cualquier curso (no hay roles).
- Eliminar es definitivo, no hay papelera.
- Si el servidor rechaza un formulario, los campos se vacían.
- No hay tokens CSRF; la protección depende de `SameSite=Lax`.
- La cookie de sesión no lleva `Secure` porque el entorno local usa HTTP.
- El listado no tiene paginación ni búsqueda.