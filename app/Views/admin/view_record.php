<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Student Record</h1>
    <a href="<?= base_url('admin/records') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> <?= esc($record['first_name'] . ' ' . $record['last_name']) ?></p>
                <p><strong>LRN:</strong> <?= esc($record['lrn']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Grade Level:</strong> <?= esc(grade_level_label((int) ($record['grade_level'] ?? 0))) ?></p>
                <p><strong>Section:</strong> <?= esc($record['section_name'] ?? 'N/A') ?></p>
                <p><strong>School Year:</strong> <?= $record['school_year'] ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Complete Grades</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Term 1</th>
                        <th>Term 2</th>
                        <th>Term 3</th>
                        <th>Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?= esc($subject['subject_name']) ?></td>
                            <?php
                            $subjectTotal = 0;
                            $subjectCount = 0;
                            for ($t = 1; $t <= 3; $t++):
                                $grade = $grades[$t][$subject['id']] ?? null;
                                if ($grade) {
                                    $subjectTotal += $grade;
                                    $subjectCount++;
                                }
                            ?>
                                <td><?= $grade ? number_format($grade, 2) : '-' ?></td>
                            <?php endfor; ?>
                            <td><strong><?= $subjectCount > 0 ? number_format($subjectTotal / $subjectCount, 2) : '-' ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-primary">
                        <td><strong>Term Average</strong></td>
                        <td><strong><?= !empty($record['term1_average']) ? number_format($record['term1_average'], 2) : '-' ?></strong></td>
                        <td><strong><?= !empty($record['term2_average']) ? number_format($record['term2_average'], 2) : '-' ?></strong></td>
                        <td><strong><?= !empty($record['term3_average']) ? number_format($record['term3_average'], 2) : '-' ?></strong></td>
                        <td><strong><?= !empty($record['final_average']) ? number_format($record['final_average'], 2) : '-' ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
