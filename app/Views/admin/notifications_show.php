<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">📋 Notification Details</h1>
  <div class="d-flex gap-2">
    <a href="<?= base_url('admin/notifications/edit/' . $notification['id']) ?>" class="btn btn-warning btn-sm">
      <i class="bi bi-pencil me-2"></i>Edit & Resend
    </a>
    <a href="<?= base_url('admin/notifications') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-2"></i>Back to List
    </a>
  </div>
</div>

<?php if ($errors = session('errors')): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if ($success = session('success')): ?>
  <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>

<!-- Notification Overview -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="card-title mb-0">
      <i class="bi bi-info-circle me-2"></i>Notification Overview
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-8">
        <h5 class="text-primary"><?= esc($notification['title']) ?></h5>
        <div class="bg-light p-3 rounded">
          <p class="mb-0"><?= nl2br(esc($notification['message'])) ?></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="row g-3">
          <div class="col-6">
            <div class="text-center">
              <div class="h4 text-info mb-1"><?= count($relatedNotifications) ?></div>
              <small class="text-muted">Total Recipients</small>
            </div>
          </div>
          <div class="col-6">
            <div class="text-center">
              <?php 
                $readCount = array_filter($relatedNotifications, function($n) { return $n['is_read']; });
                $readPercentage = count($relatedNotifications) > 0 ? round((count($readCount) / count($relatedNotifications)) * 100) : 0;
              ?>
              <div class="h4 text-success mb-1"><?= $readPercentage ?>%</div>
              <small class="text-muted">Read Rate</small>
            </div>
          </div>
          <div class="col-12">
            <div class="text-center">
              <small class="text-muted">
                <i class="bi bi-calendar me-1"></i>
                Sent: <?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?>
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recipients List -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-transparent border-0">
    <h5 class="card-title mb-0">
      <i class="bi bi-people me-2 dash-icon-inline"></i>Recipients (<?= count($relatedNotifications) ?>)
    </h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th class="border-0">Recipient</th>
            <th class="border-0">Status</th>
            <th class="border-0">Read At</th>
            <th class="border-0">Sent At</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($relatedNotifications as $recipient): ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                    <?= strtoupper(substr($recipient['email'], 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-semibold"><?= esc($recipient['email']) ?></div>
                    <small class="text-muted">ID: <?= $recipient['user_id'] ?></small>
                  </div>
                </div>
              </td>
              <td>
                <?php if ($recipient['is_read']): ?>
                  <span class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i>Read
                  </span>
                <?php else: ?>
                  <span class="badge bg-warning">
                    <i class="bi bi-clock me-1"></i>Unread
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($recipient['read_at']): ?>
                  <small class="text-muted"><?= date('M j, Y g:i A', strtotime($recipient['read_at'])) ?></small>
                <?php else: ?>
                  <small class="text-muted">Not read yet</small>
                <?php endif; ?>
              </td>
              <td>
                <small class="text-muted"><?= date('M j, Y g:i A', strtotime($recipient['created_at'])) ?></small>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
.avatar-sm {
  width: 32px;
  height: 32px;
  font-size: 0.75rem;
  font-weight: 600;
}
</style>

<?= $this->endSection() ?>
