<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-week"></i> Section Schedules</h2>
    </div>

    <div class="row g-3" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
    <?php foreach (grade_level_options() as $grade): ?>
        <?php 
        $gradeSections = array_filter($sections, fn($s) => $s['grade_level'] == $grade);
        if (empty($gradeSections)) continue;
        ?>
        
        <div>
            <div class="card h-100">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0"><i class="bi bi-mortarboard"></i> <?= esc(grade_level_label((int) $grade)) ?></h6>
                </div>
                <div class="card-body p-2">
                    <?php foreach ($gradeSections as $section): ?>
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= esc($section['section_name']) ?></strong><br>
                                    <small class="text-muted">
                                        <i class="bi bi-people"></i> <?= $section['current_enrollment'] ?>/<?= $section['max_capacity'] ?>
                                        <i class="bi bi-person-badge ms-2"></i> <?= esc($section['adviser_name'] ?? 'No Adviser') ?>
                                    </small>
                                </div>
                                <button class="btn btn-sm btn-primary" onclick="viewSchedule(<?= $section['id'] ?>, '<?= esc($section['section_name']) ?>', <?= $grade ?>)">
                                    <i class="bi bi-calendar-check me-1"></i>Manage
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<script>
function viewSchedule(sectionId, sectionName, gradeLevel) {
    window.location.href = `<?= base_url('admin/schedules/section/') ?>${sectionId}`;
}
</script>

<?= $this->endSection() ?>
