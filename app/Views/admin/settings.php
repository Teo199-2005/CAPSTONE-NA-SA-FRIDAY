<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <h1 class="h3 mb-2">System Settings</h1>
    <p class="text-muted">Configure system-wide settings</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Academic Year Configuration</h5>
    </div>
    <div class="card-body p-4">
        <form method="post" action="<?= base_url('admin/settings/update-school-year') ?>">
            <?= csrf_field() ?>
            
            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="setting-box h-100">
                        <label class="form-label fw-bold text-primary">
                            <i class="bi bi-calendar3 me-2"></i>School Year
                        </label>
                        <input type="text" name="school_year" class="form-control form-control-lg" 
                               id="schoolYearInput" value="<?= $currentSchoolYear ?>" 
                               placeholder="2025-2026" pattern="\d{4}-\d{4}" maxlength="9" required>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>Format: YYYY-YYYY
                        </small>
                    </div>
                </div>
                
                <div class="col-lg-3">
                    <div class="setting-box h-100">
                        <label class="form-label fw-bold text-primary">
                            <i class="bi bi-calendar-check me-2"></i>Current Term
                        </label>
                        <select name="term" class="form-select form-select-lg" required>
                            <option value="1" <?= $currentTerm == 1 ? 'selected' : '' ?>>Term 1</option>
                            <option value="2" <?= $currentTerm == 2 ? 'selected' : '' ?>>Term 2</option>
                            <option value="3" <?= $currentTerm == 3 ? 'selected' : '' ?>>Term 3</option>
                        </select>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>Kindergarten through Grade 6
                        </small>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="alert alert-light border h-100 mb-0">
                        <h6 class="mb-2"><i class="bi bi-exclamation-triangle text-warning me-2"></i>System Impact</h6>
                        <small class="d-block mb-2 text-muted">These settings will affect the following areas:</small>
                        <div class="d-flex flex-column gap-1">
                            <small><i class="bi bi-check-circle text-success me-1"></i>Student enrollments & registrations</small>
                            <small><i class="bi bi-check-circle text-success me-1"></i>Section assignments & class lists</small>
                            <small><i class="bi bi-check-circle text-success me-1"></i>Grade entry & report cards</small>
                            <small><i class="bi bi-check-circle text-success me-1"></i>Academic reports & transcripts</small>
                            <small><i class="bi bi-check-circle text-success me-1"></i>Teacher & admin dashboards</small>
                            <small><i class="bi bi-check-circle text-success me-1"></i>Analytics & statistics</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-4" id="saveBtn">
                    <i class="bi bi-check-circle me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Subject Management Section -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Subject Management</h5>
    </div>
    <div class="card-body p-4">
        <?php 
        $gradeOptions = grade_level_options();
        $nonSnedGrades = array_filter($gradeOptions, fn($g) => $g !== 7);
        ?>
        <?php foreach ($nonSnedGrades as $grade): ?>
        <div class="mb-4">
            <h6 class="fw-bold text-success mb-3"><i class="bi bi-book me-2"></i><?= esc(grade_level_label((int) $grade)) ?> Subjects</h6>
            <div id="grade<?= $grade ?>Subjects" class="mb-3"></div>
            <button class="btn btn-sm btn-outline-success" onclick="addSubjectToGrade(<?= $grade ?>)">
                <i class="bi bi-plus-circle me-1"></i>Add Subject
            </button>
        </div>
        <?php if ($grade < max($nonSnedGrades)): ?><hr><?php endif; ?>
        <?php endforeach; ?>

        <!-- SNED Categories & Fields Management -->
        <hr>
        <div class="mb-4">
            <h6 class="fw-bold text-success mb-3"><i class="bi bi-diagram-3 me-2"></i>SNED (Special Needs Education) - Developmental Domains</h6>
            <p class="text-muted small mb-3">SNED does not use subjects. Instead, it uses developmental domains (categories) with performance indicators (fields) for assessing students.</p>
            <div id="snedCategoriesContainer" class="mb-3"></div>
            <button class="btn btn-sm btn-outline-success" onclick="addSnedCategory()">
                <i class="bi bi-plus-circle me-1"></i>Add Developmental Domain
            </button>
        </div>
    </div>
</div>

<!-- Featured Posters Section -->
<form method="post" enctype="multipart/form-data" action="<?= base_url('admin/settings/update-featured-posters') ?>">
    <?= csrf_field() ?>
    <div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Featured Posters</h5>
    </div>
    <div class="card-body p-4">
        <p class="text-muted mb-4">Upload a poster image to be shown on teacher and student dashboards.</p>

        <table style="width:100%;border-collapse:separate;border-spacing:1rem;">
            <tr>
                <td style="width:50%;vertical-align:top;padding-right:0.5rem;">
                    <div class="border rounded-3 p-3 h-100">
                        <h6 class="fw-bold text-info mb-3"><i class="bi bi-person-video3 me-2"></i>Teacher Dashboard Poster</h6>
                        <?php if (! empty($featuredPosterTeacherUrl)): ?>
                            <div class="mb-3">
                                <img src="<?= esc($featuredPosterTeacherUrl ?? '') ?>" alt="Teacher Poster" class="img-fluid rounded-3 border" style="max-height: 220px; width: 100%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light border mb-3">No poster uploaded yet.</div>
                        <?php endif; ?>
                        <input type="file" name="featured_poster_teacher" class="form-control poster-file-input" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" data-preview-target="teacher">
                        <small class="text-muted d-block mt-2">JPG/PNG/WEBP, max 5MB.</small>
                    </div>
                </td>
                <td style="width:50%;vertical-align:top;padding-left:0.5rem;">
                    <div class="border rounded-3 p-3 h-100">
                        <h6 class="fw-bold text-info mb-3"><i class="bi bi-mortarboard-fill me-2"></i>Student Dashboard Poster</h6>
                        <?php if (! empty($featuredPosterStudentUrl)): ?>
                            <div class="mb-3">
                                <img src="<?= esc($featuredPosterStudentUrl ?? '') ?>" alt="Student Poster" class="img-fluid rounded-3 border" style="max-height: 220px; width: 100%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light border mb-3">No poster uploaded yet.</div>
                        <?php endif; ?>
                        <input type="file" name="featured_poster_student" class="form-control poster-file-input" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" data-preview-target="student">
                        <small class="text-muted d-block mt-2">JPG/PNG/WEBP, max 5MB.</small>
                    </div>
                </td>
            </tr>
        </table>

            <div class="mt-4">
                <button type="submit" class="btn btn-info text-white">
                    <i class="bi bi-image me-2"></i>Save Featured Posters
                </button>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.poster-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const target = input.getAttribute('data-preview-target');
            const file = input.files && input.files[0];
            if (!target || !file) {
                return;
            }
            const col = input.closest('.border.rounded-3');
            if (!col) {
                return;
            }
            let wrap = col.querySelector('.poster-preview-live');
            if (!wrap) {
                const empty = col.querySelector('.alert');
                if (empty) {
                    empty.remove();
                }
                const oldImg = col.querySelector('img');
                if (oldImg) {
                    oldImg.remove();
                }
                wrap = document.createElement('div');
                wrap.className = 'mb-3 poster-preview-live';
                col.insertBefore(wrap, input);
            }
            wrap.innerHTML = '';
            const img = document.createElement('img');
            img.alt = 'Preview';
            img.className = 'img-fluid rounded-3 border poster-preview-img';
            img.style.cssText = 'max-height:220px;width:100%;object-fit:cover';
            img.src = URL.createObjectURL(file);
            wrap.appendChild(img);
        });
    });

    const schoolYearInput = document.getElementById('schoolYearInput');

    schoolYearInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9-]/g, '');
        if (value.length > 4 && value.charAt(4) !== '-') {
            value = value.slice(0, 4) + '-' + value.slice(4);
        }
        value = value.replace(/--+/g, '-');
        const parts = value.split('-');
        if (parts.length > 2) {
            value = parts[0] + '-' + parts.slice(1).join('');
        }
        e.target.value = value.slice(0, 9);
    });

    schoolYearInput.addEventListener('blur', function(e) {
        const value = e.target.value;
        if (value && !/^\d{4}-\d{4}$/.test(value)) {
            alert('School year must be in format YYYY-YYYY (e.g., 2025-2026)');
            e.target.focus();
        }
    });

    const allGrades = <?= json_encode(grade_level_options()) ?>;
    // Skip SNED (grade 7) - it uses domains/fields, not subjects
    allGrades.filter(g => g !== 7).forEach(function(grade) {
        loadGradeSubjects(grade);
    });
});

function loadGradeSubjects(grade) {
    console.log('Loading subjects for grade:', grade);
    fetch(`<?= base_url('admin/settings/get-grade-subjects/') ?>${grade}`)
        .then(r => r.json())
        .then(data => {
            console.log(`${formatGradeLevel(grade)} subjects response:`, data);
            const container = document.getElementById(`grade${grade}Subjects`);
            if (data.success && data.subjects && data.subjects.length > 0) {
                container.innerHTML = data.subjects.map(s => 
                    `<span class="badge bg-primary text-white me-2 mb-2 p-2" style="font-size: 1.25rem;"><strong>${s.subject_code}</strong> - ${s.subject_name} <button class="btn-close btn-close-white btn-sm ms-2" style="font-size: 0.7rem;" onclick="deleteSubjectFromSettings(${s.id}, ${grade})"></button></span>`
                ).join('');
            } else {
                container.innerHTML = '<p class="text-muted mb-0">No subjects added yet</p>';
            }
        })
        .catch(error => {
            console.error(`Error loading grade ${grade} subjects:`, error);
            document.getElementById(`grade${grade}Subjects`).innerHTML = '<p class="text-danger mb-0">Error loading subjects</p>';
        });
}

function addSubjectToGrade(grade) {
    const existing = document.getElementById('addSubjectSettingsModal');
    if (existing) existing.remove();
    
    const modalHtml = `
    <div class="modal fade" id="addSubjectSettingsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title text-dark fw-bold"><i class="bi bi-plus-circle-fill me-2"></i>Add Subject - ${formatGradeLevel(grade)}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Subject Code</label>
                        <input type="text" id="subjectCodeInput" class="form-control form-control-lg border-2" placeholder="e.g., MATH${grade}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Subject Name</label>
                        <input type="text" id="subjectNameInput" class="form-control form-control-lg border-2" placeholder="e.g., Mathematics ${grade}" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057;"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                    <button type="button" class="btn btn-success" onclick="saveSubjectFromSettings(${grade})"><i class="bi bi-check-circle me-2"></i>Add Subject</button>
                </div>
            </div>
        </div>
    </div>`;
    
    const portal = document.getElementById('dashboard-modal-portal') || document.body;
    portal.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('addSubjectSettingsModal'), { backdrop: true, keyboard: true, focus: true });
    modal.show();
    
    setTimeout(() => document.getElementById('subjectCodeInput').focus(), 300);
}

function saveSubjectFromSettings(grade) {
    const code = document.getElementById('subjectCodeInput').value.trim();
    const name = document.getElementById('subjectNameInput').value.trim();
    
    if (!code || !name) {
        alert('Please fill in both fields');
        return;
    }
    
    const formData = new FormData();
    formData.append('subject_code', code);
    formData.append('subject_name', name);
    formData.append('grade_level', grade);
    formData.append('is_active', true);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch('<?= base_url('admin/subjects/add') ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addSubjectSettingsModal')).hide();
                loadGradeSubjects(grade);
                showSuccessToast('Subject added successfully!');
            } else {
                alert(data.error || 'Failed to add subject');
            }
        });
}

function showToast(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'danger' ? 'alert-danger' : type === 'warning' ? 'alert-warning' : 'alert-info';
    const iconClass = type === 'success' ? 'bi-check-circle' : type === 'danger' ? 'bi-exclamation-triangle' : type === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="bi ${iconClass} me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function showSuccessToast(message) {
    showToast(message, 'success');
}


function loadSnedCategories() {
    fetch('<?= base_url('admin/sned/categories') ?>')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('snedCategoriesContainer');
            if (data.success && data.categories && data.categories.length > 0) {
                container.innerHTML = data.categories.map(c => 
                    `<div class="card mb-3 border">
                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <strong><i class="bi bi-diagram-3 me-2"></i>${c.name}</strong>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="manageSnedFields(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                                    <i class="bi bi-gear me-1"></i>Manage Fields (${c.field_count})
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSnedCategory(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body py-2">
                            <small class="text-muted">${c.description || 'No description'}</small>
                        </div>
                    </div>`
                ).join('');
            } else {
                container.innerHTML = '<p class="text-muted mb-0">No developmental domains added yet. SNED uses developmental domains (categories) with performance indicators (fields) instead of traditional subjects.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading SNED categories:', error);
            document.getElementById('snedCategoriesContainer').innerHTML = '<p class="text-danger mb-0">Error loading categories</p>';
        });
}

function addSnedCategory() {
    const existing = document.getElementById('addSnedCategoryModal');
    if (existing) existing.remove();
    
    const modalHtml = `
    <div class="modal fade" id="addSnedCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-diagram-3 me-2"></i>Add Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" style="color: #333;">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #333;">Category Name</label>
                        <input type="text" id="snedCategoryName" class="form-control form-control-lg border-2" placeholder="e.g., Gross Motor Skills" style="color: #333;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #333;">Description</label>
                        <textarea id="snedCategoryDesc" class="form-control border-2" rows="3" placeholder="Brief description of this category" style="color: #333;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057;"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                    <button type="button" class="btn btn-success" onclick="saveSnedCategory()"><i class="bi bi-check-circle me-2"></i>Add Category</button>
                </div>
            </div>
        </div>
    </div>`;
    
    const portal = document.getElementById('dashboard-modal-portal') || document.body;
    portal.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('addSnedCategoryModal'), { backdrop: true, keyboard: true, focus: true });
    modal.show();
    setTimeout(() => document.getElementById('snedCategoryName').focus(), 300);
}

function saveSnedCategory() {
    const name = document.getElementById('snedCategoryName').value.trim();
    const desc = document.getElementById('snedCategoryDesc').value.trim();
    
    if (!name) {
        alert('Please enter a domain name');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', desc);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch('<?= base_url('admin/sned/categories/add') ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addSnedCategoryModal')).hide();
                loadSnedCategories();
                showSuccessToast('Category added successfully!');
            } else {
                alert(data.error || 'Failed to add category');
            }
        });
}

function manageSnedFields(categoryId, categoryName) {
    const existing = document.getElementById('manageSnedFieldsModal');
    if (existing) existing.remove();
    
    // Fetch fields
    fetch('<?= base_url('admin/sned/fields/') ?>' + categoryId)
        .then(r => r.json())
        .then(data => {
            const fields = data.success && data.fields ? data.fields : [];
            const fieldsHtml = fields.length > 0 ? 
                fields.map((f, i) => 
                    `<tr>
                        <td>${i+1}</td>
                        <td>${f.field_name}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteSnedField(${f.id}, ${categoryId})"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`
                ).join('') :
                '<tr><td colspan="3" class="text-center text-muted py-3">No fields yet</td></tr>';
            
            const modalHtml = `
            <div class="modal fade" id="manageSnedFieldsModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-gear me-2"></i>${categoryName} - Fields</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4" style="color: #333;">
                            <div class="row">
                                <div class="col-lg-7">
                                    <h6 class="fw-bold mb-3" style="color: #333;"><i class="bi bi-list-check me-2"></i>Existing Fields</h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="table-light">
                                                <tr><th>#</th><th>Field Name</th><th style="width:80px;">Action</th></tr>
                                            </thead>
                                            <tbody id="snedFieldsTableBody">${fieldsHtml}</tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <h6 class="fw-bold mb-3" style="color: #333;"><i class="bi bi-plus-circle me-2"></i>Add Field</h6>
                                    <div class="mb-3">
                                        <label class="form-label" style="color: #333;">Field Name</label>
                                        <input type="text" id="newFieldName" class="form-control" placeholder="e.g., Can walk independently" style="color: #333;">
                                    </div>
                                    <button class="btn btn-primary w-100" onclick="addSnedField(${categoryId})" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                        <i class="bi bi-plus-circle me-1"></i> Add Field
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            
            const portal = document.getElementById('dashboard-modal-portal') || document.body;
            portal.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('manageSnedFieldsModal'), { backdrop: true, keyboard: true, focus: true });
            modal.show();
        });
}

function addSnedField(categoryId) {
    const name = document.getElementById('newFieldName').value.trim();
    if (!name) {
        alert('Please enter an indicator name');
        return;
    }
    
    const formData = new FormData();
    formData.append('category_id', categoryId);
    formData.append('field_name', name);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch('<?= base_url('admin/sned/fields/add') ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('newFieldName').value = '';
                showSuccessToast('Indicator added!');
                manageSnedFields(categoryId, document.querySelector('#manageSnedFieldsModal .modal-title').textContent.replace(/.*?\\|\\| /, ''));
            } else {
                alert(data.error || 'Failed to add indicator');
            }
        });
}

function deleteSnedField(fieldId, categoryId) {
    if (!confirm('Deactivate this indicator? Existing grades will be preserved.')) return;
    
    fetch('<?= base_url('admin/sned/fields/delete/') ?>' + fieldId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Indicator deactivated');
            manageSnedFields(categoryId, document.querySelector('#manageSnedFieldsModal .modal-title').textContent.replace(/.*?\\|\\| /, ''));
        } else {
            showToast(data.error || 'Failed to delete', 'danger');
        }
    });
}

function deleteSnedCategory(categoryId, categoryName) {
    if (!confirm('Delete the domain "' + categoryName + '"? All associated fields and grades will be deactivated.')) return;
    
    fetch('<?= base_url('admin/sned/categories/delete/') ?>' + categoryId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Category deleted');
            loadSnedCategories();
        } else {
            showToast(data.error || 'Failed to delete', 'danger');
        }
    });
}

// Override loadGradeSubjects for SNED to load categories instead
const originalDomContentLoaded = document.addEventListener('DOMContentLoaded', function() {
    // Load SNED categories
    loadSnedCategories();
}, { once: true });

function deleteSubjectFromSettings(id, grade) {
    fetch(`<?= base_url('admin/subjects/delete/') ?>${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', '<?= csrf_token() ?>': '<?= csrf_hash() ?>' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Subject deleted successfully!', 'success');
            loadGradeSubjects(grade);
        } else {
            showToast(data.error || 'Failed to delete', 'danger');
        }
    });
}
</script>

<style>
.setting-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    height: 100%;
}
</style>

<?= $this->endSection() ?>
