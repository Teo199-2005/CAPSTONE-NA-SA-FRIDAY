<?php
$tabId = $isAdvisory ? 'advisory' : 'section-' . ($sectionId ?? 'unknown');
?>

<?php if (!empty($students) && !empty($subjects)): ?>
    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= count($students) ?></h5>
                    <p class="card-text text-muted mb-0">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= count($subjects) ?></h5>
                    <p class="card-text text-muted mb-0">Available Subjects</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">T<?= (int) $currentTerm ?></h5>
                    <p class="card-text text-muted mb-0">Current Term</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Grades Table -->
    <div class="card mt-4">
        <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
            <h5 class="card-title mb-0">Student Grades - Term <?= (int) $currentTerm ?></h5>
            <button type="button" class="btn btn-success" id="toggleInputMode-<?= $tabId ?>" onclick="toggleInputMode('<?= $tabId ?>')">
                <i class="bi bi-pencil-square"></i> Enable Input Mode
            </button>
        </div>
        <div class="card-body">
            <form id="bulkGradesForm-<?= $tabId ?>" method="post" action="<?= base_url('teacher/grades/bulk') ?>" onsubmit="console.log('Form submitting...')">
                <?= csrf_field() ?>
                <input type="hidden" name="term" value="<?= (int) $currentTerm ?>">
                <input type="hidden" name="grading_type" value="<?= esc($sectionGradingType ?? 'numerical') ?>">
                
                <?php 
                $debugGradingType = $sectionGradingType ?? 'numerical';
                $debugSymbolsCount = count($gradingSymbols ?? []);
                log_message('info', "DEBUG - Grading Type: {$debugGradingType}, Symbols Count: {$debugSymbolsCount}, Symbols: " . json_encode($gradingSymbols));
                if ($debugGradingType === 'non_numerical' && !empty($gradingSymbols)): ?>
                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Grading Guide (<?= $debugSymbolsCount ?> symbols loaded)</h6>
                        <div class="row">
                            <?php foreach ($gradingSymbols as $symbol): ?>
                                <div class="col-md-3 col-sm-6 mb-2">
                                    <span class="badge bg-<?= getSymbolBadgeClass($symbol['symbol']) ?> me-1"><?= esc($symbol['symbol']) ?></span>
                                    <strong><?= esc($symbol['label']) ?></strong>
                                    <small class="d-block text-muted"><?= esc($symbol['description']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div id="saveButtonContainer-<?= $tabId ?>" class="mb-3" style="display: none;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Save All Grades
                    </button>
                    <button type="button" class="btn ms-2" style="background-color: #495057; color: white;" onclick="cancelInputMode('<?= $tabId ?>')">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <?php foreach ($subjects as $subject): ?>
                                    <th><?= esc($isAdvisory ? $subject['subject_code'] : ($subject['code'] ?? $subject['subject_code'])) ?></th>
                                <?php endforeach; ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $maleStudents = array_filter($students, fn($s) => strtolower($s['gender'] ?? '') === 'male');
                            $femaleStudents = array_filter($students, fn($s) => strtolower($s['gender'] ?? '') === 'female');
                            ?>
                            <?php if (!empty($maleStudents)): ?>
                                <tr class="table-active">
                                    <td colspan="<?= count($subjects) + 2 ?>"><strong>Male Students</strong></td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($maleStudents as $student): ?>
                                <?php $excludedSubjects = !empty($student['excluded_subjects']) ? explode(',', $student['excluded_subjects']) : []; ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($student['lrn']) ?></small><br>
                                        <span class="badge bg-secondary"><?= esc(grade_level_label((int) ($student['grade_level'] ?? 0)) . ' - ' . $student['section_name']) ?></span>
                                    </td>
                                    <?php 
                                    $totalGrades = 0;
                                    $gradeCount = 0;
                                    foreach ($subjects as $subject): 
                                        $subjectId = $subject['id'];
                                        $isExcluded = in_array($subjectId, $excludedSubjects);
                                        if (!$isExcluded):
                                            $grade = $studentGrades[$student['id']][$subjectId] ?? null;
                                            $gradeValue = ($grade && isset($grade['grade'])) ? ($grade['grade'] ?? '') : '';
                                            if ($grade && isset($grade['grade']) && $grade['grade'] !== null): 
                                                $gradeDisplay = $grade['grade'];
                                                // Only add numeric grades to total
                                                if (is_numeric($gradeDisplay)) {
                                                    $totalGrades += $gradeDisplay;
                                                    $gradeCount++;
                                                }
                                            endif;
                                    ?>
                                            <td>
                                                <span class="grade-display-<?= $tabId ?>">
                                                    <?php if ($grade && isset($grade['grade']) && $grade['grade'] !== null): ?>
                                                        <?php if (($sectionGradingType ?? 'numerical') === 'non_numerical'): ?>
                                                            <span class="badge bg-<?= getSymbolBadgeClass($grade['grade']) ?>"><?= esc($grade['grade']) ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success"><?= number_format($grade['grade'], 1) ?></span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not graded</span>
                                                    <?php endif; ?>
                                                </span>
                                                <?php if (($sectionGradingType ?? 'numerical') === 'non_numerical'): ?>
                                                    <?php 
                                                    // Always render options - use database symbols if available, otherwise hardcoded
                                                    $symbolsToUse = !empty($gradingSymbols) ? $gradingSymbols : [
                                                        ['symbol' => 'P', 'label' => 'Proficient'],
                                                        ['symbol' => 'AP', 'label' => 'Approaching Proficiency'],
                                                        ['symbol' => 'D', 'label' => 'Developing'],
                                                        ['symbol' => 'B', 'label' => 'Beginning'],
                                                        ['symbol' => 'NO/NA', 'label' => 'Not Observed / Not Applicable']
                                                    ];
                                                    $optionsCount = count($symbolsToUse);
                                                    log_message('info', "Rendering select with {$optionsCount} options for student {$student['id']}, subject {$subjectId}");
                                                    ?>
                                                    <!-- DEBUG: Section=<?= $sectionGradingType ?? 'unknown' ?>, Symbols=<?= $optionsCount ?>, HasArray=<?= !empty($gradingSymbols) ? 'yes' : 'no' ?> -->
                                                    <select class="form-control form-select-sm grade-input-<?= $tabId ?>" 
                                                            name="grades[<?= $student['id'] ?>][<?= $subjectId ?>]" 
                                                            style="display: none; min-width: 200px; max-width: 250px; position: relative; z-index: 10;" 
                                                            onchange="updateTotal('<?= $tabId ?>', <?= $student['id'] ?>)">
                                                        <option value="">Select...</option>
                                                        <?php if ($optionsCount > 0): ?>
                                                            <?php foreach ($symbolsToUse as $symbol): ?>
                                                                <option value="<?= esc($symbol['symbol']) ?>" <?= ($gradeValue === $symbol['symbol']) ? 'selected' : '' ?>>
                                                                    <?= esc($symbol['symbol']) ?> - <?= esc($symbol['label']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option value="P">P - Proficient</option>
                                                            <option value="AP">AP - Approaching Proficiency</option>
                                                            <option value="D">D - Developing</option>
                                                            <option value="B">B - Beginning</option>
                                                            <option value="NO/NA">NO/NA - Not Observed / Not Applicable</option>
                                                        <?php endif; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input type="number" class="form-control form-control-sm grade-input-<?= $tabId ?>" 
                                                           name="grades[<?= $student['id'] ?>][<?= $subjectId ?>]" 
                                                           value="<?= $gradeValue ?>" 
                                                           min="0" max="100" step="0.1" 
                                                           style="display: none; min-width: 100px; width: 100%; position: relative; z-index: 10;" 
                                                           onchange="updateTotal('<?= $tabId ?>', <?= $student['id'] ?>)" 
                                                           oninput="validateGrade(this)">
                                                <?php endif; ?>
                                            </td>
                                    <?php else: ?>
                                        <td><span class="text-muted">-</span></td>
                                    <?php endif; endforeach; ?>
                                    <td>
                                        <span class="total-display-<?= $tabId ?>" data-student="<?= $student['id'] ?>">
                                            <?php if ($gradeCount > 0): ?>
                                                <strong><?= number_format($totalGrades / $gradeCount, 1) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!empty($femaleStudents)): ?>
                                <tr class="table-active">
                                    <td colspan="<?= count($subjects) + 2 ?>"><strong>Female Students</strong></td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($femaleStudents as $student): ?>
                                <?php $excludedSubjects = !empty($student['excluded_subjects']) ? explode(',', $student['excluded_subjects']) : []; ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($student['lrn']) ?></small><br>
                                        <span class="badge bg-secondary"><?= esc(grade_level_label((int) ($student['grade_level'] ?? 0)) . ' - ' . $student['section_name']) ?></span>
                                    </td>
                                    <?php 
                                    $totalGrades = 0;
                                    $gradeCount = 0;
                                    foreach ($subjects as $subject): 
                                        $subjectId = $subject['id'];
                                        $isExcluded = in_array($subjectId, $excludedSubjects);
                                        if (!$isExcluded):
                                            $grade = $studentGrades[$student['id']][$subjectId] ?? null;
                                            $gradeValue = ($grade && isset($grade['grade'])) ? $grade['grade'] : '';
                                            if ($grade && isset($grade['grade']) && $grade['grade'] !== null): 
                                                $gradeDisplay = $grade['grade'];
                                                // Only add numeric grades to total
                                                if (is_numeric($gradeDisplay)) {
                                                    $totalGrades += $gradeDisplay;
                                                    $gradeCount++;
                                                }
                                            endif;
                                    ?>
                                        <td>
                                            <span class="grade-display-<?= $tabId ?>">
                                                <?php if ($grade && isset($grade['grade']) && $grade['grade'] !== null): ?>
                                                    <?php if (($sectionGradingType ?? 'numerical') === 'non_numerical'): ?>
                                                        <span class="badge bg-<?= getSymbolBadgeClass($grade['grade']) ?>"><?= esc($grade['grade']) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success"><?= number_format($grade['grade'], 1) ?></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Not graded</span>
                                                <?php endif; ?>
                                            </span>
                                                <?php if (($sectionGradingType ?? 'numerical') === 'non_numerical'): ?>
                                                    <?php 
                                                    $symbolsToUse = !empty($gradingSymbols) ? $gradingSymbols : [
                                                        ['symbol' => 'P', 'label' => 'Proficient'],
                                                        ['symbol' => 'AP', 'label' => 'Approaching Proficiency'],
                                                        ['symbol' => 'D', 'label' => 'Developing'],
                                                        ['symbol' => 'B', 'label' => 'Beginning'],
                                                        ['symbol' => 'NO/NA', 'label' => 'Not Observed / Not Applicable']
                                                    ];
                                                    ?>
                                                    <select class="form-control form-select-sm grade-input-<?= $tabId ?>" 
                                                            name="grades[<?= $student['id'] ?>][<?= $subjectId ?>]" 
                                                            style="display: none; min-width: 200px; max-width: 250px; position: relative; z-index: 10;" 
                                                            onchange="updateTotal('<?= $tabId ?>', <?= $student['id'] ?>)">
                                                        <option value="">Select...</option>
                                                        <?php foreach ($symbolsToUse as $symbol): ?>
                                                            <option value="<?= esc($symbol['symbol']) ?>" <?= ($gradeValue === $symbol['symbol']) ? 'selected' : '' ?>>
                                                                <?= esc($symbol['symbol']) ?> - <?= esc($symbol['label']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                            <?php else: ?>
                                                <input type="number" class="form-control form-control-sm grade-input-<?= $tabId ?>" 
                                                       name="grades[<?= $student['id'] ?>][<?= $subjectId ?>]" 
                                                       value="<?= $gradeValue ?>" 
                                                       min="0" max="100" step="0.1" 
                                                       style="display: none; width: 80px; position: relative; z-index: 10;" 
                                                       onchange="updateTotal('<?= $tabId ?>', <?= $student['id'] ?>)" 
                                                       oninput="validateGrade(this)">
                                            <?php endif; ?>
                                        </td>
                                    <?php else: ?>
                                        <td><span class="text-muted">-</span></td>
                                    <?php endif; endforeach; ?>
                                    <td>
                                        <span class="total-display-<?= $tabId ?>" data-student="<?= $student['id'] ?>">
                                            <?php if ($gradeCount > 0): ?>
                                                <strong><?= number_format($totalGrades / $gradeCount, 1) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
    
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-clipboard-data fs-1 text-muted mb-3"></i>
            <h5 class="text-muted">No Students or Subjects Available</h5>
            <p class="text-muted mb-0">No students enrolled in this section.</p>
        </div>
    </div>
<?php endif; ?>

<script>
let inputModeEnabled = {};

function toggleInputMode(tabId) {
    const toggleBtn = document.getElementById(`toggleInputMode-${tabId}`);
    const saveContainer = document.getElementById(`saveButtonContainer-${tabId}`);
    
    // Use very specific selectors - include both input and select elements
    const gradeInputs = document.querySelectorAll(`input[class*="grade-input-${tabId}"], select[class*="grade-input-${tabId}"]`);
    const gradeDisplays = document.querySelectorAll(`[class*="grade-display-${tabId}"]`);
    
    inputModeEnabled[tabId] = !inputModeEnabled[tabId];
    
    if (inputModeEnabled[tabId]) {
        toggleBtn.innerHTML = '<i class="bi bi-eye me-1"></i>View Mode';
        toggleBtn.className = 'btn btn-outline-secondary btn-sm';
        saveContainer.style.display = 'block';
        
        gradeDisplays.forEach(display => {
            display.style.display = 'none';
        });
        gradeInputs.forEach(input => {
            input.style.display = 'inline-block';
            // Use responsive widths based on element type
            if (input.tagName === 'SELECT') {
                input.style.minWidth = '200px';
                input.style.width = '100%';
            } else {
                input.style.minWidth = '100px';
                input.style.width = '100%';
            }
        });
    } else {
        toggleBtn.innerHTML = '<i class="bi bi-pencil-square me-1"></i>Enable Input Mode';
        toggleBtn.className = 'btn btn-success btn-sm';
        saveContainer.style.display = 'none';
        
        gradeDisplays.forEach(display => {
            display.style.display = '';
        });
        gradeInputs.forEach(input => {
            input.style.display = 'none';
        });
    }
}

function cancelInputMode(tabId) {
    const gradeInputs = document.querySelectorAll(`input[class*="grade-input-${tabId}"], select[class*="grade-input-${tabId}"]`);
    gradeInputs.forEach(input => {
        const originalValue = input.getAttribute('data-original') || '';
        input.value = originalValue;
    });
    
    toggleInputMode(tabId);
}

function validateGrade(input) {
    const value = parseFloat(input.value);
    if (value < 0) {
        input.value = 0;
    } else if (value > 100) {
        input.value = 100;
    }
}

function updateTotal(tabId, studentId) {
    const inputs = document.querySelectorAll(`#bulkGradesForm-${tabId} input[name^="grades[${studentId}]"], #bulkGradesForm-${tabId} select[name^="grades[${studentId}]"]`);
    let total = 0;
    let count = 0;
    
    inputs.forEach(input => {
        // For non-numerical grades, skip calculation (symbols can't be averaged)
        if (input.tagName === 'SELECT') {
            return;
        }
        const value = parseFloat(input.value);
        if (!isNaN(value) && value > 0) {
            total += value;
            count++;
        }
    });
    
    const totalDisplay = document.querySelector(`.total-display-${tabId}[data-student="${studentId}"]`);
    if (count > 0) {
        totalDisplay.innerHTML = `<strong>${(total / count).toFixed(1)}</strong>`;
    } else {
        totalDisplay.innerHTML = '<span class="text-muted">-</span>';
    }
}

function submitRecommendation(tabId, sectionId, subjectId) {
    const form = document.getElementById(`bulkGradesForm-${tabId}`);
    const formData = new FormData(form);
    const grades = {};
    
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('grades[') && value) {
            const match = key.match(/grades\[(\d+)\]\[(\d+)\]/);
            if (match) {
                if (!grades[match[1]]) grades[match[1]] = {};
                grades[match[1]][match[2]] = value;
            }
        }
    }
    
    if (Object.keys(grades).length === 0) {
        showGradeAlert('Please enter at least one grade before submitting.', 'warning');
        return;
    }
    
    showConfirmModal('Submit these grade recommendations to the advisory teacher?', function() {
        fetch('<?= base_url('teacher/grades/submit-recommendation') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                section_id: sectionId,
                subject_id: subjectId,
                grades: grades
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showGradeAlert(data.message, 'success');
                cancelInputMode(tabId);
            } else {
                showGradeAlert('Error: ' + (data.error || 'Failed to submit recommendations'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showGradeAlert('An error occurred. Please try again.', 'error');
        });
    });
}

function showConfirmModal(message, onConfirm) {
    const modalHtml = `
    <div class="modal fade" id="gradeConfirmModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="gradeConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>`;
    
    const existing = document.getElementById('gradeConfirmModal');
    if (existing) existing.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modalEl = document.getElementById('gradeConfirmModal');
    const modal = new bootstrap.Modal(modalEl);
    
    document.getElementById('gradeConfirmBtn').addEventListener('click', function() {
        modal.hide();
        onConfirm();
    }, { once: true });
    
    modal.show();
}

function showGradeAlert(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-warning';
    const iconClass = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-x-circle' : 'bi-exclamation-triangle';
    
    const modalHtml = `
    <div class="modal fade" id="gradeAlertModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <i class="bi ${iconClass} fs-1 text-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'}"></i>
                    <div class="mt-3 mb-0">${message}</div>
                    <button type="button" class="btn btn-primary mt-3" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>`;
    
    const existing = document.getElementById('gradeAlertModal');
    if (existing) existing.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('gradeAlertModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const gradeInputs = document.querySelectorAll('[class*="grade-input-"]');
    gradeInputs.forEach(input => {
        input.setAttribute('data-original', input.value);
    });
});

function handleModalClicks(e) {
    // Prevent auto-confirmation by stopping event propagation on modal buttons
    if (e.target.matches('#gradeConfirmBtn, #gradeAlertModal button')) {
        e.stopImmediatePropagation();
    }
}
</script>