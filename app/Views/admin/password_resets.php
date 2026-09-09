<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">Password Reset Requests</h1>
      <p class="text-muted mb-0 small">Manage password reset requests from teachers and students</p>
    </div>
  </div>
  <div class="blue-divider"></div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="card bg-white border-0 shadow-sm rounded-3">
  <div class="card-body p-0">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
      <div>
        <input type="checkbox" id="selectAll" class="form-check-input me-2" onchange="toggleSelectAll()">
        <label for="selectAll" class="form-check-label">Select All</label>
      </div>
      <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" onclick="bulkDelete()" disabled>
        <i class="bi bi-trash me-1"></i>Delete Selected (<span id="selectedCount">0</span>)
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th class="border-0 fw-medium" style="width: 40px;"></th>
            <th class="border-0 fw-medium">User</th>
            <th class="border-0 fw-medium">Identifier</th>
            <th class="border-0 fw-medium">Requested</th>
            <th class="border-0 fw-medium">Status</th>
            <th class="border-0 fw-medium text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($requests)): ?>
            <?php foreach ($requests as $reset): ?>
              <tr>
                <td class="py-3">
                  <input type="checkbox" class="form-check-input reset-checkbox" value="<?= $reset['id'] ?>" onchange="updateSelectedCount()">
                </td>
                <td class="py-3">
                  <div class="fw-medium"><?= esc($reset['user_email'] ?? $reset['email']) ?></div>
                  <?php if (!empty($reset['first_name']) && !empty($reset['last_name'])): ?>
                    <small class="text-muted d-block"><?= esc($reset['first_name'] . ' ' . $reset['last_name']) ?></small>
                  <?php endif; ?>
                </td>
                <td class="py-3">
                  <span class="badge bg-light text-dark"><?= esc($reset['student_id'] ?? $reset['lrn'] ?? 'N/A') ?></span>
                </td>
                <td class="py-3">
                  <?php 
                    $date = new DateTime($reset['created_at']);
                    $date->setTimezone(new DateTimeZone('Asia/Manila'));
                  ?>
                  <small class="text-muted"><?= $date->format('M j, Y g:i A') ?></small>
                </td>
                <td class="py-3">
                  <?php 
                    $statusClass = match($reset['status']) {
                      'pending' => 'bg-warning',
                      'approved' => 'bg-success',
                      'rejected' => 'bg-danger',
                      'used' => 'bg-info',
                      'expired' => 'bg-secondary',
                      default => 'bg-secondary'
                    };
                  ?>
                  <span class="badge <?= $statusClass ?>"><?= ucfirst($reset['status']) ?></span>
                </td>
                <td class="py-3 text-center">
                  <?php if ($reset['status'] === 'pending'): ?>
                    <button class="btn btn-success btn-sm" onclick="approveReset(<?= $reset['id'] ?>)">
                      <i class="bi bi-check-circle me-1"></i>Approve
                    </button>
                    <button class="btn btn-danger btn-sm ms-1" onclick="rejectReset(<?= $reset['id'] ?>)">
                      <i class="bi bi-x-circle me-1"></i>Reject
                    </button>
                  <?php elseif ($reset['status'] === 'approved'): ?>
                    <a href="<?= base_url('admin/password-resets/change/' . $reset['id']) ?>" class="btn btn-primary btn-sm">
                      <i class="bi bi-key me-1"></i>Change Password
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No pending password reset requests
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
.custom-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}
.custom-modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}
.custom-modal-header {
  padding: 20px;
  border-bottom: 1px solid #dee2e6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.custom-modal-body {
  padding: 20px;
  background: #fef9e7;
  text-align: center;
}
.custom-modal-footer {
  padding: 15px 20px;
  display: flex;
  justify-content: center;
  gap: 10px;
  border-top: 1px solid #dee2e6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let currentResetId = null;
  let csrfHash = '<?= csrf_hash() ?>';

  function csrfHeaders() {
    return {
      'X-Requested-With': 'XMLHttpRequest'
    };
  }

  function csrfBody() {
    return new URLSearchParams({ '<?= csrf_token() ?>': csrfHash });
  }

  function applyCsrfRefresh(data) {
    if (data && typeof data.csrf_hash === 'string' && data.csrf_hash !== '') {
      csrfHash = data.csrf_hash;
    }
  }

  window.toggleSelectAll = function() {
  const selectAll = document.getElementById('selectAll');
  const checkboxes = document.querySelectorAll('.reset-checkbox');
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
  };

  window.updateSelectedCount = function() {
  const checkboxes = document.querySelectorAll('.reset-checkbox:checked');
  const count = checkboxes.length;
  document.getElementById('selectedCount').textContent = count;
  document.getElementById('bulkDeleteBtn').disabled = count === 0;
  
  const selectAll = document.getElementById('selectAll');
  const allCheckboxes = document.querySelectorAll('.reset-checkbox');
    selectAll.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
  };

  window.bulkDelete = function() {
  const checkboxes = document.querySelectorAll('.reset-checkbox:checked');
  const ids = Array.from(checkboxes).map(cb => cb.value);
  
  if (ids.length === 0) {
    alert('Please select at least one request to delete');
    return;
  }
  
  createModal('Confirm Bulk Delete', `Delete ${ids.length} password reset request(s)? This will only remove the request records, not undo any password changes.`, 'Confirm', () => {
    (async () => {
      let successCount = 0;
      for (const id of ids) {
        try {
          const response = await fetch(`<?= base_url('admin/password-resets/delete/') ?>${id}`, {
            method: 'POST',
            headers: csrfHeaders(),
            body: csrfBody()
          });
          const data = await response.json();
          applyCsrfRefresh(data);
          if (data.success) {
            successCount++;
          }
        } catch (error) {
          console.error('Error deleting request ' + id + ':', error);
        }
      }
      if (successCount > 0) {
        location.reload();
      } else {
        alert('Failed to delete requests');
      }
    })();
  });
  };

  window.createModal = function(title, message, confirmText, onConfirm) {
  const modalHtml = `
    <div class="custom-modal-backdrop" id="customModal">
      <div class="custom-modal-content">
        <div class="custom-modal-header">
          <h5 class="mb-0">${title}</h5>
          <button type="button" class="btn-close" onclick="closeCustomModal()"></button>
        </div>
        <div class="custom-modal-body">
          <p class="mb-0">${message}</p>
        </div>
        <div class="custom-modal-footer">
          <button type="button" class="btn btn-dark" onclick="closeCustomModal()">Cancel</button>
          <button type="button" class="btn btn-warning" onclick="confirmCustomModal()">${confirmText}</button>
        </div>
      </div>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHtml);
    window.customModalCallback = onConfirm;
  };

  window.closeCustomModal = function() {
  const modal = document.getElementById('customModal');
  if (modal) modal.remove();
    window.customModalCallback = null;
  };

  window.confirmCustomModal = function() {
  if (window.customModalCallback) {
    window.customModalCallback();
  }
    closeCustomModal();
  };

  window.approveReset = function(resetId) {
  currentResetId = resetId;
  createModal('Confirm Action', 'Approve this password reset request?', 'Confirm', () => {
    fetch(`<?= base_url('admin/password-resets/approve/') ?>${currentResetId}`, {
    method: 'POST',
    headers: csrfHeaders(),
    body: csrfBody()
    })
    .then(response => response.json())
    .then(data => {
      applyCsrfRefresh(data);
      if (data.success) {
        if (data.redirect) {
          window.location.href = data.redirect;
        } else {
          location.reload();
        }
      } else {
        alert('Error: ' + (data.error || 'Failed to approve request'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while approving the request');
    });
  });
  };

  window.rejectReset = function(resetId) {
  currentResetId = resetId;
  createModal('Confirm Action', 'Reject this password reset request?', 'Confirm', () => {
    fetch(`<?= base_url('admin/password-resets/reject/') ?>${currentResetId}`, {
    method: 'POST',
    headers: csrfHeaders(),
    body: csrfBody()
    })
    .then(response => response.json())
    .then(data => {
      applyCsrfRefresh(data);
      if (data.success) {
        location.reload();
      } else {
        alert('Error: ' + (data.error || 'Failed to reject request'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while rejecting the request');
    });
  });
  };

  window.deleteReset = function(resetId) {
  currentResetId = resetId;
  createModal('Confirm Action', 'Delete this password reset request? This will only remove the request record, not undo any password changes.', 'Confirm', () => {
    fetch(`<?= base_url('admin/password-resets/delete/') ?>${currentResetId}`, {
    method: 'POST',
    headers: csrfHeaders(),
    body: csrfBody()
    })
    .then(response => response.json())
    .then(data => {
      applyCsrfRefresh(data);
      if (data.success) {
        location.reload();
      } else {
        alert('Error: ' + (data.error || 'Failed to delete request'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while deleting the request');
    });
  });
  };
});
</script>

<?= $this->endSection() ?>