<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
/* CRUD Table Styling */
.notification-table {
  font-size: 0.9rem;
}

.notification-table .btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.notification-preview {
  max-width: 300px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.stats-badge {
  font-size: 0.75rem;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Bulk Notifications</h1>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-info btn-sm" onclick="refreshStats()">
      <i class="bi bi-arrow-clockwise me-1"></i>Refresh
    </button>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
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

<!-- Create New Notification -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="card-title mb-0">
      <i class="bi bi-plus-circle me-2"></i>Send New Notification
    </h5>
  </div>
  <div class="card-body">
    <form method="post" action="<?= base_url('admin/notifications/send') ?>" id="notificationForm">
      <?= csrf_field() ?>
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Target Audience</label>
          <select name="target" class="form-select" required>
            <option value="">Select Target</option>
            <option value="all">All Users</option>
            <option value="admin">Admins</option>
            <option value="teacher">Teachers</option>
            <option value="student">Students</option>
            <option value="parent">Parents</option>
          </select>
        </div>
        <div class="col-md-9">
          <label class="form-label fw-semibold">Title</label>
          <input type="text" class="form-control" name="title" placeholder="Enter notification title" required />
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Message</label>
          <textarea class="form-control" name="message" rows="4" placeholder="Enter your message here..." required></textarea>
        </div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-send me-2"></i>Send Notification
        </button>
        <button class="btn btn-outline-secondary" type="reset">
          <i class="bi bi-arrow-clockwise me-2"></i>Clear
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Sent Notifications List -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">
      <i class="bi bi-list-ul me-2 dash-icon-inline"></i>Sent Notifications
    </h5>
    <div class="d-flex gap-2">
      <span class="badge bg-info" id="totalSent">Total: <?= count($sentNotifications) ?></span>
      <span class="badge bg-success" id="totalRead">Read: 0</span>
      <span class="badge bg-warning" id="totalUnread">Unread: 0</span>
    </div>
  </div>
  <div class="card-body p-0">
    <?php if (!empty($sentNotifications)): ?>
      <div class="table-responsive">
        <table class="table table-hover notification-table mb-0">
          <thead class="table-light">
            <tr>
              <th class="border-0">Title</th>
              <th class="border-0">Message Preview</th>
              <th class="border-0">Target</th>
              <th class="border-0">Recipients</th>
              <th class="border-0">Read Rate</th>
              <th class="border-0">Sent</th>
              <th class="border-0 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sentNotifications as $notification): ?>
              <tr>
                <td>
                  <div class="fw-semibold text-dark"><?= esc($notification['title']) ?></div>
                </td>
                <td>
                  <div class="notification-preview text-muted">
                    <?= esc(substr($notification['message'], 0, 100)) ?><?= strlen($notification['message']) > 100 ? '...' : '' ?>
                  </div>
                </td>
                <td>
                  <span class="badge bg-secondary"><?= esc($notification['target_groups']) ?></span>
                </td>
                <td>
                  <span class="badge bg-info stats-badge"><?= $notification['recipient_count'] ?></span>
                </td>
                <td>
                  <?php
                    $readPercentage = $notification['recipient_count'] > 0
                      ? round(($notification['read_count'] / $notification['recipient_count']) * 100)
                      : 0;
                    $badgeClass = $readPercentage >= 70 ? 'success' : ($readPercentage >= 40 ? 'warning' : 'danger');
                  ?>
                  <span class="badge bg-<?= $badgeClass ?> stats-badge">
                    <?= $notification['read_count'] ?>/<?= $notification['recipient_count'] ?> (<?= $readPercentage ?>%)
                  </span>
                </td>
                <td>
                  <small class="text-muted"><?= date('M j, Y g:i A', strtotime($notification['sent_at'])) ?></small>
                </td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <a href="<?= base_url('admin/notifications/show/' . $notification['id']) ?>"
                       class="btn btn-outline-info btn-sm" title="View Details">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?= base_url('admin/notifications/edit/' . $notification['id']) ?>"
                       class="btn btn-outline-warning btn-sm" title="Edit & Resend">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button onclick="deleteNotification(<?= $notification['id'] ?>)"
                            class="btn btn-outline-danger btn-sm" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <h5 class="text-muted mt-3">No notifications sent yet</h5>
        <p class="text-muted">Send your first notification using the form above.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Delete notification function
function deleteNotification(id) {
  if (confirm('Are you sure you want to delete this notification group? This will remove it for all recipients.')) {
    // Create a form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= base_url('admin/notifications/delete/') ?>' + id;

    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '<?= csrf_token() ?>';
    csrfInput.value = '<?= csrf_hash() ?>';
    form.appendChild(csrfInput);

    document.body.appendChild(form);
    form.submit();
  }
}

// Refresh statistics
function refreshStats() {
  fetch('<?= base_url('admin/notifications/getStats') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    document.getElementById('totalSent').textContent = 'Total: ' + data.total_sent;
    document.getElementById('totalRead').textContent = 'Read: ' + data.total_read;
    document.getElementById('totalUnread').textContent = 'Unread: ' + data.total_unread;
  })
  .catch(error => {
    console.error('Error refreshing stats:', error);
  });
}

// Auto-refresh stats every 30 seconds
setInterval(refreshStats, 30000);

// Form validation
document.getElementById('notificationForm').addEventListener('submit', function(e) {
  const target = document.querySelector('select[name="target"]').value;
  const title = document.querySelector('input[name="title"]').value.trim();
  const message = document.querySelector('textarea[name="message"]').value.trim();

  if (!target || !title || !message) {
    e.preventDefault();
    alert('Please fill in all required fields.');
    return false;
  }

  if (title.length > 255) {
    e.preventDefault();
    alert('Title must be 255 characters or less.');
    return false;
  }

  // Confirm before sending
  const userCount = getUserCountEstimate(target);
  if (!confirm(`Are you sure you want to send this notification to ${userCount}?`)) {
    e.preventDefault();
    return false;
  }
});

// Estimate user count for confirmation
function getUserCountEstimate(target) {
  const estimates = {
    'all': 'all users',
    'admin': 'all administrators',
    'teacher': 'all teachers',
    'student': 'all students',
    'parent': 'all parents'
  };
  return estimates[target] || 'selected users';
}

// Initialize stats on page load
document.addEventListener('DOMContentLoaded', function() {
  refreshStats();
});
</script>

<?= $this->endSection() ?>



