<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0">Comprobante de matrícula #<?= $enrollment['id'] ?></h4>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-4">Estudiante</dt>
            <dd class="col-sm-8"><?= esc($enrollment['student_name']) ?></dd>

            <dt class="col-sm-4">Carné</dt>
            <dd class="col-sm-8"><?= esc($enrollment['student_card']) ?></dd>

            <dt class="col-sm-4">Curso</dt>
            <dd class="col-sm-8"><?= esc($enrollment['course_code']) ?> — <?= esc($enrollment['course_name']) ?></dd>

            <dt class="col-sm-4">Créditos</dt>
            <dd class="col-sm-8"><?= esc($enrollment['credits']) ?></dd>

            <dt class="col-sm-4">Carrera</dt>
            <dd class="col-sm-8"><?= esc($enrollment['career_name']) ?></dd>

            <dt class="col-sm-4">Fecha de matrícula</dt>
            <dd class="col-sm-8"><?= esc($enrollment['created_at']) ?></dd>
        </dl>
    </div>
</div>

<a href="<?= site_url('/') ?>" class="btn btn-primary mt-3">Nueva matrícula</a>

<?= $this->endSection() ?>