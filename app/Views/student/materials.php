<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Learning Materials</h1>
    <a href="<?= base_url('student/dashboard') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<?php if ($error = session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= esc($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Student-Specific Materials -->
    <?php if (!empty($studentMaterials)): ?>
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-check me-2"></i>My Personal Materials
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>File Type</th>
                                    <th>Size</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($studentMaterials as $material): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-<?= $material->file_type === 'pdf' ? 'pdf' : 'text' ?> me-2 dash-icon-inline"></i>
                                                <div>
                                                    <strong><?= esc($material->title) ?></strong>
                                                    <?php if ($material->description): ?>
                                                        <br><small class="text-muted"><?= esc($material->description) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $material->category === 'transcript' ? 'success' : ($material->category === 'certificate' ? 'warning' : 'info') ?>">
                                                <?= ucfirst($material->category) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= strtoupper($material->file_type) ?></span>
                                        </td>
                                        <td><?= number_format($material->file_size / 1024, 1) ?> KB</td>
                                        <td>
                                            <small class="text-muted"><?= esc($material->uploader_name ?? 'Admin') ?></small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('M j, Y', strtotime($material->created_at)) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('student/materials/download/' . $material->id) ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Public Materials -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-globe me-2"></i>Public Learning Materials
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($publicMaterials)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-folder2-open display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">No public materials available</h5>
                        <p class="text-muted">Check back later for learning materials and resources.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>File Type</th>
                                    <th>Size</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($publicMaterials as $material): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-<?= $material->file_type === 'pdf' ? 'pdf' : 'text' ?> me-2 dash-icon-inline"></i>
                                                <div>
                                                    <strong><?= esc($material->title) ?></strong>
                                                    <?php if ($material->description): ?>
                                                        <br><small class="text-muted"><?= esc($material->description) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $material->category === 'transcript' ? 'success' : ($material->category === 'certificate' ? 'warning' : 'info') ?>">
                                                <?= ucfirst($material->category) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= strtoupper($material->file_type) ?></span>
                                        </td>
                                        <td><?= number_format($material->file_size / 1024, 1) ?> KB</td>
                                        <td>
                                            <small class="text-muted"><?= esc($material->uploader_name ?? 'Admin') ?></small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('M j, Y', strtotime($material->created_at)) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('student/materials/download/' . $material->id) ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>