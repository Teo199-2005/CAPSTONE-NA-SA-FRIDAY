<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<?php helper('nutrition'); ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h1 class="h3 mb-1">Student nutrition / BMI</h1>
    <p class="text-muted small mb-0">Enrolled students — filter by grade, section, or screening category. Export respects current filters.</p>
    <p class="small text-muted mb-0"><strong>Active filters:</strong> <?= esc($filtersSummary) ?></p>
  </div>
  <div class="d-flex gap-2">
    <?php
    $pdfParams = array_filter([
        'grade_level'       => $filter_grade !== null && $filter_grade !== '' ? (string) $filter_grade : null,
        'section_id'        => $filter_section !== null && $filter_section !== '' ? (string) $filter_section : null,
        'nutrition_status'  => $filter_status !== null && $filter_status !== '' ? (string) $filter_status : null,
        'q'                 => isset($filter_q) && $filter_q !== null && trim((string) $filter_q) !== '' ? trim((string) $filter_q) : null,
    ], static fn ($v) => $v !== null && $v !== '');
    $pdfUrl = base_url('admin/student-nutrition/export-pdf') . ($pdfParams !== [] ? '?' . http_build_query($pdfParams) : '');
    ?>
    <a href="<?= esc($pdfUrl) ?>" class="btn btn-primary" target="_blank" rel="noopener">
      <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
    </a>
  </div>
</div>

<form class="card border-0 shadow-sm mb-4" method="get" action="<?= base_url('admin/student-nutrition') ?>">
  <div class="card-body row g-3 align-items-end">
    <div class="col-md-2">
      <label class="form-label">Grade</label>
      <select name="grade_level" class="form-select">
        <option value="">All</option>
        <?php foreach (grade_level_options() as $g): ?>
          <option value="<?= $g ?>" <?= (string) ($filter_grade ?? '') === (string) $g ? 'selected' : '' ?>><?= esc(grade_level_label($g)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Section</label>
      <select name="section_id" class="form-select">
        <option value="">All sections</option>
        <?php foreach ($sections as $sec): ?>
          <option value="<?= (int) $sec['id'] ?>" <?= (string) ($filter_section ?? '') === (string) $sec['id'] ? 'selected' : '' ?>>
            G<?= esc((string) $sec['grade_level']) ?> — <?= esc($sec['section_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Nutrition status</label>
      <select name="nutrition_status" class="form-select">
        <?php foreach (nutrition_status_filter_options() as $val => $lab): ?>
          <option value="<?= esc((string) $val) ?>" <?= (string) ($filter_status ?? '') === (string) $val ? 'selected' : '' ?>><?= esc($lab) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Search</label>
      <input type="text" name="q" class="form-control" placeholder="Name or LRN" value="<?= esc((string) ($filter_q ?? '')) ?>">
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button type="submit" class="btn btn-primary flex-grow-1">Apply</button>
      <a href="<?= base_url('admin/student-nutrition') ?>" class="btn btn-outline-secondary">Reset</a>
    </div>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-striped mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>LRN</th>
            <th>Grade</th>
            <th>Section</th>
            <th>Age (y)</th>
            <th>Sex</th>
            <th>Height</th>
            <th>Weight</th>
            <th>BMI</th>
            <th>Ethnicity</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($students === []): ?>
            <tr><td colspan="11" class="text-center text-muted py-4">No students match these filters.</td></tr>
          <?php endif; ?>
          <?php foreach ($students as $s): ?>
            <?php
            $ageY = '—';
            if (! empty($s['date_of_birth'])) {
                try {
                    $dob = new \DateTimeImmutable($s['date_of_birth']);
                    $ageY = (string) $dob->diff(new \DateTimeImmutable('today'))->y;
                } catch (\Throwable) {
                    $ageY = '—';
                }
            }
            $name = trim($s['first_name'] . ' ' . ($s['middle_name'] ? $s['middle_name'] . ' ' : '') . $s['last_name']);
            ?>
            <tr>
              <td><?= esc($name) ?></td>
              <td><?= esc((string) ($s['lrn'] ?? '')) ?></td>
              <td><?= esc((string) ($s['grade_level'] ?? '')) ?></td>
              <td><?= esc((string) ($s['section_name'] ?? '—')) ?></td>
              <td><?= esc($ageY) ?></td>
              <td><?= esc((string) ($s['gender'] ?? '')) ?></td>
              <td><?= $s['height_cm'] !== null && $s['height_cm'] !== '' ? esc((string) $s['height_cm']) : '—' ?></td>
              <td><?= $s['weight_kg'] !== null && $s['weight_kg'] !== '' ? esc((string) $s['weight_kg']) : '—' ?></td>
              <td><?= $s['bmi'] !== null && $s['bmi'] !== '' ? esc((string) $s['bmi']) : '—' ?></td>
              <td><?= esc(ethnicity_option_label($s['ethnicity'] ?? null)) ?></td>
              <td>
                <?php if (! empty($s['nutrition_status'])): ?>
                  <span class="badge bg-secondary"><?= esc(\App\Libraries\StudentNutritionClassifier::statusLabel($s['nutrition_status'])) ?></span>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
