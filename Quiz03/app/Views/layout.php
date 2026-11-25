<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Quiz 3') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">Quiz 3</a>
            <div class="navbar-nav">
                <a class="nav-link" href="<?= site_url('/') ?>">Matrícula</a>
                <a class="nav-link" href="<?= site_url('courses') ?>">Cursos</a>
            </div>
        </div>
    </nav>

    <main class="container" style="max-width: 900px;">
        <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
        <?php endif ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif ?>

        <?php if (session('_ci_validation_errors')): ?>
            <div class="alert alert-danger"><?= validation_list_errors() ?></div>
        <?php endif ?>

        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>