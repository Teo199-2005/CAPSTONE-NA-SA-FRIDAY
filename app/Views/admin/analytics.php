<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<!-- Prevent caching of analytics data -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<div class="d-flex justify-content-between align-items-center mb-3 analytics-header">
  <h1 class="h5 mb-0">Analytics Dashboard</h1>
  <div class="d-flex gap-2">
    <a href="<?= base_url('admin/analytics/export-pdf') ?>" class="btn btn-sm btn-primary" target="_blank">
      <i class="bi bi-file-earmark-pdf"></i> Export PDF Report
    </a>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<?php
  $maleCount = (int)($genderDistribution['male'] ?? 0);
  $femaleCount = (int)($genderDistribution['female'] ?? 0);
  $enrolledTotal = (int)($statusDistribution['enrolled'] ?? 0);
  $pendingTotal = (int)($statusDistribution['pending'] ?? 0);
  $approvedTotal = (int)($statusDistribution['approved'] ?? 0);
  $rejectedTotal = (int)($statusDistribution['rejected'] ?? 0);
  $totalStudents = $enrolledTotal + $pendingTotal + $approvedTotal + $rejectedTotal;
?>

<div class="analytics-page compact">
  <div class="card overview-card mb-3">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2 py-2">
      <div class="d-flex align-items-center gap-2">
        <h6 class="mb-0">Overview</h6>
        <small class="text-muted">This school year's quick snapshot</small>
      </div>
      <div class="stat-chips">
        <span class="stat-chip bg-primary-soft">Total <strong><?= $totalStudents ?></strong></span>
        <span class="stat-chip bg-blue-soft">Enrolled <strong><?= $enrolledTotal ?></strong></span>
        <span class="stat-chip bg-amber-soft">Pending <strong><?= $pendingTotal ?></strong></span>
        <span class="stat-chip bg-slate-soft">Approved <strong><?= $approvedTotal ?></strong></span>
        <span class="stat-chip bg-gray-soft">Rejected <strong><?= $rejectedTotal ?></strong></span>
        <span class="stat-chip bg-cyan-soft">Male <strong><?= $maleCount ?></strong></span>
        <span class="stat-chip bg-indigo-soft">Female <strong><?= $femaleCount ?></strong></span>
      </div>
    </div>
  </div>

  <div class="analytics-layout">
    <!-- Left: 2x2 charts grid (compact) -->
    <div class="analytics-cell">
      <div class="charts-grid">

        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Teachers Overview</strong>
            <small class="text-muted d-none d-md-inline">Faculty</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-primary"><?= $teacherStats['active'] ?? 0 ?></div>
                  <small class="text-muted">Active Teachers</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-warning"><?= $teacherStats['with_adviser'] ?? 0 ?></div>
                  <small class="text-muted">With Sections</small>
                </div>
              </div>
            </div>
            <div class="chart-container pie-chart" style="height: 50px;">
              <canvas id="teacherChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Grade Level Distribution</strong>
            <small class="text-muted d-none d-md-inline">Per grade</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-primary"><?= max($gradeDistribution ?? [0]) ?></div>
                  <small class="text-muted">Highest Grade</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-info"><?= count(array_filter($gradeDistribution ?? [])) ?></div>
                  <small class="text-muted">Active Grades</small>
                </div>
              </div>
            </div>
            <div class="chart-container bar-chart" style="height: 50px;">
              <canvas id="gradeChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Gender Distribution</strong>
            <small class="text-muted d-none d-md-inline">Enrolled</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-primary"><?= $maleCount ?></div>
                  <small class="text-muted">Male</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-pink"><?= $femaleCount ?></div>
                  <small class="text-muted">Female</small>
                </div>
              </div>
            </div>
            <div class="chart-container pie-chart" style="height: 50px;">
              <canvas id="genderChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>

        <div class="card chart-card">
          <div class="card-header d-flex justify-content-between align-items-center py-2">
            <strong class="small">Enrollment Status</strong>
            <small class="text-muted d-none d-md-inline">Breakdown</small>
          </div>
          <div class="card-body py-2">
            <div class="row mb-2">
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-success"><?= $enrolledTotal ?></div>
                  <small class="text-muted">Enrolled</small>
                </div>
              </div>
              <div class="col-6">
                <div class="text-center">
                  <div class="h5 mb-0 text-warning"><?= $pendingTotal ?></div>
                  <small class="text-muted">Pending</small>
                </div>
              </div>
            </div>
            <div class="chart-container pie-chart" style="height: 50px;">
              <canvas id="statusChart" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: compact widgets stacked -->
    <div class="analytics-cell">
      <div class="card mb-3">
        <div class="card-header py-2"><strong class="small">Key Metrics</strong></div>
        <div class="card-body py-2">
          <div class="metric-row"><span>Completion</span><strong><?= esc(($metrics['completionRate'] ?? 0) . '%') ?></strong></div>
          <div class="metric-row"><span>Pending</span><strong><?= esc(($metrics['pendingRate'] ?? 0) . '%') ?></strong></div>
          <div class="metric-row"><span>Approval</span><strong><?= esc(($metrics['approvalRate'] ?? 0) . '%') ?></strong></div>
          <div class="metric-row mb-0"><span>Gender Gap</span><strong><?= esc($metrics['genderBalance'] ?? 0) ?></strong></div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
          <strong class="small">Recent Enrolled</strong>
          <small class="text-muted">Latest 5</small>
        </div>
        <div class="card-body p-0">
          <?php if (!empty($recentEnrolled)): ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($recentEnrolled as $s): ?>
                <li class="list-group-item py-2 d-flex justify-content-between align-items-center">
                  <span class="small text-truncate" style="max-width: 170px;">
                    <?= esc(($s['last_name'] ?? '') . ', ' . ($s['first_name'] ?? '')) ?>
                  </span>
                  <small class="text-muted">G<?= esc($s['grade_level'] ?? '-') ?></small>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted m-2 small">No recent records.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-header py-2">
          <strong class="small">Average Grade (T<?= esc((string) ($currentTerm ?? get_current_term())) ?>)</strong>
        </div>
        <div class="card-body py-2">
          <?php foreach (grade_level_options() as $g): ?>
          <div class="metric-row<?= $g === grade_level_max() ? ' mb-0' : '' ?>"><span><?= esc(grade_level_label($g)) ?></span><strong><?= esc($gradeAverages[$g] ?? 0) ?></strong></div>
          <?php endforeach; ?>
          <small class="text-muted d-block mt-1 small">SY: <?= esc($schoolYear ?? get_current_school_year()) ?></small>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const css = getComputedStyle(document.documentElement);
const colorPrimary = css.getPropertyValue('--color-primary').trim() || '#1e40af';
const colorPrimaryLight = css.getPropertyValue('--color-primary-light').trim() || '#3b82f6';
const colorHeading = css.getPropertyValue('--color-heading').trim() || '#0f172a';

const genderData = <?= json_encode($genderDistribution ?? []) ?>;
const statusData = <?= json_encode($statusDistribution ?? []) ?>;
const teacherData = <?= json_encode($teacherStats ?? []) ?>;

document.addEventListener('DOMContentLoaded', function () {
const chartDefaults = { responsive: true, maintainAspectRatio: false };

// Grade Bar (modern blue palette)
const gradeEl = document.getElementById('gradeChart');
if (gradeEl) new Chart(gradeEl, { type: 'bar', data: { labels: <?= json_encode(grade_level_chart_labels()) ?>, datasets: [{ data: <?= json_encode(array_map(static fn (int $g): int => (int) ($gradeDistribution[$g] ?? $gradeDistribution[(string) $g] ?? 0), grade_level_options())) ?>, backgroundColor: ['#7c3aed', colorPrimary, colorPrimaryLight, '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe'], borderRadius: 6 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.06)' }, ticks: { stepSize: 1, color: colorHeading, maxTicksLimit: 4 } }, x: { grid: { display: false }, ticks: { color: colorHeading } } }, plugins: { legend: { display: false } } } });

// Teacher Doughnut (orange shades)
const teacherEl = document.getElementById('teacherChart');
if (teacherEl) new Chart(teacherEl, { type: 'doughnut', data: { labels: ['With Sections','Available'], datasets: [{ data: [teacherData.with_adviser ?? 0, teacherData.without_adviser ?? 0], backgroundColor: ['#f59e0b', '#fbbf24'], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { color: colorHeading, boxWidth: 10 } } } } });

// Gender Doughnut (blue shades)
const genderEl = document.getElementById('genderChart');
if (genderEl) new Chart(genderEl, { type: 'doughnut', data: { labels: ['Male','Female'], datasets: [{ data: [genderData.male ?? 0, genderData.female ?? 0], backgroundColor: [colorPrimary, colorPrimaryLight], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { color: colorHeading, boxWidth: 10 } } } } });



// Enrollment Status (doughnut)
const statusEl = document.getElementById('statusChart');
if (statusEl) new Chart(statusEl, { type: 'doughnut', data: { labels: ['Enrolled','Pending','Approved','Rejected'], datasets: [{ data: [statusData.enrolled ?? 0, statusData.pending ?? 0, statusData.approved ?? 0, statusData.rejected ?? 0], backgroundColor: [colorPrimary, colorPrimaryLight, '#60a5fa', '#94a3b8'], borderWidth: 0 }] },   options: { ...chartDefaults, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { color: colorHeading, boxWidth: 10 } } } } });

});
</script>
<?= $this->endSection() ?> 
