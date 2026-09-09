<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0"><?= esc($category['name']) ?> - Grade Entry</h1>
        <small class="text-muted">
            Section: <strong><?= esc($section['section_name']) ?></strong> | 
            School Year: <strong><?= esc($schoolYear) ?></strong>
        </small>
    </div>
    <a href="<?= base_url("teacher/sned/categories/{$section['id']}") ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Categories
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Quarter Tabs -->
<ul class="nav nav-tabs mb-3" id="quarterTabs" role="tablist">
    <?php foreach ($quarters as $q): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $q === 1 ? 'active' : '' ?>" id="q<?= $q ?>-tab" data-bs-toggle="tab" data-bs-target="#q<?= $q ?>" type="button" role="tab">
            Quarter <?= $q ?>
        </button>
    </li>
    <?php endforeach; ?>
</ul>

<!-- Grade Symbols Legend -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="fw-bold small me-2">Rating Guide:</span>
            <?php foreach ($gradeSymbols as $gs): ?>
                <span class="badge rounded-pill me-1 bg-secondary"><?= esc($gs['symbol']) ?></span>
                <small class="text-muted me-3"><?= esc($gs['label']) ?></small>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Quarter Content -->
<div class="tab-content">
    <?php foreach ($quarters as $q): ?>
    <div class="tab-pane fade <?= $q === 1 ? 'show active' : '' ?>" id="q<?= $q ?>" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="min-width: 200px;">Student</th>
                        <th style="min-width: 150px;">LRN</th>
                        <?php foreach ($category['fields'] as $field): ?>
                            <th style="min-width: 120px;"><?= esc($field['field_name']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="<?= 3 + count($category['fields']) ?>" class="text-center py-4">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">No students enrolled in this section.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><strong><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></strong></td>
                            <td><?= esc($student['lrn'] ?? 'N/A') ?></td>
                            <?php foreach ($category['fields'] as $field): 
                                $currentGrade = $gradesByQuarter[$q][$student['id']][$field['id']] ?? null;
                                $currentSymbol = $currentGrade['grade_symbol'] ?? '';
                                $currentRemarks = $currentGrade['remarks'] ?? '';
                            ?>
                                <td>
                                    <select class="form-select form-select-sm grade-select" 
                                            data-student="<?= $student['id'] ?>" 
                                            data-field="<?= $field['id'] ?>" 
                                            data-quarter="<?= $q ?>"
                                            onchange="saveGrade(this)">
                                        <option value="">--</option>
                                        <?php foreach ($gradeSymbols as $gs): ?>
                                            <option value="<?= esc($gs['symbol']) ?>" <?= $currentSymbol === $gs['symbol'] ? 'selected' : '' ?>>
                                                <?= esc($gs['symbol']) ?> - <?= esc($gs['label']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" class="form-control form-control-sm mt-1 remarks-input" 
                                           placeholder="Remarks"
                                           data-student="<?= $student['id'] ?>" 
                                           data-field="<?= $field['id'] ?>" 
                                           data-quarter="<?= $q ?>"
                                           value="<?= esc($currentRemarks) ?>"
                                           onchange="saveRemarks(this)">
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
function saveGrade(select) {
    const studentId = select.dataset.student;
    const fieldId = select.dataset.field;
    const quarter = select.dataset.quarter;
    const gradeSymbol = select.value;
    
    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('field_id', fieldId);
    formData.append('quarter', quarter);
    formData.append('grade_symbol', gradeSymbol);
    
    fetch('<?= base_url('teacher/sned/grades/save') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            select.classList.add('is-valid');
            setTimeout(() => select.classList.remove('is-valid'), 2000);
        } else {
            alert('Error: ' + (data.message || 'Failed to save grade'));
        }
    })
    .catch(error => console.error('Error:', error));
}

function saveRemarks(input) {
    const studentId = input.dataset.student;
    const fieldId = input.dataset.field;
    const quarter = input.dataset.quarter;
    const remarks = input.value;
    
    const formData = new FormData();
    formData.append('student_id', studentId);
    formData.append('field_id', fieldId);
    formData.append('quarter', quarter);
    formData.append('grade_symbol', '');
    formData.append('remarks', remarks);
    
    fetch('<?= base_url('teacher/sned/grades/save') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.classList.add('is-valid');
            setTimeout(() => input.classList.remove('is-valid'), 2000);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?= $this->endSection() ?>