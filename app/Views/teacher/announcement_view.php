<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3"><?= esc($announcement['title']) ?></h1>
    <div class="d-flex align-items-center gap-3 mt-2">
      <span class="badge bg-<?= $announcement['target_roles'] === 'admin' ? 'primary' : ($announcement['target_roles'] === 'all' ? 'success' : 'info') ?>">
        <?= ucfirst(esc($announcement['target_roles'])) ?>
      </span>
      <small class="text-muted">
        <i class="bi bi-calendar me-1"></i><?= date('F j, Y \a\t g:i A', strtotime($announcement['created_at'])) ?>
      </small>
    </div>
  </div>
  <a href="<?= base_url('teacher/announcements') ?>" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-2"></i>Back to Announcements
  </a>
</div>

<div class="card">
  <div class="card-body">
    <div class="announcement-content">
      <?= $announcement['body'] ?>
    </div>
  </div>
</div>

<style>
.announcement-content {
  line-height: 1.6;
  font-size: 1.1rem;
}
.announcement-content h1, .announcement-content h2, .announcement-content h3, 
.announcement-content h4, .announcement-content h5, .announcement-content h6 {
  margin-top: 1.5rem;
  margin-bottom: 1rem;
}
.announcement-content p {
  margin-bottom: 1rem;
}
</style>

<?= $this->endSection() ?>