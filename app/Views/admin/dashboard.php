<?= $this->extend('dashboard_layout') ?>

<?= $this->section('portal_overlays') ?>
<!-- Outside .main-content so backdrop stacks above fixed sidebar + sticky top bar -->
<div id="createAdminModal" class="custom-modal-overlay create-admin-modal-overlay" style="display: none;">
  <div class="create-admin-shell" role="dialog" aria-modal="true" aria-labelledby="createAdminModalTitle">
    <div class="create-admin-shell-header">
      <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
        <span class="create-admin-modal-header-icon" aria-hidden="true"><i class="bi bi-person-plus"></i></span>
        <div class="min-w-0">
          <h3 id="createAdminModalTitle" class="create-admin-shell-title mb-0">Create admin account</h3>
          <span class="create-admin-shell-subtitle">Add a master admin or restricted admin staff member</span>
        </div>
      </div>
      <button type="button" class="btn btn-sm rounded-circle create-admin-modal-close" onclick="closeCreateAdminModal()" aria-label="Close">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="create-admin-shell-body">
      <form id="createAdminForm">
        <p class="create-admin-section-title"><i class="bi bi-shield-check me-2"></i>Account type</p>
        <div class="row g-2 mb-4 create-admin-type-row">
          <div class="col-sm-6">
            <label class="create-admin-type-card" for="accountTypeMaster">
              <input class="staff-page-cb-sr" type="radio" name="account_type" id="accountTypeMaster" value="master" checked onchange="toggleStaffPagePickers()">
              <span class="create-admin-type-card-inner">
                <i class="bi bi-shield-lock create-admin-type-icon" aria-hidden="true"></i>
                <span class="create-admin-type-title">Master admin</span>
                <span class="create-admin-type-desc">Full access to all admin areas</span>
              </span>
            </label>
          </div>
          <div class="col-sm-6">
            <label class="create-admin-type-card" for="accountTypeStaff">
              <input class="staff-page-cb-sr" type="radio" name="account_type" id="accountTypeStaff" value="staff" onchange="toggleStaffPagePickers()">
              <span class="create-admin-type-card-inner">
                <i class="bi bi-person-badge create-admin-type-icon" aria-hidden="true"></i>
                <span class="create-admin-type-title">Admin staff</span>
                <span class="create-admin-type-desc">Limited to selected pages only</span>
              </span>
            </label>
          </div>
        </div>

        <p class="create-admin-section-title"><i class="bi bi-person-vcard me-2"></i>Account details</p>
        <div class="mb-3">
          <label for="adminEmail" class="form-label small fw-semibold text-secondary">Email</label>
          <div class="input-group">
            <span class="input-group-text create-admin-input-icon"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control" id="adminEmail" name="email" placeholder="name@school.edu" required autocomplete="email">
          </div>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <label for="adminFirstName" class="form-label small fw-semibold text-secondary">First name</label>
            <div class="input-group">
              <span class="input-group-text create-admin-input-icon"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control" id="adminFirstName" name="first_name" placeholder="Given name" required autocomplete="given-name">
            </div>
          </div>
          <div class="col-md-6">
            <label for="adminLastName" class="form-label small fw-semibold text-secondary">Last name</label>
            <div class="input-group">
              <span class="input-group-text create-admin-input-icon"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control" id="adminLastName" name="last_name" placeholder="Family name" required autocomplete="family-name">
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label for="adminPassword" class="form-label small fw-semibold text-secondary">Password</label>
          <div class="input-group">
            <span class="input-group-text create-admin-input-icon"><i class="bi bi-key"></i></span>
            <input type="password" class="form-control" id="adminPassword" name="password" placeholder="Minimum 6 characters" required autocomplete="new-password">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Show password">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
        <div class="mb-3">
          <label for="adminConfirmPassword" class="form-label small fw-semibold text-secondary">Confirm password</label>
          <div class="input-group">
            <span class="input-group-text create-admin-input-icon"><i class="bi bi-key-fill"></i></span>
            <input type="password" class="form-control" id="adminConfirmPassword" name="confirm_password" placeholder="Re-enter password" required autocomplete="new-password">
          </div>
        </div>

        <div id="staffPagesSection" class="create-admin-pages-wrap" style="display: none;">
          <p class="create-admin-section-title mb-2"><i class="bi bi-ui-checks-grid me-2"></i>Pages admin staff can open</p>
          <p class="small text-muted mb-3">Choose at least one area. Staff will not see links or URLs for anything unchecked.</p>
          <div class="create-admin-page-grid">
            <?php foreach (admin_valid_page_keys() as $key): ?>
              <label class="create-admin-page-tile" for="staffpage_<?= esc($key) ?>">
                <input class="staff-page-cb" type="checkbox" name="pages[]" value="<?= esc($key) ?>" id="staffpage_<?= esc($key) ?>">
                <span class="create-admin-page-tile-inner">
                  <i class="bi <?= esc(admin_page_icon($key)) ?> create-admin-page-icon" aria-hidden="true"></i>
                  <span class="create-admin-page-label"><?= esc(admin_page_label($key)) ?></span>
                </span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      </form>
    </div>
    <div class="create-admin-shell-footer">
      <button type="button" class="btn btn-outline-secondary px-4" onclick="closeCreateAdminModal()"><i class="bi bi-x-lg me-2"></i>Cancel</button>
      <button type="button" class="btn btn-primary px-4" onclick="submitCreateAdminForm()"><i class="bi bi-check2-circle me-2"></i>Create account</button>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<script>
function toggleStaffPagePickers() {
  const staff = document.getElementById('accountTypeStaff');
  const section = document.getElementById('staffPagesSection');
  if (!staff || !section) return;
  section.style.display = staff.checked ? 'block' : 'none';
}

function openCreateAdminModal() {
  const modal = document.getElementById('createAdminModal');
  const footer = document.querySelector('.modern-footer');
  
  if (footer) footer.style.display = 'none';
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  const master = document.getElementById('accountTypeMaster');
  if (master) master.checked = true;
  toggleStaffPagePickers();
  document.querySelectorAll('.staff-page-cb').forEach(cb => { cb.checked = false; });
}

function closeCreateAdminModal() {
  const modal = document.getElementById('createAdminModal');
  const footer = document.querySelector('.modern-footer');
  
  modal.style.display = 'none';
  if (footer) footer.style.display = 'block';
  document.body.style.overflow = '';
  
  document.getElementById('createAdminForm').reset();
}

function submitCreateAdminForm() {
  const form = document.getElementById('createAdminForm');
  const password = document.getElementById('adminPassword').value;
  const confirmPassword = document.getElementById('adminConfirmPassword').value;
  const staffRadio = document.getElementById('accountTypeStaff');
  
  if (password !== confirmPassword) {
    showToast('danger', 'Passwords do not match!');
    return;
  }
  if (staffRadio && staffRadio.checked) {
    const any = form.querySelectorAll('.staff-page-cb:checked').length > 0;
    if (!any) {
      showToast('warning', 'Select at least one page for admin staff.');
      return;
    }
  }
  
  const formData = new FormData(form);
  const btn = document.querySelector('.create-admin-shell-footer .btn-primary');
  const originalContent = btn.innerHTML;
  
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Creating...';
  
  fetch('<?= base_url('admin/dashboard/createAdmin') ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast('success', 'Admin account created successfully!');
      setTimeout(() => {
        closeCreateAdminModal();
        location.reload();
      }, 1500);
    } else {
      showToast('danger', 'Error: ' + data.message);
      btn.disabled = false;
      btn.innerHTML = originalContent;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('danger', 'An error occurred while creating the admin account.');
    btn.disabled = false;
    btn.innerHTML = originalContent;
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const togglePassword = document.getElementById('togglePassword');
  if (togglePassword) {
    togglePassword.addEventListener('click', function() {
      const password = document.getElementById('adminPassword');
      const confirmPassword = document.getElementById('adminConfirmPassword');
      const icon = this.querySelector('i');
      
      if (password.type === 'password') {
        password.type = 'text';
        confirmPassword.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        password.type = 'password';
        confirmPassword.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  }
});
</script>

<div class="admin-dashboard-home">
<div class="dashboard-header admin-dash-header mb-3">
  <div class="d-flex align-items-center gap-3">
    <div class="dash-page-icon" aria-hidden="true"><i class="bi bi-speedometer2"></i></div>
    <div>
      <h2 class="mb-0" style="font-size:1.25rem;">Admin Dashboard</h2>
      <small class="text-muted">Overview and quick actions</small>
    </div>
  </div>
  <div class="admin-dash-actions d-flex flex-wrap gap-2">
    <div class="btn-group flex-wrap admin-dash-actions-nav">
  <a href="<?= base_url('admin/students') ?>" class="btn btn-sm btn-outline-primary compact-md-hide-text"><i class="bi bi-people-fill me-2"></i>Students</a>
  <a href="<?= base_url('admin/teachers') ?>" class="btn btn-sm btn-outline-primary compact-md-hide-text"><i class="bi bi-person-video3 me-2"></i>Teachers</a>
  <a href="<?= base_url('admin/sections') ?>" class="btn btn-sm btn-outline-primary compact-md-hide-text"><i class="bi bi-grid-3x3-gap me-2"></i>Subjects & Sections</a>
  <a href="<?= base_url('announcements/admin') ?>" class="btn btn-sm btn-outline-primary compact-md-hide-text"><i class="bi bi-megaphone me-2"></i>Announcements</a>
  <a href="<?= base_url('admin/students/pending') ?>" class="btn btn-sm btn-outline-primary compact-md-hide-text"><i class="bi bi-clock-history me-2"></i>Pending Applications</a>
  <a href="<?= base_url('admin/analytics') ?>" class="btn btn-sm btn-outline-primary compact-md-hide-text"><i class="bi bi-graph-up me-2"></i>Analytics</a>
    </div>
    
    <?php if (function_exists('is_master_admin') && is_master_admin()): ?>
    <button type="button" class="btn btn-sm btn-warning" onclick="openCreateAdminModal()" id="createAdminBtn">
      <i class="bi bi-person-plus me-1"></i>Create admin
    </button>
    <?php endif; ?>
    
    <button type="button" class="btn btn-sm <?= ($registrationEnabled ?? true) ? 'btn-success' : 'btn-danger' ?>" onclick="toggleEnrollment()" id="enrollmentToggleBtn">
      <i class="bi <?= ($registrationEnabled ?? true) ? 'bi-unlock' : 'bi-lock' ?> me-1"></i><?= ($registrationEnabled ?? true) ? 'Registration Open' : 'Registration Closed' ?>
    </button>
    
    <button type="button" class="btn btn-sm <?= ($gradingEnabled ?? true) ? 'btn-success' : 'btn-danger' ?>" onclick="toggleGrading()" id="gradingToggleBtn">
      <i class="bi <?= ($gradingEnabled ?? true) ? 'bi-pencil-square' : 'bi-lock-fill' ?> me-1"></i><?= ($gradingEnabled ?? true) ? 'Grading Open' : 'Grading Closed' ?>
    </button>
  </div>
</div>

<?php /* Removed feature tiles grid as requested */ ?>

<!-- Top Row: 2 Charts -->
<div class="admin-dash-grid admin-dash-grid--top">
  <div class="admin-dash-widget admin-dash-widget--enrollment">
    <div class="card h-100 admin-dash-card">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title mb-0 fw-semibold">Enrolled Students</h6>
            <small class="text-muted">Kindergarten – Grade 6 (selected school year)</small>
          </div>
          <div class="chart-controls">
            <label for="yearFilter" class="small text-muted me-2">School Year:</label>
            <select id="yearFilter" class="form-select form-select-sm" style="width: auto;" onchange="updateEnrollmentChart()">
              <?php foreach ($availableYears as $year): ?>
                <option value="<?= $year ?>" <?= $year == $selectedYear ? 'selected' : '' ?>><?= $year ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <div class="card-body py-2">
        <div class="text-center mb-2">
          <div class="admin-dash-total-value text-primary" id="totalEnrollment">0</div>
          <small class="text-muted">Total Students</small>
        </div>
        <div class="chart-container admin-dash-chart admin-dash-chart--sm">
          <canvas id="enrollmentChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="admin-dash-widget admin-dash-widget--stats">
    <div class="card h-100 admin-dash-card">
      <div class="card-header py-2">
        <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-grid-1x2 me-1"></i> Quick stats</h6>
        <small class="text-muted">Snapshot</small>
      </div>
      <div class="card-body py-2 h-100">
        <div class="admin-dash-stat-grid">
          <div class="admin-dash-stat-cell">
            <div class="p-3 rounded-3 border text-center flex-fill">
              <div class="text-warning mb-1">
                <i class="bi bi-hourglass-split fs-3"></i>
              </div>
              <div class="fw-bold fs-1 text-dark" style="line-height: 1;">
                <?= (int)($pending_enrollments ?? 0) ?>
              </div>
              <small class="text-muted d-block">Pending applications</small>
            </div>
          </div>

          <div class="admin-dash-stat-cell">
            <div class="p-3 rounded-3 border text-center flex-fill">
              <div class="text-primary mb-1">
                <i class="bi bi-unlock fs-3"></i>
              </div>
              <span class="badge fs-5 px-3 py-2 <?= ($registrationEnabled ?? true) ? 'bg-success' : 'bg-danger' ?>">
                <?= ($registrationEnabled ?? true) ? 'Registration open' : 'Registration closed' ?>
              </span>
              <small class="text-muted d-block mt-1">Enrollment setting</small>
            </div>
          </div>

          <div class="admin-dash-stat-cell">
            <div class="p-3 rounded-3 border text-center flex-fill">
              <div class="text-info mb-1">
                <i class="bi bi-pencil-square fs-3"></i>
              </div>
              <span class="badge fs-5 px-3 py-2 <?= ($gradingEnabled ?? true) ? 'bg-success' : 'bg-secondary' ?>">
                <?= ($gradingEnabled ?? true) ? 'Grading enabled' : 'Grading disabled' ?>
              </span>
              <small class="text-muted d-block mt-1">Grade setting</small>
            </div>
          </div>

          <div class="admin-dash-stat-cell">
            <div class="p-3 rounded-3 border text-center flex-fill">
              <div class="text-secondary mb-1">
                <i class="bi bi-calendar-check fs-3"></i>
              </div>
              <div class="fw-bold fs-1 text-dark" style="line-height: 1;">
                <?= (int)($currentTerm ?? 0) ?>
              </div>
              <small class="text-muted d-block">Current term</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bottom Row -->
<div class="admin-dash-grid admin-dash-grid--bottom">
  <div class="admin-dash-widget">
    <div class="card h-100 admin-dash-card">
      <div class="card-header py-2">
        <h6 class="card-title mb-0 fw-semibold">Recent Enrollment Applications</h6>
      </div>
      <div class="card-body py-2 admin-dash-scroll">
        <?php if (!empty($recentEnrollments)): ?>
          <div class="list-group list-group-flush">
            <?php foreach (array_slice($recentEnrollments, 0, 5) as $enrollment): ?>
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1">
                    <div class="fw-semibold text-dark mb-1" style="font-size: 0.85rem;">
                      <?= esc(($enrollment['last_name'] ?? '') . ', ' . ($enrollment['first_name'] ?? '')) ?>
                    </div>
                    <div class="text-muted small"><?= esc(grade_level_label((int) ($enrollment['grade_level'] ?? 0))) ?></div>
                  </div>
                  <?php 
                  $statusClass = match($enrollment['enrollment_status']) {
                    'enrolled' => 'bg-success-subtle text-success-emphasis',
                    'pending' => 'bg-warning-subtle text-warning-emphasis', 
                    'rejected' => 'bg-danger-subtle text-danger-emphasis',
                    'approved' => 'bg-info-subtle text-info-emphasis',
                    default => 'bg-secondary-subtle text-secondary-emphasis'
                  };
                  ?>
                  <span class="badge <?= $statusClass ?> ms-2 small"><?= ucfirst($enrollment['enrollment_status']) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-person-check text-muted fs-1 mb-2"></i>
            <p class="text-muted mb-2">No recent enrollment applications.</p>
            <p class="small text-muted mb-3">New student registrations will appear here for review.</p>
            <a href="<?= base_url('admin/students/pending') ?>" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-clock-history me-1"></i> View Pending Applications
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="admin-dash-widget">
    <div class="card h-100 admin-dash-card">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">Enrollment by Grade Level</h6>
          <small class="text-muted">Kindergarten – Grade 6</small>
        </div>
      </div>
      <div class="card-body py-2">
        <div class="chart-container admin-dash-chart admin-dash-chart--md">
          <canvas id="gradeChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="admin-dash-widget">
    <div class="card h-100 admin-dash-card">
      <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-semibold">Recent Announcements</h6>
          <a href="<?= base_url('admin/announcements') ?>" class="btn btn-sm btn-outline-primary px-2 py-1">
            <i class="bi bi-gear"></i> Manage
          </a>
        </div>
      </div>
      <div class="card-body py-2 admin-dash-scroll">
        <?php if (!empty($recentAnnouncements)): ?>
          <div class="list-group list-group-flush">
            <?php foreach (array_slice($recentAnnouncements, 0, 4) as $announcement): ?>
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                  <h6 class="mb-1 fw-semibold text-dark" style="font-size: 0.85rem;"><?= esc($announcement['title']) ?></h6>
                  <small class="text-muted ms-2"><?= $announcement['created_at'] ? date('M j, Y', strtotime($announcement['created_at'])) : 'N/A' ?></small>
                </div>
                <p class="mb-1 small text-muted"><?= esc(substr(strip_tags($announcement['body']), 0, 70)) ?>...</p>
                <small class="text-primary">Target: <?= esc($announcement['target_roles']) ?></small>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-4">
            <i class="bi bi-megaphone text-muted fs-1 mb-2"></i>
            <p class="text-muted mb-2">No recent announcements.</p>
            <p class="small text-muted mb-3">Create announcements to communicate with students, teachers, and parents.</p>
            <a href="<?= base_url('admin/announcements') ?>" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-plus-circle me-1"></i> Create Announcement
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php if (! empty($adminStaffList) && function_exists('is_master_admin') && is_master_admin()): ?>
<div class="card border-0 shadow-sm mt-4">
  <div class="card-header py-2 d-flex justify-content-between align-items-center">
    <h6 class="mb-0 fw-semibold"><i class="bi bi-people me-2"></i>Admin staff</h6>
    <small class="text-muted">Page access for restricted admin accounts</small>
  </div>
  <div class="card-body py-2">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead><tr><th>Email</th><th>Name</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($adminStaffList as $row): ?>
            <tr>
              <td><?= esc($row['email'] ?? '') ?></td>
              <td><?= esc(trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))) ?></td>
              <td class="text-end">
                <a href="<?= base_url('admin/dashboard/staff-permissions/' . (int) ($row['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-primary">Edit pages</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

</div>

<style>
/* Clean modal styling */
.modal-xl {
  max-width: 1200px;
}

.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}

.announcement-item {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  background: #f8f9fa;
  transition: all 0.2s ease;
}

.announcement-item:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transform: translateY(-1px);
}

.announcement-title {
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.5rem;
}

.announcement-body {
  color: #6c757d;
  margin-bottom: 0.75rem;
  line-height: 1.5;
}

.announcement-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
  color: #6c757d;
}

.target-badge {
  background: #e9ecef;
  color: #495057;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Custom Modal Styles */
.custom-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
}

.custom-modal-container {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
  z-index: 100001;
  pointer-events: auto;
}

.custom-modal-header {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: #ffffff !important;
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 3px solid #1e40af;
}

.custom-modal-header * {
  color: #ffffff !important;
}

.custom-modal-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #ffffff !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.custom-modal-title, .custom-modal-title * {
  color: #ffffff !important;
}

.custom-modal-close {
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: #ffffff !important;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1.2rem;
}

.custom-modal-close:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  color: #ffffff !important;
  transform: scale(1.1);
}

.custom-modal-close i {
  color: #ffffff !important;
}

.custom-modal-body {
  padding: 2rem;
  max-height: 60vh;
  overflow-y: auto;
  background: #f8fafc;
  position: relative;
  z-index: 100001;
}

.custom-modal-body input,
.custom-modal-body select,
.custom-modal-body textarea,
.custom-modal-body button {
  position: relative;
  z-index: 100002;
  pointer-events: auto;
}

.custom-modal-footer {
  background: #f1f5f9;
  padding: 1.5rem 2rem;
  border-top: 2px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
</style>

<!-- Chart.js for enrollment charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
<script>
let enrollmentChartInstance;

// Enrollment data by grade level (Kinder – Grade 6)
const enrollmentByGrade = <?= json_encode($enrollmentChartValues ?? []) ?>;
const gradeChartLabels = <?= json_encode($enrollmentChartLabels ?? []) ?>;

function initializeCharts() {
  // Enrollment Pie Chart
  const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
  const totalEnrollment = enrollmentByGrade.reduce((a, b) => a + b, 0);
  document.getElementById('totalEnrollment').textContent = totalEnrollment;
  
  enrollmentChartInstance = new Chart(enrollmentCtx, {
    type: 'pie',
    data: {
      labels: gradeChartLabels,
      datasets: [{
        data: enrollmentByGrade,
        backgroundColor: ['#7c3aed', '#1e3a8a', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#dbeafe'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: { padding: 10, font: { size: 11 } }
        },
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.label}: ${ctx.raw} students`
          }
        }
      }
    }
  });
}

// Initialize Grade Level Chart
function initializeGradeChart() {
  const gradeCtx = document.getElementById('gradeChart').getContext('2d');
  const colorPrimary = '#3b82f6';
  const colorHeading = '#0f172a';
  
  const gradient = gradeCtx.createLinearGradient(0, 0, 0, 300);
  gradient.addColorStop(0, 'rgba(59, 130, 246, 0.9)');
  gradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

  new Chart(gradeCtx, {
    type: 'bar',
    data: {
      labels: gradeChartLabels,
      datasets: [{
        label: 'Enrolled Students',
        data: enrollmentByGrade,
        backgroundColor: gradient,
        borderColor: colorPrimary,
        borderWidth: 1,
        borderRadius: 8,
        maxBarThickness: 40,
        categoryPercentage: 0.6,
        barPercentage: 0.7,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1, color: colorHeading },
          grid: { color: 'rgba(15,23,42,0.06)' }
        },
        x: {
          ticks: { color: colorHeading },
          grid: { display: false }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => ` ${ctx.raw} students`
          }
        }
      }
    }
  });
}

// Update enrollment chart when year filter changes
function updateEnrollmentChart() {
  const selectedYear = document.getElementById('yearFilter').value;
  const url = new URL(window.location);
  url.searchParams.set('year', selectedYear);
  
  // Show loading state
  const btn = document.getElementById('yearFilter');
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Loading...';
  
  setTimeout(() => {
    window.location.href = url.toString();
  }, 300);
}

// Initialize all charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeCharts();
  initializeGradeChart();
  
  const yearFilter = document.getElementById('yearFilter');
  if (yearFilter) {
    yearFilter.value = '<?= $selectedYear ?>';
  }
});



function formatDateTime(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

function toggleEnrollment() {
  const btn = document.getElementById('enrollmentToggleBtn');
  const originalContent = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Updating...';
  
  fetch('<?= base_url('admin/dashboard/toggleEnrollment') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast('success', 'Registration ' + (data.enabled ? 'opened' : 'closed') + ' successfully');
      setTimeout(() => location.reload(), 1500);
    } else {
      showToast('danger', 'Failed to toggle registration: ' + data.message);
      btn.disabled = false;
      btn.innerHTML = originalContent;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('danger', 'An error occurred while toggling registration.');
    btn.disabled = false;
    btn.innerHTML = originalContent;
  });
}

function toggleGrading() {
  const btn = document.getElementById('gradingToggleBtn');
  const originalContent = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Updating...';
  
  fetch('<?= base_url('admin/dashboard/toggleGrading') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast('success', 'Grading ' + (data.enabled ? 'enabled' : 'disabled') + ' successfully');
      setTimeout(() => location.reload(), 1500);
    } else {
      showToast('danger', 'Failed to toggle grading: ' + data.message);
      btn.disabled = false;
      btn.innerHTML = originalContent;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('danger', 'An error occurred while toggling grading.');
    btn.disabled = false;
    btn.innerHTML = originalContent;
  });
}

function showToast(type, message) {
  const toastId = 'toast-' + Date.now();
  const toastHtml = `
    <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 100002; min-width: 300px;">
      <div class="d-flex">
        <div class="toast-body">
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `;
  
  document.body.insertAdjacentHTML('beforeend', toastHtml);
  const toastElement = document.getElementById(toastId);
  const toast = new bootstrap.Toast(toastElement, { delay: 3000, autohide: true });
  toast.show();
  
  toastElement.addEventListener('hidden.bs.toast', () => {
    toastElement.remove();
  });
}

// Set the selected year in the filter on page load
document.addEventListener('DOMContentLoaded', function() {
  const yearFilter = document.getElementById('yearFilter');
  if (yearFilter) {
    yearFilter.value = '<?= $selectedYear ?>';
  }
});

</script>


<?= $this->endSection() ?>