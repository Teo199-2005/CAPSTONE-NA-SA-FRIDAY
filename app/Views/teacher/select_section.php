<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-8">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0"><i class="bi bi-list-check me-2"></i>Select Class</h4>
        </div>
        <div class="card-body p-4">
          <p class="text-muted mb-4">Please select which class you want to access:</p>
          
          <div class="row g-3">
            <?php foreach ($sections as $section): ?>
              <div class="col-md-6">
                <div class="card h-100 section-card" onclick="selectSection(<?= $section['id'] ?>)" style="cursor: pointer;">
                  <div class="card-body text-center">
                    <i class="bi bi-<?= $section['type'] === 'Advisory Class' ? 'person-badge' : 'book' ?> fs-1 dash-icon-inline mb-3"></i>
                    <h5 class="card-title"><?= esc($section['name']) ?></h5>
                    <p class="text-muted mb-2"><?= esc(grade_level_label((int) $section['grade'])) ?></p>
                    <span class="badge bg-<?= $section['type'] === 'Advisory Class' ? 'success' : 'info' ?>">
                      <?= esc($section['type']) ?>
                    </span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.section-card {
  transition: all 0.3s ease;
  border: 2px solid #e2e8f0;
}

.section-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  border-color: #3b82f6;
}
</style>

<script>
function selectSection(sectionId) {
  fetch('<?= base_url('teacher/select-section') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'section_id=' + sectionId + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      window.location.href = '<?= base_url('teacher/dashboard') ?>';
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to select section');
  });
}
</script>

<?= $this->endSection() ?>
