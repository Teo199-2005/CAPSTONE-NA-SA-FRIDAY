<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3">Announcements & Notifications</h1>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <?php if (!empty($announcements)): ?>
      <?php foreach ($announcements as $announcement): ?>
        <a href="<?= base_url('student/announcements/view/' . $announcement['id']) ?>" class="announcement-item border-bottom pb-3 mb-3 text-decoration-none text-dark d-block <?= !$announcement['is_read'] ? 'unread-announcement' : '' ?>">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center gap-2">
              <?php if (!$announcement['is_read']): ?>
                <span class="badge bg-success">New</span>
              <?php endif; ?>
              <h6 class="mb-1 <?= !$announcement['is_read'] ? 'fw-bold' : '' ?>"><?= esc($announcement['title']) ?></h6>
            </div>
            <small class="text-muted">
              <?= date('M j, Y', strtotime($announcement['created_at'])) ?>
            </small>
          </div>
          <p class="text-muted mb-0">
            <?= strip_tags(substr($announcement['body'], 0, 200)) ?><?= strlen(strip_tags($announcement['body'])) > 200 ? '...' : '' ?>
          </p>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="text-center py-4">
        <i class="bi bi-megaphone fs-1 text-muted mb-3"></i>
        <h5 class="text-muted">No Announcements</h5>
        <p class="text-muted mb-0">No announcements have been posted yet.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
.announcement-item:last-child {
  border-bottom: none !important;
  margin-bottom: 0 !important;
  padding-bottom: 0 !important;
}
.announcement-item:hover {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 12px;
  margin: -12px;
  margin-bottom: 12px;
}
.unread-announcement {
  background-color: #e7f3ff;
  border-left: 4px solid #0d6efd;
  padding-left: 12px;
}
</style>

<?= $this->endSection() ?> 