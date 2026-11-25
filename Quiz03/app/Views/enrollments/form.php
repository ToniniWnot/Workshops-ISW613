<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h1 class="mb-3">Matrícula de estudiantes</h1>

<form action="<?= site_url('enrollments') ?>" method="post">
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-md-8 mb-3">
            <label class="form-label">Nombre del estudiante</label>
            <input type="text" name="student_name" class="form-control" value="<?= esc(old('student_name')) ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Carné</label>
            <input type="text" name="student_card" class="form-control" value="<?= esc(old('student_card')) ?>">
        </div>
    </div>

    <h5 class="mt-2">Seleccione un curso</h5>
    <table class="table table-hover align-middle">
        <thead>
            <tr><th></th><th>Código</th><th>Curso</th><th>Créditos</th><th>Carrera</th></tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $c): ?>
            <tr>
                <td>
                    <input class="form-check-input" type="radio" name="course_id" id="course<?= $c['id'] ?>"
                           value="<?= $c['id'] ?>" <?= old('course_id') == $c['id'] ? 'checked' : '' ?>>
                </td>
                <td><label for="course<?= $c['id'] ?>"><?= esc($c['code']) ?></label></td>
                <td><label for="course<?= $c['id'] ?>"><?= esc($c['name']) ?></label></td>
                <td><?= esc($c['credits']) ?></td>
                <td><?= esc($c['career_name']) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <button class="btn btn-primary btn-lg">Matricular</button>
</form>

<?= $this->endSection() ?>