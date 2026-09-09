<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Enroll New Student</h1>
  <div>
    <a href="<?= base_url('admin/students') ?>" class="btn btn-outline-secondary">Back to Students</a>
  </div>
</div>

<form id="enrollStudentForm" method="post" action="<?= base_url('admin/students/store') ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0"><i class="bi bi-person-badge me-2"></i>Account Information</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">LRN *</label>
            <input type="text" class="form-control" name="lrn" required maxlength="12" pattern="[0-9]{12}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12)">
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" class="form-control" name="email" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Password *</label>
            <div style="position: relative;">
              <input type="password" class="form-control" name="password" id="password" required minlength="8">
              <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                <i class="bi bi-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Confirm Password *</label>
            <div style="position: relative;">
              <input type="password" class="form-control" name="confirm_password" id="confirm_password" required minlength="8">
              <button type="button" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                <i class="bi bi-eye" id="confirmEyeIcon"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">First Name *</label>
            <input type="text" class="form-control" name="first_name" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Middle Name</label>
            <input type="text" class="form-control" name="middle_name" oninput="this.value = this.value.replace(/[0-9]/g, '')">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Last Name *</label>
            <input type="text" class="form-control" name="last_name" required oninput="this.value = this.value.replace(/[0-9]/g, '')">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Suffix</label>
            <select class="form-select" name="suffix">
              <option value="">None</option>
              <option value="Jr.">Jr.</option>
              <option value="Sr.">Sr.</option>
              <option value="II">II</option>
              <option value="III">III</option>
              <option value="IV">IV</option>
              <option value="V">V</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Gender *</label>
            <select class="form-select" name="gender" required>
              <option value="">Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Date of Birth *</label>
            <input type="date" class="form-control" name="date_of_birth" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Place of Birth</label>
            <input type="text" class="form-control" name="place_of_birth">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Nationality</label>
            <input type="text" class="form-control" name="nationality" value="Filipino">
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Religion</label>
            <input type="text" class="form-control" name="religion">
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" class="form-control" name="contact_number" oninput="this.value = this.value.replace(/[^0-9+\-\s()]/g, '')">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0"><i class="bi bi-book me-2"></i>Academic Information</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Grade Level *</label>
            <select class="form-select" name="grade_level" id="gradeLevel" required>
              <option value="">Select Grade</option>
              <?php foreach (grade_level_options() as $g): ?>
                <option value="<?= $g ?>"><?= esc(grade_level_label($g)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Section</label>
            <select class="form-select" name="section_id" id="sectionSelect">
              <option value="">Select grade level first</option>
            </select>
            <div id="sectionCapacityInfo" class="form-text" style="display: none;"></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Student Type *</label>
            <select class="form-select" name="student_type" required>
              <option value="">Select Type</option>
              <option value="New Student">New Student</option>
              <option value="Transferee">Transferee</option>
              <option value="Old Student">Old Student</option>
            </select>
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea class="form-control" name="address" rows="2"></textarea>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0"><i class="bi bi-file-earmark me-2"></i>Documents (Optional)</h5>
      <small class="text-muted">Documents can be uploaded later if not available now</small>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Birth Certificate</label>
            <input type="file" class="form-control" name="birth_certificate" accept=".pdf,.jpg,.jpeg,.png">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Report Card (Form 138)</label>
            <input type="file" class="form-control" name="report_card" accept=".pdf,.jpg,.jpeg,.png">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Good Moral Certificate</label>
            <input type="file" class="form-control" name="good_moral" accept=".pdf,.jpg,.jpeg,.png">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">2x2 Photo</label>
            <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0"><i class="bi bi-telephone me-2"></i>Emergency Contact</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Emergency Contact Name</label>
            <input type="text" class="form-control" name="emergency_contact_name" oninput="this.value = this.value.replace(/[0-9]/g, '')">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Emergency Contact Number</label>
            <input type="text" class="form-control" name="emergency_contact_number" oninput="this.value = this.value.replace(/[^0-9+\-\s()]/g, '')">
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Relationship</label>
        <select class="form-select" name="emergency_contact_relationship">
          <option value="">Select Relationship</option>
          <option value="Father">Father</option>
          <option value="Mother">Mother</option>
          <option value="Guardian">Guardian</option>
          <option value="Grandfather">Grandfather</option>
          <option value="Grandmother">Grandmother</option>
          <option value="Uncle">Uncle</option>
          <option value="Aunt">Aunt</option>
          <option value="Other">Other</option>
        </select>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-end gap-2 mb-4">
    <a href="<?= base_url('admin/students') ?>" class="btn btn-secondary" style="background-color: #374151; border-color: #374151;">Cancel</a>
    <button type="submit" class="btn btn-primary">Enroll Student</button>
  </div>
</form>

<script>
// Password toggle functionality
document.getElementById('togglePassword').addEventListener('click', function() {
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eyeIcon');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.className = 'bi bi-eye-slash';
  } else {
    passwordInput.type = 'password';
    eyeIcon.className = 'bi bi-eye';
  }
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
  const confirmPasswordInput = document.getElementById('confirm_password');
  const confirmEyeIcon = document.getElementById('confirmEyeIcon');
  
  if (confirmPasswordInput.type === 'password') {
    confirmPasswordInput.type = 'text';
    confirmEyeIcon.className = 'bi bi-eye-slash';
  } else {
    confirmPasswordInput.type = 'password';
    confirmEyeIcon.className = 'bi bi-eye';
  }
});

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
  const password = document.getElementById('password').value;
  const confirmPassword = this.value;
  
  if (password !== confirmPassword) {
    this.setCustomValidity('Passwords do not match');
  } else {
    this.setCustomValidity('');
  }
});



// Grade level change - filter sections with capacity info
const allSections = <?= json_encode($sections ?? []) ?>;
document.getElementById('gradeLevel').addEventListener('change', function() {
  const gradeLevel = this.value;
  const sectionSelect = document.getElementById('sectionSelect');
  const capacityInfo = document.getElementById('sectionCapacityInfo');
  
  sectionSelect.innerHTML = '<option value="">No Section Assigned</option>';
  capacityInfo.style.display = 'none';
  
  if (gradeLevel) {
    // Fetch sections with enrollment counts
    fetch(`<?= base_url('admin/sections/grade-sections/') ?>${gradeLevel}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          data.sections.forEach(section => {
            const option = document.createElement('option');
            option.value = section.id;
            const capacity = section.max_capacity || 40;
            const enrollment = section.current_enrollment || 0;
            const status = enrollment >= capacity ? ' (FULL)' : ` (${enrollment}/${capacity})`;
            option.textContent = section.section_name + status;
            option.disabled = enrollment >= capacity;
            sectionSelect.appendChild(option);
          });
        }
      })
      .catch(error => {
        console.error('Error fetching sections:', error);
        // Fallback to original method
        const filteredSections = allSections.filter(s => s.grade_level == gradeLevel);
        filteredSections.forEach(section => {
          const option = document.createElement('option');
          option.value = section.id;
          option.textContent = section.section_name;
          sectionSelect.appendChild(option);
        });
      });
  }
});

// Section selection - show capacity info
document.getElementById('sectionSelect').addEventListener('change', function() {
  const sectionId = this.value;
  const capacityInfo = document.getElementById('sectionCapacityInfo');
  
  if (sectionId) {
    fetch(`<?= base_url('admin/sections/capacity-info/') ?>${sectionId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const capacity = data.section.max_capacity || 40;
          const enrollment = data.section.current_enrollment || 0;
          
          if (enrollment >= capacity) {
            capacityInfo.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> This section is at full capacity (${enrollment}/${capacity})</span>`;
            capacityInfo.className = 'form-text text-danger';
          } else {
            capacityInfo.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> Available slots: ${capacity - enrollment} (${enrollment}/${capacity})</span>`;
            capacityInfo.className = 'form-text text-success';
          }
          capacityInfo.style.display = 'block';
        }
      })
      .catch(error => {
        console.error('Error fetching capacity info:', error);
        capacityInfo.style.display = 'none';
      });
  } else {
    capacityInfo.style.display = 'none';
  }
});

// Form submission with capacity validation
document.getElementById('enrollStudentForm').addEventListener('submit', function(e) {
  const sectionSelect = document.getElementById('sectionSelect');
  const selectedOption = sectionSelect.options[sectionSelect.selectedIndex];
  
  if (selectedOption && selectedOption.disabled) {
    e.preventDefault();
    alert('Cannot enroll in a full section. Please select a different section.');
    return false;
  }
  
  const submitBtn = this.querySelector('button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Enrolling...';
  return true;
});
</script>

<?= $this->endSection() ?>