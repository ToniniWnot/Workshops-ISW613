<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php $editing = $course !== null; ?>

<h1 class="mb-3"><?= $editing ? 'Editar curso' : 'Nuevo curso' ?></h1>

<form action="<?= $editing ? site_url("courses/{$course['id']}/update") : site_url('courses') ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label class="form-label">Nombre del curso</label>
        <input type="text" name="name" class="form-control"
               value="<?= esc(old('name', $course['name'] ?? '')) ?>">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Código del curso</label>
            <input type="text" name="code" class="form-control" placeholder="Ej: ISW-101"
                   value="<?= esc(old('code', $course['code'] ?? '')) ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Cantidad de créditos</label>
            <input type="number" name="credits" min="1" max="10" class="form-control"
                   value="<?= esc(old('credits', $course['credits'] ?? '')) ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Carrera</label>
        <select name="career_id" class="form-select">
            <option value="">-- Seleccione una carrera --</option>
            <?php $selected = (string) old('career_id', $course['career_id'] ?? ''); ?>
            <?php foreach ($careers as $career): ?>
                <option value="<?= $career['id'] ?>" <?= $selected === (string) $career['id'] ? 'selected' : '' ?>>
                    <?= esc($career['name']) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <button class="btn btn-success"><?= $editing ? 'Actualizar' : 'Guardar' ?></button>
    <a href="<?= site_url('courses') ?>" class="btn btn-secondary">Cancelar</a>
</form>

<?= $this->endSection() ?>