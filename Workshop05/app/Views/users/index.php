<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Usuarios</h1>
        <a href="<?= site_url('users/new') ?>" class="btn btn-primary">+ Nuevo usuario</a>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
    <?php endif ?>

    <table class="table table-striped align-middle">
        <thead>
            <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Creado</th><th class="text-end">Acciones</th></tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="5" class="text-center text-muted">No hay usuarios todavía.</td></tr>
        <?php endif ?>

        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= esc($u['name']) ?></td>
                <td><?= esc($u['email']) ?></td>
                <td><?= esc($u['created_at']) ?></td>
                <td class="text-end">
                    <a href="<?= site_url("users/{$u['id']}/edit") ?>" class="btn btn-sm btn-warning">Editar</a>
                    <form action="<?= site_url("users/{$u['id']}/delete") ?>" method="post" class="d-inline"
                          onsubmit="return confirm('¿Eliminar este usuario?')">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

</body>
</html>