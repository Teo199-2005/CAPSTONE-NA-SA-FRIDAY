<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Learning Materials</h1>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-upload me-2"></i>Upload Material
        </button>
        <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<?php if ($error = session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= esc($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($success = session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">My Uploaded Materials</h5>
            </div>
            <div class="card-body">
                <?php if (empty($materials)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-folder2-open display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">No materials uploaded yet</h5>
                        <p class="text-muted">Upload learning materials, documents, and resources for your students.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Student</th>
                                    <th>File Type</th>
                                    <th>Size</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materials as $material): ?>
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
                                            <?php if ($material->student_id): ?>
                                                <?php 
                                                $student = array_filter($students, fn($s) => $s['id'] == $material->student_id);
                                                $student = reset($student);
                                                ?>
                                                <?= $student ? esc($student['first_name'] . ' ' . $student['last_name']) : 'Unknown' ?>
                                            <?php else: ?>
                                                <span class="text-muted">General</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= strtoupper($material->file_type) ?></span>
                                        </td>
                                        <td><?= number_format($material->file_size / 1024, 1) ?> KB</td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('M j, Y', strtotime($material->created_at)) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('teacher/materials/download/' . $material->id) ?>" 
                                                   class="btn btn-outline-primary" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <button class="btn btn-outline-danger" 
                                                        onclick="confirmDelete(<?= $material->id ?>, '<?= esc($material->title) ?>')" 
                                                        title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
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

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Learning Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" enctype="multipart/form-data" action="<?= base_url('teacher/materials/upload') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">Select Category</option>
                            <option value="document">Learning Material</option>
                            <option value="transcript">Transcript</option>
                            <option value="certificate">Certificate</option>
                            <option value="form">Form</option>
                            <option value="report">Report</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student (Optional)</label>
                        <select name="student_id" class="form-select">
                            <option value="">General Material</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>"><?= esc($student['first_name'] . ' ' . $student['last_name']) ?> (<?= esc($student['lrn']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" name="material" class="form-control" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar" required>
                        <div class="form-text">Allowed: PDF, DOC, DOCX, JPG, PNG, ZIP, RAR (Max: 10MB)</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_public" class="form-check-input" id="isPublic">
                            <label class="form-check-label" for="isPublic">
                                Make this material publicly accessible
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Material</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, title) {
    if (confirm(`Are you sure you want to delete "${title}"? This action cannot be undone.`)) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= base_url('teacher/materials/delete/') ?>${id}`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?= $this->endSection() ?>