<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-warning"></i> 
                        New Student Applications
                        <span class="badge bg-warning text-dark ms-2"><?= count($pendingStudents) ?></span>
                    </h5>
                    <div>
                        <a href="<?= base_url('admin/students/pending/history') ?>" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-clock-history"></i> History
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingStudents)): ?>
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">No New Student Applications</h5>
                            <p class="text-muted mb-0">All new student applications have been processed.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Grade Level</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingStudents as $student): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <?= strtoupper(substr($student['first_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></strong>
                                                        <?php if ($student['middle_name']): ?>
                                                            <br><small class="text-muted"><?= esc($student['middle_name']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= esc($student['email']) ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= esc(grade_level_label((int) ($student['grade_level'] ?? 0))) ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $createdDate = new DateTime($student['created_at']);
                                                $createdDate->setTimezone(new DateTimeZone('Asia/Manila'));
                                                ?>
                                                <?= $createdDate->format('M j, Y') ?>
                                                <br><small class="text-muted"><?= $createdDate->format('g:i A') ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            onclick="approveStudent(<?= $student['id'] ?>, '<?= esc($student['first_name'] . ' ' . $student['last_name']) ?>')">
                                                        <i class="bi bi-check-lg"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="rejectStudent(<?= $student['id'] ?>, '<?= esc($student['first_name'] . ' ' . $student['last_name']) ?>')">
                                                        <i class="bi bi-x-lg"></i> Reject
                                                    </button>
                                                    <button type="button" class="btn btn-outline-info btn-sm" 
                                                            onclick="viewStudentDetails(<?= $student['id'] ?>)">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
function getDashboardModalPortal() {
    return document.getElementById('dashboard-modal-portal') || document.body;
}

function createPendingModal(id, titleClass, title, bodyHtml, confirmButtonId, confirmButtonClass, confirmButtonText) {
    const existing = document.getElementById(id);
    if (existing) existing.remove();

    const modalDiv = document.createElement('div');
    modalDiv.className = 'modal fade';
    modalDiv.id = id;
    modalDiv.setAttribute('tabindex', '-1');
    modalDiv.setAttribute('aria-hidden', 'true');

    modalDiv.innerHTML = `
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header ${titleClass}">
            <h5 class="modal-title">${title}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            ${bodyHtml}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn" style="background-color: #6c757d; color: white;" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn ${confirmButtonClass}" id="${confirmButtonId}">${confirmButtonText}</button>
          </div>
        </div>
      </div>`;

    getDashboardModalPortal().appendChild(modalDiv);

    modalDiv.addEventListener('hidden.bs.modal', function () {
        modalDiv.remove();
        document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
            backdrop.remove();
        });
    }, { once: true });

    return modalDiv;
}

function approveStudent(studentId, studentName) {
    const modalDiv = createPendingModal(
        'approveModal',
        'bg-success text-white',
        'Confirm Approval',
        `
            <p>Are you sure you want to APPROVE <strong>${studentName}</strong>'s application?</p>
            <p class="mb-2"><strong>This will:</strong></p>
            <ul class="mb-0">
              <li>Activate their account</li>
              <li>Send them login credentials via email</li>
              <li>Allow them to access the student portal</li>
            </ul>
        `,
        'confirmApproveBtn',
        'btn-success',
        'Confirm'
    );

    const modal = new bootstrap.Modal(modalDiv, { backdrop: 'static', keyboard: true, focus: true });
    modal.show();

    const confirmBtn = document.getElementById('confirmApproveBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            confirmApproval(studentId);
        }, { once: true });
    }
}

function confirmApproval(studentId) {
    const modalEl = document.getElementById('approveModal');
    if (!modalEl) return;
    
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    // Show loading state
    showAlert('info', 'Processing approval...');
    
    fetch(`<?= base_url('admin/students/approve/') ?>${studentId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try { return JSON.parse(text); } catch(e) { return { error: 'Server returned status ' + response.status + '. Please try again.' }; }
            });
        }
        return response.json();
    })
    .then(data => {
        // Remove loading alert
        dismissAlerts();
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 2500);
        } else {
            showAlert('error', data.error || 'Failed to approve student');
        }
    })
    .catch(error => {
        dismissAlerts();
        showAlert('error', 'Network error occurred. Please check your connection and try again.');
    });
}

function rejectStudent(studentId, studentName) {
    const modalDiv = createPendingModal(
        'rejectStudentModal',
        'bg-danger text-white',
        'Reject Application',
        `
            <p>Are you sure you want to REJECT <strong>${studentName}</strong>'s application?</p>
            <div class="alert alert-warning mb-0">
              <i class="bi bi-exclamation-triangle"></i> This action cannot be undone.
            </div>
        `,
        'confirmRejectStudentBtn',
        'btn-danger',
        'Confirm Rejection'
    );

    const modal = new bootstrap.Modal(modalDiv, { backdrop: 'static', keyboard: true, focus: true });
    modal.show();

    const confirmBtn = document.getElementById('confirmRejectStudentBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            confirmRejectStudent(studentId);
        }, { once: true });
    }
}

function confirmRejectStudent(studentId) {
    const modalEl = document.getElementById('rejectStudentModal');
    if (!modalEl) return;
    
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    // Show loading state
    showAlert('info', 'Processing rejection...');
    
    fetch(`<?= base_url('admin/students/reject/') ?>${studentId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try { return JSON.parse(text); } catch(e) { return { error: 'Server returned status ' + response.status + '. Please try again.' }; }
            });
        }
        return response.json();
    })
    .then(data => {
        dismissAlerts();
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.error || 'Failed to reject student');
        }
    })
    .catch(error => {
        dismissAlerts();
        showAlert('error', 'Network error occurred. Please check your connection and try again.');
    });
}

function viewStudentDetails(studentId) {
    window.location.href = `<?= base_url('admin/students/view/') ?>${studentId}`;
}

function dismissAlerts() {
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.querySelectorAll('.alert').forEach(a => a.remove());
    }
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : (type === 'info' ? 'alert-info' : 'alert-danger');
    const formattedMessage = message.replace(/\n/g, '<br>');
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${formattedMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 8 seconds for success messages with credentials
    const dismissTime = message.includes('Login Credentials') ? 8000 : 5000;
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, dismissTime);
}

</script>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: bold;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}
</style>

<?= $this->endSection() ?>
