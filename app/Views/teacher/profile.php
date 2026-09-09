<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
.blue-divider {
  height: 3px;
  background: linear-gradient(90deg, #007bff, #0056b3);
  border-radius: 2px;
  margin: 1rem 0;
}

.profile-card {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.profile-header {
  background: linear-gradient(135deg, #007bff, #0056b3);
  color: white !important;
  padding: 2rem;
  text-align: center;
}

.profile-header h3,
.profile-header p {
  color: white !important;
}

.profile-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  font-size: 2.5rem;
  font-weight: bold;
}

.form-section {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-section h5 {
  color: #007bff;
  margin-bottom: 1rem;
  font-weight: 600;
}

.teacher-info-section { margin-bottom: 1.5rem; }
.teacher-info-title {
  font-size: 1rem; font-weight: 600; color: #007bff; margin-bottom: 0.75rem;
  padding-bottom: 0.35rem; border-bottom: 1px solid #dee2e6;
}
.teacher-info-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.75rem;
}
.teacher-info-label {
  font-size: 0.75rem; font-weight: 600; color: #6c757d; text-transform: uppercase;
}
.teacher-info-value { font-size: 0.95rem; color: #212529; }
.teacher-info-value.empty { color: #adb5bd; font-style: italic; }
.status-badge {
  display: inline-block; padding: 0.25rem 0.5rem; border-radius: 0.25rem;
  font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
}
.status-active { background: #d1e7dd; color: #0f5132; }
.status-inactive, .status-terminated { background: #f8d7da; color: #842029; }
.status-on_leave, .status-resigned { background: #fff3cd; color: #664d03; }
</style>

<!-- Header Section -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">My Profile</h1>
      <p class="text-muted mb-0 small">Manage your personal information and account settings</p>
    </div>
  </div>
  
  <!-- Blue Divider -->
  <div class="blue-divider"></div>
</div>

<?php if ($success = session('success')): ?>
  <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>
<?php if ($error = session('error')): ?>
  <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endif; ?>
<?php if ($errors = session('errors')): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?= esc($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php
// Profile completion reminder
$missingFields = [];
if (empty($teacher['middle_name'])) $missingFields[] = 'Middle Name';
if (empty($teacher['gender'])) $missingFields[] = 'Gender';
if (empty($teacher['date_of_birth'])) $missingFields[] = 'Date of Birth';
if (empty($teacher['contact_number'])) $missingFields[] = 'Contact Number';
if (empty($teacher['address'])) $missingFields[] = 'Address';
if (empty($teacher['license_number'])) $missingFields[] = 'License Number';
if (empty($teacher['position'])) $missingFields[] = 'Position';
if (empty($teacher['designation'])) $missingFields[] = 'Designation';

if (!empty($missingFields)):
?>
<div class="alert alert-info border-0 shadow-sm" role="alert">
  <div class="d-flex align-items-start gap-3">
    <i class="bi bi-info-circle-fill fs-4 flex-shrink-0 mt-1"></i>
    <div class="flex-grow-1">
      <strong>Complete Your Profile</strong>
      <p class="mb-2 small">Help us get to know you better! These fields are optional but will help us improve your experience:</p>
      <div class="d-flex flex-wrap gap-2">
        <?php foreach ($missingFields as $field): ?>
          <span class="badge bg-light text-dark border"><?= esc($field) ?></span>
        <?php endforeach; ?>
      </div>
      <p class="mb-0 mt-2 small text-muted">
        <i class="bi bi-pencil-square me-1"></i>
        You can update these anytime by scrolling down to the Personal Information and Contact Information sections below.
      </p>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="row">
  <!-- Profile Information -->
  <div class="col-lg-8">
    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-avatar">
          <?= strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)) ?>
        </div>
        <h3 class="mb-1" style="color: white;"><?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></h3>
        <p class="mb-0" style="color: white; opacity: 0.9;"><?= esc($teacher['email']) ?></p>
      </div>
      
      <div class="card-body p-4">
        <form action="<?= base_url('teacher/profile/update') ?>" method="post" id="profileUpdateForm">
          <?= csrf_field() ?>
          
          <div class="form-section">
            <h5><i class="bi bi-person me-2"></i>Personal Information</h5>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       value="<?= esc($teacher['first_name']) ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="middle_name" class="form-label">Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" 
                       value="<?= esc($teacher['middle_name']) ?>">
              </div>
              <div class="col-md-4 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       value="<?= esc($teacher['last_name']) ?>" required>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h5><i class="bi bi-envelope me-2"></i>Contact Information</h5>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= esc($teacher['email']) ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="contact_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number" 
                       value="<?= esc($teacher['contact_number'] ?? '') ?>">
              </div>
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" id="address" name="address" rows="2"><?= esc($teacher['address'] ?? '') ?></textarea>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle me-2"></i>Update Profile
            </button>
            <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Change Password -->
  <div class="col-lg-4">
    <div class="profile-card">
      <div class="card-header bg-warning text-dark">
        <h5 class="card-title mb-0">
          <i class="bi bi-shield-lock me-2"></i>Change Password
        </h5>
      </div>
      <div class="card-body">
        <form id="changePasswordForm" action="<?= base_url('teacher/profile/change-password') ?>" method="post">
          <?= csrf_field() ?>
          
          <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="current_password" name="current_password" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                <i class="bi bi-eye" id="current_password_icon"></i>
              </button>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="new_password" name="new_password" 
                     minlength="8" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                <i class="bi bi-eye" id="new_password_icon"></i>
              </button>
            </div>
            <div class="form-text">Minimum 8 characters</div>
          </div>
          
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                <i class="bi bi-eye" id="confirm_password_icon"></i>
              </button>
            </div>
          </div>
          
          <button type="button" class="btn btn-warning w-100" onclick="confirmPasswordChange()">
            <i class="bi bi-key me-2"></i>Change Password
          </button>
        </form>
      </div>
    </div>

    <!-- Profile Stats -->
    <div class="profile-card mt-3">
      <div class="card-header bg-info text-white">
        <h5 class="card-title mb-0">
          <i class="bi bi-graph-up me-2"></i>Quick Stats
        </h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">Employee No.</span>
          <span class="fw-bold"><?= esc($teacher['government_employee_no'] ?? $teacher['employee_id'] ?? '—') ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">Position</span>
          <span class="fw-bold"><?= esc($teacher['position'] ?? '—') ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="text-muted">Department</span>
          <span class="fw-bold"><?= esc($department ?? 'Not Assigned') ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <span class="text-muted">Status</span>
          <span class="badge bg-success">Active</span>
        </div>
      </div>
    </div>


  </div>
</div>

<div class="row mt-4">
  <div class="col-12">
    <div class="profile-card">
      <div class="card-body p-4">
        <h5 class="text-primary mb-2"><i class="bi bi-file-earmark-person me-2"></i>Personnel Record</h5>
        <p class="text-muted small mb-3">Official personnel data on file. Contact the administrator to request corrections.</p>
        <?= view('admin/partials/teacher_personnel_details', ['teacher' => $teacher]) ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmPasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Confirm Password Change</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to change your password?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #495057; color: white;">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="submitPasswordChange()">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script>
function confirmPasswordChange() {
  const form = document.getElementById('changePasswordForm');
  if (form.checkValidity()) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    if (newPass !== confirmPass) {
      alert('New password and confirm password do not match!');
      return;
    }
    const modalEl = document.getElementById('confirmPasswordModal');
    const portal = document.getElementById('dashboard-modal-portal') || document.body;
    if (modalEl && modalEl.parentElement !== portal) {
      portal.appendChild(modalEl);
    }
    const modal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: true, focus: true });
    modal.show();
  } else {
    form.reportValidity();
  }
}

function submitPasswordChange() {
  document.getElementById('changePasswordForm').submit();
}

function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Add form submission debugging
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileUpdateForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            console.log('Profile form submitted');
            
            // Basic validation
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!firstName || !lastName || !email) {
                e.preventDefault();
                alert('Please fill in all required fields (First Name, Last Name, Email)');
                return false;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
            
            console.log('Form validation passed, submitting...');
        });
    }
});
</script>

<?= $this->endSection() ?>
