<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Set Class Schedule</h1>
  <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
</div>

<div class="alert alert-warning">
  <div class="d-flex align-items-start">
    <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 2rem;"></i>
    <div>
      <h5 class="alert-heading mb-2">Schedule Creation Restricted</h5>
      <p class="mb-0">You cannot create or set schedules for students until the admin has assigned a schedule to you.</p>
      <hr class="my-3">
      <p class="mb-0"><strong>What to do:</strong> Please contact the administrator to assign your teaching schedule first. Once your schedule is assigned, you will be able to create class schedules for your students.</p>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body text-center py-5">
    <i class="bi bi-calendar-x" style="font-size: 4rem; color: #6c757d; opacity: 0.3;"></i>
    <h4 class="mt-3 text-muted">No Schedule Access</h4>
    <p class="text-muted">Schedule management will be available once the admin assigns your teaching schedule.</p>
  </div>
</div>

<?= $this->endSection() ?>
