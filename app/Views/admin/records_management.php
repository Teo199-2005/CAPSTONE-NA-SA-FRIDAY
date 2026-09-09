<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
.grade-card { border-left: 4px solid #0d6efd; margin-bottom: 1rem; }
.section-item { background: #f8f9fa; border-radius: 8px; padding: 12px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s; }
.section-item:hover { background: #e9ecef; transform: translateX(4px); }
.student-link { display: block; padding: 10px 15px; border-radius: 6px; text-decoration: none; color: inherit; transition: all 0.2s; }
.student-link:hover { background: #e7f3ff; color: #0d6efd; }
.badge-count { font-size: 0.85rem; padding: 4px 10px; }
</style>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-2">Records Management</h1>
            <p class="text-muted mb-0">View and manage student report cards by grade level and section</p>
        </div>
        <div class="text-end">
            <label class="fw-bold d-block mb-1">School Year</label>
            <select name="year" class="form-select form-select-sm" style="min-width: 150px;" onchange="window.location.href='?year='+this.value">
                <?php if (empty($schoolYears)): ?>
                    <option value="<?= get_current_school_year() ?>"><?= get_current_school_year() ?></option>
                <?php else: ?>
                    <?php foreach ($schoolYears as $year): ?>
                        <option value="<?= $year['school_year'] ?>" <?= $year['school_year'] == $selectedYear ? 'selected' : '' ?>>
                            <?= $year['school_year'] ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>

</div>

<?php if (empty($groupedRecords)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <h5 class="mt-3">No Records</h5>
            <p class="text-muted">No sections found for <?= $selectedYear ?></p>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($groupedRecords as $gradeLevel => $sections): ?>
        <?php
        $colors = [
            0 => '#7c3aed',
            1 => '#0d6efd',
            2 => '#0056b3',
            3 => '#004085',
            4 => '#002752',
            5 => '#28a745',
            6 => '#1e7e34',
        ];
        $bgColor = $colors[$gradeLevel] ?? '#0d6efd';
        ?>
        <div class="card grade-card mb-3">
                    <div class="card-header" style="cursor: pointer; background: <?= $bgColor ?>;" onclick="toggleGrade(<?= $gradeLevel ?>)">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white"><i class="bi bi-mortarboard me-2"></i><?= esc(grade_level_label((int) $gradeLevel)) ?></h5>
                            <i class="bi bi-chevron-down text-white" id="icon-grade-<?= $gradeLevel ?>"></i>
                        </div>
                    </div>
                    <div class="card-body" id="grade-<?= $gradeLevel ?>" style="display: none;">
                        <div class="mb-3">
                            <small class="text-muted"><i class="bi bi-folder2-open me-1"></i><?= count($sections) ?> section(s) available</small>
                            <p class="text-muted small mb-0 mt-1">Click a section to view student report cards</p>
                        </div>
                        <div class="list-group">
                            <?php foreach ($sections as $sectionName => $students): ?>
                                <a href="<?= base_url('admin/records/section/' . $gradeLevel . '/' . urlencode($sectionName) . '?year=' . $selectedYear) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-people me-2"></i><?= esc($sectionName) ?></span>
                                    <i class="bi bi-arrow-right-circle"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
function toggleGrade(grade) {
    // Get all grade content divs
    const allGrades = document.querySelectorAll('[id^="grade-"]');
    const currentContent = document.getElementById('grade-' + grade);
    const currentIcon = document.getElementById('icon-grade-' + grade);
    
    // Close all grades
    allGrades.forEach(el => {
        if (el !== currentContent) {
            el.style.display = 'none';
        }
    });
    
    // Reset all icons
    document.querySelectorAll('[id^="icon-grade-"]').forEach(icon => {
        if (icon !== currentIcon) {
            icon.className = 'bi bi-chevron-down text-white';
        }
    });
    
    // Toggle current grade
    if (currentContent.style.display === 'none' || currentContent.style.display === '') {
        currentContent.style.display = 'block';
        currentIcon.className = 'bi bi-chevron-up text-white';
    } else {
        currentContent.style.display = 'none';
        currentIcon.className = 'bi bi-chevron-down text-white';
    }
}
</script>

<?= $this->endSection() ?>
