<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge dash-icon-inline"></i>
                        <?= esc($student['first_name'] . ' ' . $student['last_name']) ?> - Academic Record
                    </h5>
                    <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i> Close
                    </button>
                </div>
                <div class="card-body">
                    <!-- Student Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>LRN:</strong> <?= esc($student['lrn']) ?></p>
                            <p><strong>Grade Level:</strong> <?= esc(grade_level_label((int) ($student['grade_level'] ?? 0))) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>School Year:</strong> <?= esc($schoolYear) ?></p>
                            <p><strong>GWA:</strong> 
                                <span class="badge bg-<?= $gwa >= 90 ? 'success' : ($gwa >= 85 ? 'info' : ($gwa >= 75 ? 'warning' : 'danger')) ?> text-white">
                                    <?= $gwa ? number_format($gwa, 2) : 'N/A' ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Term Tabs -->
                    <ul class="nav nav-tabs" id="termTabs" role="tablist">
                        <?php for ($t = 1; $t <= 3; $t++): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $t === 1 ? 'active' : '' ?>"
                                        id="term<?= $t ?>-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#term<?= $t ?>"
                                        type="button" role="tab">
                                    Term <?= $t ?>
                                    <?php if (isset($allTermGrades[$t]) && $allTermGrades[$t] !== null): ?>
                                        <span class="badge bg-secondary ms-1"><?= number_format($allTermGrades[$t], 1) ?></span>
                                    <?php endif; ?>
                                </button>
                            </li>
                        <?php endfor; ?>
                    </ul>

                    <!-- Term Content -->
                    <div class="tab-content" id="termTabsContent">
                        <?php for ($t = 1; $t <= 3; $t++): ?>
                            <div class="tab-pane fade <?= $t === 1 ? 'show active' : '' ?>"
                                 id="term<?= $t ?>" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Subject</th>
                                                <th>Code</th>
                                                <th>Grade</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($grades[$t])): ?>
                                                <?php foreach ($grades[$t] as $gradeData): ?>
                                                    <tr>
                                                        <td><?= esc($gradeData['subject']['subject_name']) ?></td>
                                                        <td><span class="badge bg-light text-dark"><?= esc($gradeData['subject']['subject_code']) ?></span></td>
                                                        <td>
                                                            <?php if ($gradeData['grade']): ?>
                                                                <span class="badge bg-<?= $gradeData['grade']['grade'] >= 90 ? 'success' : ($gradeData['grade']['grade'] >= 85 ? 'info' : ($gradeData['grade']['grade'] >= 75 ? 'warning' : 'danger')) ?> text-white">
                                                                    <?= number_format($gradeData['grade']['grade'], 2) ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">Not graded</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($gradeData['grade'] && isset($gradeData['grade']['remarks'])): ?>
                                                                <?= esc($gradeData['grade']['remarks']) ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">--</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="2"><strong class="fs-5">Term Average:</strong></td>
                                                <td>
                                                    <?php if (isset($allTermGrades[$t]) && $allTermGrades[$t] !== null): ?>
                                                        <span class="badge bg-<?= $allTermGrades[$t] >= 90 ? 'success' : ($allTermGrades[$t] >= 85 ? 'info' : ($allTermGrades[$t] >= 75 ? 'warning' : 'danger')) ?> text-white fs-5 px-3 py-2">
                                                            <?= number_format($allTermGrades[$t], 2) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted fs-5">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($allTermGrades[$t]) && $allTermGrades[$t] !== null): ?>
                                                        <span class="badge bg-<?= $allTermGrades[$t] >= 75 ? 'success' : 'danger' ?> text-white fs-5 px-3 py-2">
                                                            <?= $allTermGrades[$t] >= 75 ? 'PASSED' : 'FAILED' ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted fs-5">--</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>