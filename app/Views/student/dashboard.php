<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<?php
helper('materials');
$studentDashboardMaterials = array_slice(public_website_materials(), 0, 9);
?>

<style>
/* Student stats: same card style as teacher dashboard */
/* Force white text on badges */
.badge.text-white {
  color: #ffffff !important;
}

.badge.bg-success.text-white {
  background-color: #198754 !important;
  color: #ffffff !important;
}

.badge.bg-warning.text-white {
  background-color: #ffc107 !important;
  color: #ffffff !important;
}

.badge.bg-info.text-white {
  background-color: #0dcaf0 !important;
  color: #ffffff !important;
}

.student-widget-divider {
  height: 1px;
  width: 100%;
  background: rgba(59, 130, 246, 0.25);
  border-radius: 999px;
}

/* Student dashboard: two equal stacked widgets beside Featured (grid = reliable height) */
@media (min-width: 992px) {
  .student-dashboard-progress-row {
    align-items: stretch !important;
  }
  .student-dashboard-progress-col {
    /* Grid avoids flex 1 1 0 collapsing to 0 height in some browsers */
    display: grid !important;
    grid-template-rows: 1fr 1fr;
    gap: 1rem;
    align-content: stretch;
    /* If row stretch ever fails, left column still keeps usable space */
    min-height: 420px;
  }
  .student-dashboard-progress-col .student-progress-card {
    min-height: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .student-dashboard-progress-col .student-progress-card .card-body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow-y: auto;
  }
  .student-dashboard-progress-col .student-progress-card .student-progress-inner {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
  }
  .student-dashboard-progress-col .student-progress-card .student-progress-inner .student-progress-actions {
    flex-shrink: 0;
  }

  .student-dashboard-progress-col .student-progress-card .student-progress-inner.student-overview-inner,
  .student-dashboard-progress-col .student-progress-card .student-progress-inner.student-term-inner {
    justify-content: space-between;
  }
}
</style>

<!-- Compact Header Section with Blue Divider -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">Student Dashboard</h1>
      <p class="text-muted mb-0 small">Welcome back, <span class="fw-semibold text-dark"><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></span></p>
      <p class="text-muted small">Last login: <?= date('M j, Y \a\t g:i A') ?></p>
    </div>
  </div>
  
  <!-- Blue Divider Line -->
  <div class="blue-divider"></div>
</div>

<?php if (! empty($nutrition_profile_incomplete)): ?>
  <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start gap-3 mb-4" role="alert">
    <i class="bi bi-heart-pulse fs-4 flex-shrink-0 mt-1"></i>
    <div>
      <strong>Health profile needed.</strong> Please complete your height, weight, and ethnicity on your profile so the school can keep accurate wellness records.
      <a href="<?= base_url('student/profile') ?>" class="alert-link fw-semibold d-inline-block mt-1">Go to My Profile</a>
    </div>
  </div>
<?php endif; ?>

<!-- Quick Stats (same style as teacher dashboard) -->
<div class="row g-3 mb-3 student-stats-row">
  <div class="col-12 col-sm-6 col-lg student-stat-col">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3 py-3">
        <div class="dash-icon-tile dash-icon-tile--slate" aria-hidden="true">
          <i class="bi bi-wallet2"></i>
        </div>
        <div class="flex-grow-1 min-width-0">
          <div class="text-muted small">LRN</div>
          <div class="fw-bold text-primary" style="font-size:1.15rem; line-height:1.2;"><?= esc(!empty($student['lrn']) ? (string) $student['lrn'] : 'Pending') ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-lg student-stat-col">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3 py-3">
        <div class="dash-icon-tile dash-icon-tile--emerald" aria-hidden="true">
          <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="flex-grow-1 min-width-0">
          <div class="text-muted small">Current Grade Level</div>
          <div class="fw-bold text-success" style="font-size:1.15rem; line-height:1.2;"><?= esc(grade_level_label((int) ($student['grade_level'] ?? 0))) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-lg student-stat-col">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3 py-3">
        <div class="dash-icon-tile dash-icon-tile--cyan" aria-hidden="true">
          <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="flex-grow-1 min-width-0">
          <div class="text-muted small">T<?= (int) $currentTerm ?> Average</div>
          <div class="fw-bold" style="font-size:1.15rem; line-height:1.2; color:#0aa2c0;"><?= $termAverage !== null ? number_format($termAverage, 2) : 'N/A' ?></div>
        </div>
        <?php if ($termAverage !== null): ?>
        <a class="btn btn-sm btn-outline-info flex-shrink-0" href="<?= base_url('student/grades') ?>">Grades</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-lg student-stat-col">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3 py-3">
        <div class="dash-icon-tile dash-icon-tile--amber" aria-hidden="true">
          <i class="bi bi-person-check-fill"></i>
        </div>
        <div class="flex-grow-1 min-width-0">
          <div class="text-muted small">Enrollment Status</div>
          <div class="fw-bold" style="font-size:1.15rem; line-height:1.2; color:#b58100;"><?= ucfirst(esc($student['enrollment_status'])) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-lg student-stat-col">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3 py-3">
        <div class="dash-icon-tile dash-icon-tile--violet" aria-hidden="true">
          <i class="bi bi-calendar-event"></i>
        </div>
        <div class="flex-grow-1 min-width-0">
          <div class="text-muted small">School Year</div>
          <div class="fw-bold" style="font-size:1.15rem; line-height:1.2; color:#6f42c1;"><?= get_current_school_year() ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Blue Divider -->
<div class="blue-divider mb-4"></div>

<?php
  $taNumeric = $termAverage !== null ? (float) $termAverage : null;
  $hasTermGrades = $taNumeric !== null;
  $taBarWidth = $hasTermGrades ? min(100, max(0, $taNumeric)) : 0;
  $taLabel = $hasTermGrades ? number_format($taNumeric, 2) . '%' : null;
?>
<!-- Academic Progress + Featured (Featured larger) -->
<div class="row mb-4 student-dashboard-progress-row">
  <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column gap-3 student-dashboard-progress-col">
    <!-- Widget 1: Overview — same flex height as term card on lg+ -->
    <div class="card bg-white border-0 shadow-sm rounded-3 student-progress-card">
      <div class="card-header bg-transparent border-0 pb-0 pt-3 px-3 flex-shrink-0">
        <h4 class="card-title mb-0 fw-semibold">Academic Progress Overview</h4>
      </div>
      <div class="card-body p-3 pt-2">
        <div class="student-progress-inner student-overview-inner">
          <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2">
                School Year <?= esc(get_current_school_year()) ?>
              </span>
              <span class="badge bg-info-subtle text-primary border border-info-subtle rounded-pill px-3 py-2">
                Term <?= (int) $currentTerm ?>
              </span>
            </div>

            <p class="text-muted small mb-0">
              Follow your standing for the selected year and term. Open Grades to see your subject-by-subject performance and remarks.
            </p>
          </div>

          <div class="mt-auto pt-3 student-progress-actions">
            <div class="student-widget-divider mb-3"></div>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
              <a href="<?= base_url('student/grades') ?>" class="btn btn-primary">
                <i class="bi bi-journal-text me-1"></i> View all grades
              </a>
              <div class="small text-muted">Quick breakdown per subject</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Widget 2: Current term -->
    <div class="card bg-white border-0 shadow-sm rounded-3 student-progress-card">
      <div class="card-header bg-transparent border-0 pb-0 pt-3 px-3 flex-shrink-0">
        <h4 class="card-title mb-0 fw-semibold">Current Term Performance</h4>
      </div>
      <div class="card-body p-3 pt-2">
        <div class="student-progress-inner student-term-inner">
        <?php if (!$hasTermGrades): ?>
          <div class="h-100 d-flex flex-column">
            <div class="rounded-3 border bg-light bg-opacity-50 px-3 py-4 d-flex flex-column flex-sm-row align-items-center gap-3">
              <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background: rgba(108,117,125,0.15);color:#6c757d;">
                <i class="bi bi-clipboard-data fs-3"></i>
              </div>
              <div class="flex-grow-1 text-center text-sm-start w-100">
                <p class="mb-1 fw-semibold text-dark">No grades for Term <?= (int) $currentTerm ?> yet</p>
                <p class="text-muted mb-3" style="font-size: 0.95rem;">
                  Teachers will post grades here once available. You can still open Grades anytime.
                </p>
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-sm-start align-items-center">
                  <span class="badge <?= esc($performanceMessage['class']) ?> px-3 py-2 d-inline-flex align-items-center gap-1">
                    <i class="bi <?= esc($performanceMessage['icon'] ?? 'bi-info-circle') ?>" aria-hidden="true"></i>
                    <span><?= esc($performanceMessage['message']) ?></span>
                  </span>
                </div>
              </div>
            </div>

            <div class="student-widget-divider my-3"></div>
            <div class="mt-auto d-flex flex-wrap align-items-center justify-content-between gap-3">
              <a href="<?= base_url('student/grades') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-journal-text me-1"></i> Open Grades
              </a>
              <div class="small text-muted">Per-subject updates</div>
            </div>
          </div>
        <?php else: ?>
          <div class="h-100 d-flex flex-column">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
              <div>
                <div class="small text-muted mb-1">T<?= (int) $currentTerm ?> average</div>
                <div class="fw-bold text-primary" style="font-size: 2rem; line-height: 1.1;"><?= esc(number_format($taNumeric, 2)) ?>%</div>
              </div>

              <div class="performance-message flex-shrink-0 align-self-sm-center">
                <span class="badge <?= esc($performanceMessage['class']) ?> px-3 py-2 d-inline-flex align-items-center gap-1">
                  <i class="bi <?= esc($performanceMessage['icon'] ?? 'bi-info-circle') ?>" aria-hidden="true"></i>
                  <span><?= esc($performanceMessage['message']) ?></span>
                </span>
              </div>
            </div>

            <div class="student-widget-divider my-3"></div>

              <div class="progress mb-0" style="height: 12px;">
                <div
                  class="progress-bar <?= $taNumeric >= 75 ? 'bg-success' : ($taNumeric >= 60 ? 'bg-info' : 'bg-warning') ?>"
                  role="progressbar"
                  style="width: <?= $taBarWidth ?>%"
                  aria-valuenow="<?= $taBarWidth ?>"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  aria-valuetext="<?= number_format($taNumeric, 2) ?> percent"
                ></div>
              </div>

            <div class="d-flex justify-content-between small text-muted mt-2">
              <span>Keep improving</span>
              <span>Great performance</span>
            </div>

            <div class="mt-auto pt-3 small text-muted">
              Open Grades for full subject breakdown and remarks.
            </div>
          </div>
        <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-7 order-1 order-lg-2">
    <div class="card bg-white border-0 shadow-sm rounded-3 overflow-hidden h-100">
      <div class="featured-student-banner">
        <?php if (! empty($featuredPosterStudentUrl)): ?>
          <img
            src="<?= esc($featuredPosterStudentUrl) ?>"
            alt=""
            role="presentation"
            onerror="this.removeAttribute('src'); this.style.display='none';"
          >
        <?php endif; ?>
        <div style="position:absolute; left:16px; bottom:16px; color:white;">
          <div style="font-weight:900; font-size:1.35rem; line-height:1.2;">Featured</div>
          <div style="font-size:1rem; opacity:0.95;">Student Dashboard</div>
          <?php if (empty($featuredPosterStudentUrl)): ?>
            <div style="font-size:0.875rem; opacity:0.85;">(No poster uploaded yet)</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('portal_overlays') ?>
<?= view('partials/public_materials_modal') ?>
<?= $this->endSection() ?>
