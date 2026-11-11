
Readme · MD
# Workshop 05 — CRUD de Usuarios con CodeIgniter 4
 
Curso **ISW613**. Aplicación web en **CodeIgniter 4** que implementa un CRUD (crear, listar, editar y eliminar) de usuarios sobre una base de datos **MySQL**.
 
## Tecnologías
 
- PHP 8.2 o superior (instalado con Homebrew)
- CodeIgniter 4.7
- Composer
- MySQL (XAMPP)
- Bootstrap 5 (vía CDN) para las vistas
## Funcionalidades
 
- Listado de usuarios ordenado del más reciente al más antiguo
- Creación de usuarios con validación de nombre, email y contraseña
- Edición de usuarios (la contraseña solo cambia si se escribe una nueva)
- Eliminación con confirmación
- Contraseñas encriptadas con `password_hash()` antes de guardarse
- Email único por usuario
- Protección CSRF en los formularios y mensajes de confirmación tras cada acción
## Instalación
 
### 1. Requisitos previos (macOS)
 
```bash
brew install php composer
```
 
Verificar:
 
```bash
php -v
composer --version
```
 
### 2. Clonar el repositorio e instalar dependencias
 
```bash
git clone <url-del-repositorio>
cd Workshops-ISW613/Workshop05
composer install
```
 
### 3. Configurar el entorno
 
```bash
cp env .env
```
 
Editar `.env` y descomentar o ajustar:
 
```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
 
database.default.hostname = 127.0.0.1
database.default.database = workshop05
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```
 
> Se usa `127.0.0.1` en lugar de `localhost` para que el PHP de Homebrew se conecte por TCP a la MySQL de XAMPP.
 
### 4. Crear la base de datos y la tabla
 
Con MySQL encendido en XAMPP:
 
```bash
php spark db:create workshop05
php spark migrate
```
 
### 5. Levantar el servidor
 
```bash
php spark serve
```
 
Abrir **http://localhost:8080/users**. Para detener el servidor: `Ctrl + C`.
 
## Estructura del CRUD
 
| Archivo | Descripción |
|---|---|
| `app/Database/Migrations/*_CreateUsersTable.php` | Crea la tabla `users` |
| `app/Models/UserModel.php` | Modelo con timestamps automáticos y hash de contraseña |
| `app/Controllers/Users.php` | Lógica del CRUD y validaciones |
| `app/Config/Routes.php` | Rutas del módulo de usuarios |
| `app/Views/users/index.php` | Listado de usuarios |
| `app/Views/users/form.php` | Formulario compartido para crear y editar |
 
### Tabla `users`
 
| Campo | Tipo | Detalle |
|---|---|---|
| `id` | INT UNSIGNED | Llave primaria, autoincremental |
| `name` | VARCHAR(100) | Requerido, mínimo 3 caracteres |
| `email` | VARCHAR(150) | Requerido, único |
| `password` | VARCHAR(255) | Encriptada, mínimo 6 caracteres |
| `created_at` | DATETIME | Automático |
| `updated_at` | DATETIME | Automático |
 
### Rutas
 
| Método | URL | Acción |
|---|---|---|
| GET | `/users` | Listar usuarios |
| GET | `/users/new` | Formulario de creación |
| POST | `/users` | Guardar nuevo usuario |
| GET | `/users/{id}/edit` | Formulario de edición |
| POST | `/users/{id}/update` | Actualizar usuario |
| POST | `/users/{id}/delete` | Eliminar usuario |
 
## Problemas comunes
 
- **`Unable to connect to the database`**: MySQL no está encendido en XAMPP, o el `hostname` es `localhost` en lugar de `127.0.0.1`.
- **`404` en `/users`**: revisar las rutas en `app/Config/Routes.php`.
- **`The action you requested is not allowed`**: el token CSRF expiró; recargar el formulario.
- **`Could not resolve host` al usar Composer**: problema de DNS. Probar `networksetup -setdnsservers Wi-Fi 8.8.8.8 1.1.1.1`.