<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
/* Force white text on "Not recorded" badges */
.badge.bg-secondary.text-white {
  color: #ffffff !important;
  background-color: #6c757d !important;
}

/* Fix modal z-index issues */
.modal-backdrop {
  z-index: 1050 !important;
}

#enrollmentConfirmModal {
  z-index: 1055 !important;
}

#enrollmentConfirmModal .modal-dialog {
  z-index: 1056 !important;
}

#enrollmentConfirmModal .modal-content {
  z-index: 1057 !important;
  pointer-events: auto !important;
}
</style>

<!-- Compact Header Section with Blue Divider -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">My Academic Grades</h1>
      <p class="text-muted mb-0 small">Track your academic progress and performance</p>
    </div>
    <div class="d-flex gap-2">
      <?php if (($student['can_view_report_card'] ?? 1) == 1): ?>
        <a href="<?= base_url('student/report-card') ?>" class="btn btn-success btn-sm">
          <i class="bi bi-file-earmark-pdf me-2"></i>View Report Card
        </a>
      <?php else: ?>
        <button class="btn btn-secondary btn-sm" disabled title="Report card access disabled by teacher">
          <i class="bi bi-file-earmark-pdf me-2"></i>View Report Card
        </button>
      <?php endif; ?>
      <a href="<?= base_url('student/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
      </a>
    </div>
  </div>

  <!-- Blue Divider Line -->
  <div class="blue-divider"></div>
</div>

<!-- Compact Stats Cards -->
<div class="d-flex gap-3 mb-4">
  <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
    <div class="card-body text-center p-3">
      <div class="stats-icon bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
        <i class="bi bi-trophy-fill text-white fs-5"></i>
      </div>
      <h4 class="stats-number text-success mb-1 small"><?= $gwa !== null ? number_format($gwa, 2) : 'N/A' ?></h4>
      <p class="stats-label text-muted fw-medium mb-0 small">General Weighted Average</p>
    </div>
  </div>

  <?php for ($t = 1; $t <= 3; $t++): ?>
    <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
      <div class="card-body text-center p-3">
        <div class="stats-icon bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
          <i class="bi bi-calendar-check text-white fs-5"></i>
        </div>
        <h4 class="stats-number text-primary mb-1 small">
          <?= isset($allTermGrades[$t]) && $allTermGrades[$t] !== null
              ? number_format($allTermGrades[$t], 1)
              : '--' ?>
        </h4>
        <p class="stats-label text-muted fw-medium mb-0 small">Term <?= $t ?></p>
      </div>
    </div>
  <?php endfor; ?>
</div>

<!-- Blue Divider -->
<div class="blue-divider mb-4"></div>

<!-- Grades Table -->
<div class="card bg-white border-0 shadow-sm rounded-3 mb-4">
  <div class="card-header bg-transparent border-0 p-3">
    <h4 class="card-title mb-0 small">
      <i class="bi bi-list-check me-2 dash-icon-inline"></i>
      Term <?= esc($term) ?> Grades
    </h4>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th class="border-0 fw-medium small">Subject</th>
            <th class="border-0 fw-medium small">Code</th>
            <th class="border-0 fw-medium small text-center">Grade</th>
            <th class="border-0 fw-medium small">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($grades)): ?>
            <?php foreach ($grades as $row): ?>
              <tr>
                <td class="py-2">
                  <div class="fw-medium text-dark small"><?= esc($row['subject']['subject_name']) ?></div>
                </td>
                <td class="py-2">
                  <span class="badge bg-light text-dark small"><?= esc($row['subject']['subject_code']) ?></span>
                </td>
                <td class="py-2 text-center">
                  <?php if ($row['grade'] && $row['grade']['grade'] !== null): ?>
                    <?php
                      $grade = $row['grade']['grade'];
                      $badgeClass = $grade >= 90 ? 'success' : ($grade >= 85 ? 'info' : ($grade >= 75 ? 'warning' : 'danger'));
                    ?>
                    <span class="badge bg-<?= $badgeClass ?> small" style="color: white !important;">
                      <?= number_format($grade, 2) ?>
                    </span>
                  <?php else: ?>
                    <span class="badge bg-secondary small" style="color: white !important;">Not recorded</span>
                  <?php endif; ?>
                </td>
                <td class="py-2">
                  <?php if ($row['grade'] && isset($row['grade']['remarks'])): ?>
                    <span class="text-muted small"><?= esc($row['grade']['remarks']) ?></span>
                  <?php else: ?>
                    <span class="text-muted small">--</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                <small>No grades recorded for this term yet.</small>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Term Summary -->
  <div class="card-footer bg-light border-0 p-3">
    <div class="row align-items-center">
      <div class="col">
        <strong class="text-primary small">Term <?= esc($term) ?> Average:</strong>
        <span class="ms-2 fw-bold">
          <?= $termAverage !== null ? number_format($termAverage, 2) : 'N/A' ?>
        </span>
      </div>
      <div class="col-auto">
        <?php if ($termAverage !== null): ?>
          <?php
            $avgBadgeClass = $termAverage >= 90 ? 'success' : ($termAverage >= 85 ? 'info' : ($termAverage >= 75 ? 'warning' : 'danger'));
            $avgMessage = $termAverage >= 90 ? 'Excellent' : ($termAverage >= 85 ? 'Very Good' : ($termAverage >= 75 ? 'Good' : 'Needs Improvement'));
          ?>
          <span class="badge bg-<?= $avgBadgeClass ?> small" style="color: white !important;">
            <?= $avgMessage ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>







<?= $this->endSection() ?>