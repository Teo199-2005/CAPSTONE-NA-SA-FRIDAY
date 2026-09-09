<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Add New Teacher</h1>
  <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to Teachers
  </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php
$validationErrors = session()->getFlashdata('validation');
if ($validationErrors instanceof \CodeIgniter\Validation\Validation):
?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($validationErrors->getErrors() as $error): ?>
        <li><?= esc($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Teacher Personnel Record</h5>
    <small class="text-muted">Complete all required fields per school personnel data sheet</small>
  </div>
  <div class="card-body">
    <form method="post" action="<?= base_url('admin/teachers/store') ?>">
      <?= csrf_field() ?>
      <?= view('admin/partials/teacher_personnel_fields', ['mode' => 'create', 'showAccount' => true]) ?>
      <div class="d-flex justify-content-end gap-2">
        <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle"></i> Create Teacher
        </button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
