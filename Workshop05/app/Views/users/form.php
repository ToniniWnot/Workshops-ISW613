<?php $editing = $user !== null; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editing ? 'Editar' : 'Nuevo' ?> usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4" style="max-width: 600px;">

    <h1 class="mb-3"><?= $editing ? 'Editar usuario' : 'Nuevo usuario' ?></h1>

    <?php if (session('_ci_validation_errors')): ?>
        <div class="alert alert-danger"><?= validation_list_errors() ?></div>
    <?php endif ?>

    <form action="<?= $editing ? site_url("users/{$user['id']}/update") : site_url('users') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control"
                   value="<?= esc(old('name', $user['name'] ?? '')) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= esc(old('email', $user['email'] ?? '')) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">
                Contraseña <?= $editing ? '<small class="text-muted">(déjala vacía para no cambiarla)</small>' : '' ?>
            </label>
            <input type="password" name="password" class="form-control">
        </div>

        <button class="btn btn-success"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
        <a href="<?= site_url('users') ?>" class="btn btn-secondary">Cancelar</a>
    </form>

</body>
</html>