<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Developmental Domains</h1>
        <small class="text-muted">Section: <strong><?= esc($section['section_name']) ?></strong></small>
    </div>
    <a href="<?= base_url('teacher/sned') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach ($categories as $category): ?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" 
                         style="width: 56px; height: 56px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-diagram-3 text-white fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-1"><?= esc($category['name']) ?></h5>
                        <small class="text-muted"><?= $category['field_count'] ?> Field(s)</small>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a href="<?= base_url("teacher/sned/grades/{$section['id']}/{$category['id']}") ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil-square me-1"></i> Enter Grades
                    </a>
                    <a href="<?= base_url("teacher/sned/categories/{$category['id']}/fields") ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-gear me-1"></i> Manage Fields
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>