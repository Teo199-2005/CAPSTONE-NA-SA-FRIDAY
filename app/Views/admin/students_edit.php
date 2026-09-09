<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Edit Student</h1>
    <a href="<?= base_url('admin/students') ?>" class="btn btn-outline-secondary">Back to Students</a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= base_url('admin/students/update/' . $student['id']) ?>">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="lrn" class="form-label">LRN</label>
                        <input type="text" class="form-control" name="lrn" id="lrn" 
                               value="<?= esc($student['lrn']) ?>" required maxlength="12" pattern="[0-9]{12}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12)">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="student_type" class="form-label">Student Type</label>
                        <select class="form-select" name="student_type" id="student_type">
                            <option value="New Student" <?= in_array($student['student_type'] ?? '', ['New Student', 'new'], true) ? 'selected' : '' ?>>New Student</option>
                            <option value="Transferee" <?= in_array($student['student_type'] ?? '', ['Transferee', 'transferee'], true) ? 'selected' : '' ?>>Transferee</option>
                            <option value="Old Student" <?= in_array($student['student_type'] ?? '', ['Old Student', 'old student', 'old'], true) ? 'selected' : '' ?>>Old Student</option>
                            <option value="Returnee" <?= in_array($student['student_type'] ?? '', ['Returnee', 'returnee'], true) ? 'selected' : '' ?>>Returnee</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" 
                               value="<?= esc($student['email']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="first_name" 
                               value="<?= esc($student['first_name']) ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" id="middle_name" 
                               value="<?= esc($student['middle_name'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="last_name" 
                               value="<?= esc($student['last_name']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="grade_level" class="form-label">Grade Level</label>
                        <select class="form-select" name="grade_level" id="grade_level" required>
                            <?php foreach (grade_level_options() as $g): ?>
                                <option value="<?= $g ?>" <?= $student['grade_level'] == $g ? 'selected' : '' ?>><?= esc(grade_level_label($g)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="section_id" class="form-label">Section</label>
                        <select class="form-select" name="section_id" id="section_id">
                            <option value="">No Section</option>
                            <?php 
                            $db = \Config\Database::connect();
                            foreach ($sections as $section): 
                                $enrollmentCount = $db->table('students')
                                    ->where('section_id', $section['id'])
                                    ->where('enrollment_status', 'enrolled')
                                    ->where('deleted_at IS NULL')
                                    ->countAllResults();
                                
                                $capacity = $section['capacity'] ?? 40;
                                $isFull = $enrollmentCount >= $capacity;
                                $isCurrentSection = $student['section_id'] == $section['id'];
                            ?>
                                <option value="<?= $section['id'] ?>" 
                                        data-grade="<?= $section['grade_level'] ?>"
                                        <?= $isCurrentSection ? 'selected' : '' ?>
                                        <?= $isFull && !$isCurrentSection ? 'disabled' : '' ?>>
                                    <?= esc($section['section_name']) ?>
                                    <?= $isFull ? ' - FULL' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" name="gender" id="gender" required>
                            <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" 
                               value="<?= esc($student['date_of_birth']) ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="place_of_birth" class="form-label">Place of Birth</label>
                        <input type="text" class="form-control" name="place_of_birth" id="place_of_birth" 
                               value="<?= esc($student['place_of_birth'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" 
                               value="<?= esc($student['contact_number'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="nationality" class="form-label">Nationality</label>
                        <input type="text" class="form-control" name="nationality" id="nationality" 
                               value="<?= esc($student['nationality'] ?? 'Filipino') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="religion" class="form-label">Religion</label>
                        <input type="text" class="form-control" name="religion" id="religion" 
                               value="<?= esc($student['religion'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="suffix" class="form-label">Suffix</label>
                        <select class="form-select" name="suffix" id="suffix">
                            <option value="">None</option>
                            <option value="Jr." <?= ($student['suffix'] ?? '') === 'Jr.' ? 'selected' : '' ?>>Jr.</option>
                            <option value="Sr." <?= ($student['suffix'] ?? '') === 'Sr.' ? 'selected' : '' ?>>Sr.</option>
                            <option value="II" <?= ($student['suffix'] ?? '') === 'II' ? 'selected' : '' ?>>II</option>
                            <option value="III" <?= ($student['suffix'] ?? '') === 'III' ? 'selected' : '' ?>>III</option>
                            <option value="IV" <?= ($student['suffix'] ?? '') === 'IV' ? 'selected' : '' ?>>IV</option>
                            <option value="V" <?= ($student['suffix'] ?? '') === 'V' ? 'selected' : '' ?>>V</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" name="address" id="address" rows="2"><?= esc($student['address'] ?? '') ?></textarea>
            </div>

            <?php helper('nutrition'); ?>
            <h5 class="mt-4 mb-3">Health / nutrition</h5>
            <p class="text-muted small">Optional: enter on behalf of the student. BMI and screening category update when all three are filled.</p>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="height_cm" class="form-label">Height (cm)</label>
                    <input type="number" step="0.1" min="80" max="250" class="form-control" name="height_cm" id="height_cm"
                           value="<?= esc(old('height_cm', $student['height_cm'] ?? '')) ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="weight_kg" class="form-label">Weight (kg)</label>
                    <input type="number" step="0.1" min="15" max="200" class="form-control" name="weight_kg" id="weight_kg"
                           value="<?= esc(old('weight_kg', $student['weight_kg'] ?? '')) ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="ethnicity" class="form-label">Ethnicity</label>
                    <select class="form-select" name="ethnicity" id="ethnicity">
                        <option value="">—</option>
                        <?php $ev = old('ethnicity', $student['ethnicity'] ?? '');
                        foreach (student_ethnicity_options() as $val => $lab): ?>
                            <option value="<?= esc($val) ?>" <?= (string) $ev === (string) $val ? 'selected' : '' ?>><?= esc($lab) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if (! empty($student['bmi']) || ! empty($student['nutrition_status'])): ?>
                <p class="small text-muted">Current: BMI <?= esc((string) ($student['bmi'] ?? '—')) ?> —
                    <?= esc(\App\Libraries\StudentNutritionClassifier::statusLabel($student['nutrition_status'] ?? \App\Libraries\StudentNutritionClassifier::STATUS_UNKNOWN)) ?></p>
            <?php endif; ?>

            <h5 class="mt-4 mb-3">Emergency Contact</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="emergency_contact_name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="emergency_contact_name" id="emergency_contact_name" 
                               value="<?= esc($student['emergency_contact_name'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="emergency_contact_number" class="form-label">Number</label>
                        <input type="text" class="form-control" name="emergency_contact_number" id="emergency_contact_number" 
                               value="<?= esc($student['emergency_contact_number'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                        <input type="text" class="form-control" name="emergency_contact_relationship" id="emergency_contact_relationship" 
                               value="<?= esc($student['emergency_contact_relationship'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="enrollment_status" class="form-label">Status</label>
                        <select class="form-select" name="enrollment_status" id="enrollment_status" required>
                            <option value="enrolled" selected>Enrolled</option>
                            <option value="graduated" <?= $student['enrollment_status'] == 'graduated' ? 'selected' : '' ?>>Graduated</option>
                            <option value="transferred" <?= $student['enrollment_status'] == 'transferred' ? 'selected' : '' ?>>Transferred</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="school_year" class="form-label">School Year</label>
                        <input type="text" class="form-control" name="school_year" id="school_year" 
                               value="<?= esc($student['school_year'] ?? get_current_school_year()) ?>">
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('admin/students') ?>" class="btn" style="background-color: #6c757d; color: white;">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Student</button>
            </div>
        </form>

<?php if (!empty($student['section_id'])): ?>
<div class="card mt-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-book me-2"></i>Enrolled Subjects</h5>
        <button type="button" class="btn btn-sm btn-warning" onclick="unassignSelectedSubjects(event)" id="unassignBtn" disabled>
            <i class="bi bi-x-circle"></i> Unassign Selected (<span id="selectedSubjectCount">0</span>)
        </button>
    </div>
    <div class="card-body">
        <?php 
        $db = \Config\Database::connect();
        $subjects = $db->query(
            "SELECT s.id, s.subject_name, s.subject_code
             FROM subjects s
             JOIN section_subjects ss ON ss.subject_id = s.id
             WHERE ss.section_id = ? AND s.grade_level = ?
             ORDER BY s.subject_name",
            [$student['section_id'], $student['grade_level']]
        )->getResultArray();
        $excludedSubjects = !empty($student['excluded_subjects']) ? explode(',', $student['excluded_subjects']) : [];
        ?>
        <?php if (!empty($subjects)): ?>
            <div class="row g-3">
                <?php foreach ($subjects as $subject): ?>
                    <?php if (!in_array($subject['id'], $excludedSubjects)): ?>
                    <div class="col-md-3">
                        <div class="subject-card-edit">
                            <input type="checkbox" class="subject-checkbox" value="<?= $subject['id'] ?>" onchange="updateSubjectCount()">
                            <div class="subject-icon-edit">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div class="subject-info-edit">
                                <div class="subject-name-edit"><?= esc($subject['subject_name']) ?></div>
                                <div class="subject-code-edit"><?= esc($subject['subject_code']) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> No subjects assigned to this section yet.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
(function() {
    'use strict';
    window.pendingSubjectIds = [];
    
    window.updateSubjectCount = function() {
        const selected = document.querySelectorAll('.subject-checkbox:checked');
        document.getElementById('selectedSubjectCount').textContent = selected.length;
        document.getElementById('unassignBtn').disabled = selected.length === 0;
    };
    
    window.unassignSelectedSubjects = function(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const selected = Array.from(document.querySelectorAll('.subject-checkbox:checked'));
        window.pendingSubjectIds = selected.map(cb => cb.value);
        
        if (window.pendingSubjectIds.length === 0) {
            alert('Please select at least one subject to unassign.');
            return false;
        }
        
        const existingModal = document.getElementById('unassignModal');
        if (existingModal) existingModal.remove();
        
        const modalHtml = `
            <div class="modal fade" id="unassignModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Unassign</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Unassign ${window.pendingSubjectIds.length} subject(s) from this student?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-warning" onclick="confirmUnassign()">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const portal = document.getElementById('dashboard-modal-portal') || document.body;
        portal.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('unassignModal'), { backdrop: 'static', keyboard: true, focus: true });
        modal.show();
        
        return false;
    };
    
    window.confirmUnassign = function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('unassignModal'));
        if (modal) modal.hide();
        
        fetch('<?= base_url('admin/students/unassign-subjects/' . $student['id']) ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ subject_ids: window.pendingSubjectIds })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                alert(d.message);
                location.reload();
            } else {
                alert('Error: ' + (d.error || 'Failed'));
            }
        })
        .catch(e => alert('Error: ' + e.message));
    };
})();
</script>

<style>
.subject-card-edit {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.2s ease;
    height: 100%;
}

.subject-card-edit:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.1);
}

.subject-icon-edit {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    font-size: 1.2rem;
}

.subject-info-edit {
    flex: 1;
    min-width: 0;
}

.subject-name-edit {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.subject-code-edit {
    font-size: 0.75rem;
    color: #64748b;
    margin-bottom: 0.25rem;
}

.subject-card-edit input[type="checkbox"] {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 18px;
    height: 18px;
    cursor: pointer;
    z-index: 1;
}

.subject-card-edit {
    position: relative;
}

.subject-unassigned {
    opacity: 0.5;
    background: #f1f5f9 !important;
}
</style>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const gradeSelect = document.getElementById('grade_level');
    const sectionSelect = document.getElementById('section_id');

    function filterSections() {
        const selectedGrade = gradeSelect.value;
        const options = sectionSelect.querySelectorAll('option');

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                return;
            }

            const optionGrade = option.getAttribute('data-grade');
            if (optionGrade === selectedGrade) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
                if (option.selected) {
                    sectionSelect.value = '';
                }
            }
        });
    }

    gradeSelect.addEventListener('change', filterSections);
    filterSections();
});
</script>
    </div>
</div>

<?= $this->endSection() ?>
