<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Create User</h1>
  <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <?= form_open('admin/users/create') ?>
      <?= csrf_field() ?>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
               id="email" name="email" value="<?= old('email') ?>" required>
        <?php if (isset($validation) && $validation->hasError('email')): ?>
          <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
               id="password" name="password" required>
        <?php if (isset($validation) && $validation->hasError('password')): ?>
          <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select class="form-select <?= isset($validation) && $validation->hasError('role') ? 'is-invalid' : '' ?>" 
                id="role" name="role" required onchange="toggleRoleFields()">
          <option value="">Select Role</option>
          <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="teacher" <?= old('role') === 'teacher' ? 'selected' : '' ?>>Teacher</option>
          <option value="student" <?= old('role') === 'student' ? 'selected' : '' ?>>Student</option>
          <option value="parent" <?= old('role') === 'parent' ? 'selected' : '' ?>>Parent</option>
        </select>
        <?php if (isset($validation) && $validation->hasError('role')): ?>
          <div class="invalid-feedback"><?= $validation->getError('role') ?></div>
        <?php endif; ?>
      </div>

      <!-- Student Fields -->
      <div id="studentFields" class="role-fields" style="display: none;">
        <h6 class="text-primary mb-3">Student Information</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="first_name" class="form-label">First Name *</label>
            <input type="text" class="form-control" name="first_name" value="<?= old('first_name') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="last_name" class="form-label">Last Name *</label>
            <input type="text" class="form-control" name="last_name" value="<?= old('last_name') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="gender" class="form-label">Gender *</label>
            <select class="form-select" name="gender">
              <option value="">Select</option>
              <option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
              <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label for="date_of_birth" class="form-label">Date of Birth *</label>
            <input type="date" class="form-control" name="date_of_birth" value="<?= old('date_of_birth') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="grade_level" class="form-label">Grade Level *</label>
            <select class="form-select" name="grade_level">
              <option value="">Select</option>
              <?php foreach (grade_level_options() as $g): ?>
                <option value="<?= $g ?>" <?= old('grade_level') === (string) $g ? 'selected' : '' ?>><?= esc(grade_level_label($g)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label for="lrn" class="form-label">LRN</label>
            <input type="text" class="form-control" name="lrn" value="<?= old('lrn') ?>" placeholder="e.g. 123456789012">
          </div>
        </div>
      </div>

      <!-- Teacher Fields -->
      <div id="teacherFields" class="role-fields" style="display: none;">
        <h6 class="text-primary mb-3">Teacher Information</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="teacher_first_name" class="form-label">First Name *</label>
            <input type="text" class="form-control" name="teacher_first_name" value="<?= old('teacher_first_name') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="teacher_last_name" class="form-label">Last Name *</label>
            <input type="text" class="form-control" name="teacher_last_name" value="<?= old('teacher_last_name') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="employee_id" class="form-label">Employee ID</label>
            <input type="text" class="form-control" name="employee_id" value="<?= old('employee_id') ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" name="department" value="<?= old('department') ?>">
          </div>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Cancel</a>
      </div>
    <?= form_close() ?>
  </div>
</div>

<script>
function toggleRoleFields() {
  const role = document.getElementById('role').value;
  const studentFields = document.getElementById('studentFields');
  const teacherFields = document.getElementById('teacherFields');
  
  console.log('Role selected:', role); // Debug
  
  // Hide all fields
  document.querySelectorAll('.role-fields').forEach(field => {
    field.style.display = 'none';
  });
  
  // Show relevant fields
  if (role === 'student' && studentFields) {
    studentFields.style.display = 'block';
    console.log('Showing student fields'); // Debug
  } else if (role === 'teacher' && teacherFields) {
    teacherFields.style.display = 'block';
    console.log('Showing teacher fields'); // Debug
  }
}

// Ensure DOM is loaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
      roleSelect.onchange = toggleRoleFields;
      toggleRoleFields();
    }
  });
} else {
  const roleSelect = document.getElementById('role');
  if (roleSelect) {
    roleSelect.onchange = toggleRoleFields;
    toggleRoleFields();
  }
}
</script>

<?= $this->endSection() ?>