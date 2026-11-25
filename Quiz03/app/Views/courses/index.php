<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Cursos</h1>
    <a href="<?= site_url('courses/new') ?>" class="btn btn-primary">+ Nuevo curso</a>
</div>

<table class="table table-striped align-middle">
    <thead>
        <tr><th>Código</th><th>Nombre</th><th>Créditos</th><th>Carrera</th><th class="text-end">Acciones</th></tr>
    </thead>
    <tbody>
    <?php if (empty($courses)): ?>
        <tr><td colspan="5" class="text-center text-muted">No hay cursos registrados.</td></tr>
    <?php endif ?>

    <?php foreach ($courses as $c): ?>
        <tr>
            <td><?= esc($c['code']) ?></td>
            <td><?= esc($c['name']) ?></td>
            <td><?= esc($c['credits']) ?></td>
            <td><?= esc($c['career_name']) ?></td>
            <td class="text-end">
                <a href="<?= site_url("courses/{$c['id']}/edit") ?>" class="btn btn-sm btn-warning">Editar</a>
                <form action="<?= site_url("courses/{$c['id']}/delete") ?>" method="post" class="d-inline"
                      onsubmit="return confirm('¿Eliminar este curso?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>