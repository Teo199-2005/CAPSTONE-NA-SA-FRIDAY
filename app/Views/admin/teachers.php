<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Manage Teachers</h1>
  <div>
    <a href="<?= base_url('admin/teachers/create') ?>" class="btn btn-primary me-2">
      <i class="bi bi-plus-circle"></i> Add New Teacher
    </a>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show">
    <?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- Search Form -->
<form class="row g-2 mb-3" method="get">
  <div class="col-md-4">
    <label class="form-label">Search</label>
    <input type="text" name="search" class="form-control" placeholder="Name, Employee No., TIN, PRC, or Email"
           value="<?= esc($search ?? '') ?>">
  </div>
  <div class="col-md-3">
    <label class="form-label">Assignment</label>
    <select name="assignment" class="form-select">
      <option value="">All Teachers</option>
      <option value="assigned" <?= ($assignment ?? '') == 'assigned' ? 'selected' : '' ?>>With Advisory Class</option>
      <option value="unassigned" <?= ($assignment ?? '') == 'unassigned' ? 'selected' : '' ?>>No Advisory Class</option>
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Sort by</label>
    <select name="sort_by" class="form-select">
      <option value="name" <?= ($sortBy ?? 'name') === 'name' ? 'selected' : '' ?>>Name</option>
      <option value="age" <?= ($sortBy ?? 'name') === 'age' ? 'selected' : '' ?>>Age</option>
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Order</label>
    <select name="sort_order" class="form-select">
      <option value="asc" <?= ($sortOrder ?? 'asc') === 'asc' ? 'selected' : '' ?>>Ascending</option>
      <option value="desc" <?= ($sortOrder ?? 'asc') === 'desc' ? 'selected' : '' ?>>Descending</option>
    </select>
  </div>
  <div class="col-auto d-flex align-items-end">
    <button type="submit" class="btn btn-primary me-2">Filter</button>
    <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary">Clear</a>
  </div>
</form>

<div class="card">
  <div class="card-body p-0">
    <?php if (!empty($teachers)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr>
              <th>Emp. No.</th>
              <th>Name</th>
              <th>Age</th>
              <th>Email</th>
              <th>Department</th>
              <th>Position</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($teachers as $teacher): ?>
              <tr>
                <td><?= esc($teacher['government_employee_no'] ?? $teacher['employee_id'] ?? '—') ?></td>
                <td><?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></td>
                <td><?= esc((string) ($teacher['age'] ?? '—')) ?></td>
                <td><?= esc($teacher['email']) ?></td>
                <td><?= esc($teacher['department'] ?? '—') ?></td>
                <td><?= esc($teacher['position'] ?? '—') ?></td>
                <td>
                  <?php
                  $statusClass = match($teacher['employment_status'] ?? 'active') {
                    'active' => 'bg-success',
                    'inactive' => 'bg-danger',
                    'on_leave' => 'bg-warning',
                    default => 'bg-secondary'
                  };
                  ?>
                  <span class="badge <?= $statusClass ?>">
                    <?= ucfirst(str_replace('_', ' ', $teacher['employment_status'] ?? 'active')) ?>
                  </span>
                </td>
                <td class="text-end">
                  <div class="btn-group" role="group">
                    <?php if (!empty($teacher['section_name'])): ?>
                      <button class="btn btn-sm btn-outline-success" onclick="viewAdvisoryClass(<?= $teacher['id'] ?>, '<?= esc($teacher['section_name']) ?>', <?= $teacher['grade_level'] ?>)" title="Advisory Class">
                        <i class="bi bi-people"></i>
                      </button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewTeacher(<?= $teacher['id'] ?>)" title="View Details">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="openEditTeacherModal(<?= $teacher['id'] ?>)" title="Edit Teacher">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTeacher(<?= $teacher['id'] ?>, '<?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?>')" title="Delete Teacher">
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
      <div class="p-4 text-center text-muted">
        <i class="bi bi-person-x display-1 text-muted"></i>
        <p class="mt-3">No teachers found.</p>
        <a href="<?= base_url('admin/teachers/create') ?>" class="btn btn-primary">
          <i class="bi bi-plus-circle"></i> Add First Teacher
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>





<style>
/* Teacher sections cell styling */
.teacher-sections-cell {
  max-width: 300px;
}

.section-badge {
  display: inline-block !important;
  margin-right: 5px;
  margin-bottom: 3px;
  padding: 3px 8px;
  font-size: 0.75rem;
  white-space: nowrap;
}

/* Teacher modal uses Bootstrap defaults - no custom styles needed */

/* Teacher Info Styles */
.teacher-info-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 2px solid #e2e8f0;
}

.teacher-info-title {
  color: #1e40af;
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e2e8f0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.teacher-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.teacher-info-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.teacher-info-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
}

.teacher-info-value {
  font-size: 1rem;
  color: #111827;
  font-weight: 500;
}

.status-badge {
  display: inline-flex;
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-active {
  background-color: #d1fae5;
  color: #065f46;
}
</style>

<script>
// Store teacher data for fallback
const teachersData = <?= json_encode($teachers ?? []) ?>;

// View teacher details in full page
function viewTeacher(teacherId) {
  window.location.href = `<?= base_url('admin/teachers/view/') ?>${teacherId}`;
}

function buildTeacherDetailsModal(teacherId) {
  const existing = document.getElementById('customTeacherModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="customTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Teacher Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="customTeacherDetails">
          <div class="text-center">
            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            <p class="mt-2">Loading teacher details...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('customTeacherModal');
}

function openTeacherModal(teacherId) {
  const modalEl = buildTeacherDetailsModal(teacherId);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
  
  // Load teacher data
  fetch(`<?= base_url('admin/teachers') ?>/details/${teacherId}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('customTeacherDetails').innerHTML = html;
    })
    .catch(error => {
      console.error('Error:', error);
      // Fallback to local teacher data
      const teacher = teachersData.find(t => t.id == teacherId);
      if (teacher) {
        showBasicTeacherDetails(teacher, document.getElementById('customTeacherDetails'));
      } else {
        document.getElementById('customTeacherDetails').innerHTML = '<div class="alert alert-danger">Failed to load teacher data</div>';
      }
    });
}

// Fallback function to show basic teacher details
function showBasicTeacherDetails(teacher, container) {
  const html = `
    <div class="teacher-info-section">
      <div class="teacher-info-title">
        <i class="bi bi-person"></i>
        Personal Information
      </div>
      <div class="teacher-info-grid">
        <div class="teacher-info-item">
          <div class="teacher-info-label">Full Name</div>
          <div class="teacher-info-value">${teacher.first_name} ${teacher.last_name}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">PRC License Number</div>
          <div class="teacher-info-value">${teacher.license_number || 'N/A'}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Email</div>
          <div class="teacher-info-value">${teacher.email || 'N/A'}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Department</div>
          <div class="teacher-info-value">${teacher.department || 'N/A'}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Position</div>
          <div class="teacher-info-value">${teacher.position || 'N/A'}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Status</div>
          <div class="teacher-info-value">
            <span class="status-badge status-${teacher.employment_status || 'active'}">
              ${(teacher.employment_status || 'active').replace('_', ' ').toUpperCase()}
            </span>
          </div>
        </div>
      </div>
    </div>
  `;
  container.innerHTML = html;
}

function buildEditTeacherModal(teacherId) {
  const existing = document.getElementById('editTeacherModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="editTeacherModal" tabindex="-1">
    <div class="modal-dialog modal-xl"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Teacher</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editTeacherForm" method="post" action="<?= base_url('admin/teachers/update') ?>/${teacherId}">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="POST">
        <div class="modal-body">
          <div id="editTeacherContent">
            <div class="text-center">
              <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
              <p class="mt-2">Loading teacher details...</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Teacher</button>
        </div>
      </form>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('editTeacherModal');
}

function openEditTeacherModal(teacherId) {
  if (!teacherId || teacherId <= 0) {
    alert('Invalid teacher ID');
    return;
  }
  
  const modalEl = buildEditTeacherModal(teacherId);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
  
  // Load teacher data
  fetch(`<?= base_url('admin/teachers/edit-form') ?>/${teacherId}`, {
    method: 'GET',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'text/html,application/json'
    }
  })
    .then(response => {
      if (!response.ok) {
        if (response.status === 404) {
          throw new Error('Teacher not found');
        } else if (response.status === 403) {
          throw new Error('Access denied');
        } else {
          throw new Error(`Server error: ${response.status}`);
        }
      }
      return response.text();
    })
    .then(html => {
      if (html.trim().startsWith('{')) {
        // Response is JSON (error)
        const errorData = JSON.parse(html);
        throw new Error(errorData.error || 'Failed to load teacher data');
      }
      document.getElementById('editTeacherContent').innerHTML = html;
    })
    .catch(error => {
      console.error('Error loading teacher data:', error);
      const errorMessage = error.message || 'Failed to load teacher data';
      document.getElementById('editTeacherContent').innerHTML = 
        `<div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>
          ${errorMessage}
          <br><small class="mt-2 d-block">Please try again or contact support if the problem persists.</small>
        </div>`;
    });
}



// Handle edit form submission
document.addEventListener('submit', function(e) {
  if (e.target.id === 'editTeacherForm') {
    e.preventDefault();
    
    // Remove existing error messages
    const existingErrors = document.querySelectorAll('#editTeacherContent .alert-danger, #editTeacherContent .alert-success');
    existingErrors.forEach(error => error.remove());
    
    // Basic client-side validation
    const form = e.target;
    const firstName = form.querySelector('#first_name')?.value?.trim();
    const lastName = form.querySelector('#last_name')?.value?.trim();
    const email = form.querySelector('#email')?.value?.trim();
    const gender = form.querySelector('#gender')?.value;
    const birthMonth = form.querySelector('#birth_month')?.value;
    const birthDay = form.querySelector('#birth_day')?.value;
    const birthYear = form.querySelector('#birth_year')?.value;
    const employmentStatus = form.querySelector('#employment_status')?.value;
    const tin = form.querySelector('#tin')?.value;
    const philsys = form.querySelector('#philsys_number')?.value;
    
    const errors = [];
    if (!firstName) errors.push('First name is required');
    if (!lastName) errors.push('Last name is required');
    if (!email) errors.push('Email is required');
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Please enter a valid email address');
    if (!gender) errors.push('Sex is required');
    if (!birthMonth || !birthDay || !birthYear) errors.push('Complete date of birth (month, day, year) is required');
    if (!employmentStatus) errors.push('Employment status is required');
    if (tin && !/^\d{3}-\d{3}-\d{3}$/.test(tin)) errors.push('TIN must be in format 999-999-999');
    if (philsys && !/^\d{12}$/.test(philsys)) errors.push('PhilSys number must be exactly 12 digits');
    
    if (errors.length > 0) {
      const errorHtml = `<div class="alert alert-danger"><ul class="mb-0">${errors.map(error => `<li>${error}</li>`).join('')}</ul></div>`;
      document.getElementById('editTeacherContent').insertAdjacentHTML('afterbegin', errorHtml);
      return;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
    submitBtn.disabled = true;
    
    const formData = new FormData(e.target);
    
    fetch(e.target.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      console.log('Response status:', response.status);
      console.log('Response headers:', response.headers);
      
      if (!response.ok) {
        return response.text().then(text => {
          console.error('Error response body:', text);
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        });
      }
      
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        return response.json();
      } else {
        return response.text().then(text => {
          console.error('Non-JSON response:', text);
          throw new Error('Server returned non-JSON response');
        });
      }
    })
    .then(data => {
      if (data.success) {
        // Show success message briefly
        const successHtml = '<div class="alert alert-success">Teacher updated successfully!</div>';
        document.getElementById('editTeacherContent').insertAdjacentHTML('afterbegin', successHtml);
        
        setTimeout(() => {
          bootstrap.Modal.getInstance(document.getElementById('editTeacherModal')).hide();
          location.reload();
        }, 1000);
      } else {
        // Show validation errors
        let errorHtml = '<div class="alert alert-danger">';
        if (data.errors && typeof data.errors === 'object') {
          errorHtml += '<ul class="mb-0">';
          for (let field in data.errors) {
            if (Array.isArray(data.errors[field])) {
              data.errors[field].forEach(error => {
                errorHtml += `<li>${error}</li>`;
              });
            } else {
              errorHtml += `<li>${data.errors[field]}</li>`;
            }
          }
          errorHtml += '</ul>';
        } else if (data.error) {
          errorHtml += data.error;
        } else {
          errorHtml += 'Failed to update teacher. Please check your input and try again.';
        }
        errorHtml += '</div>';
        document.getElementById('editTeacherContent').insertAdjacentHTML('afterbegin', errorHtml);
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      let errorMessage = 'Network error occurred. Please check your connection and try again.';
      
      if (error.message.includes('HTTP 404')) {
        errorMessage = 'Update endpoint not found. Please contact support.';
      } else if (error.message.includes('HTTP 403')) {
        errorMessage = 'Access denied. Please refresh the page and try again.';
      } else if (error.message.includes('HTTP 500')) {
        errorMessage = 'Server error occurred. Please try again later.';
      } else if (error.message.includes('Failed to fetch')) {
        errorMessage = 'Connection failed. Please check your internet connection.';
      }
      
      const errorHtml = `<div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        ${errorMessage}
        <br><small class="mt-2 d-block text-muted">Error details: ${error.message}</small>
      </div>`;
      document.getElementById('editTeacherContent').insertAdjacentHTML('afterbegin', errorHtml);
    })
    .finally(() => {
      // Reset button state
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  }
});

// Manage teacher schedule
function manageSchedule(teacherId, teacherName) {
  window.location.href = `<?= base_url('admin/teachers/schedule/') ?>${teacherId}`;
}

// Toggle subjects display
function toggleSubjects(event, teacherId) {
  event.preventDefault();
  const subjectsList = document.getElementById('subjects-' + teacherId);
  const button = event.currentTarget;
  const icon = button.querySelector('i');
  
  if (subjectsList.style.display === 'none') {
    subjectsList.style.display = 'block';
    icon.className = 'bi bi-chevron-up';
  } else {
    subjectsList.style.display = 'none';
    icon.className = 'bi bi-chevron-down';
  }
}

// View advisory class function
function viewAdvisoryClass(teacherId, sectionName, gradeLevel) {
  const modalHtml = `
  <div class="modal fade" id="advisoryClassModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-book me-2"></i>Teacher Classes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center">
            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            <p class="mt-2">Loading class information...</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #6c757d; color: white;">Close</button>
        </div>
      </div>
    </div>
  </div>`;
  
  const existing = document.getElementById('advisoryClassModal');
  if (existing) existing.remove();
  
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  const modal = new bootstrap.Modal(document.getElementById('advisoryClassModal'));
  modal.show();
  
  // Fetch teacher's classes
  const teacher = teachersData.find(t => t.id == teacherId);
  if (!teacher) {
    document.querySelector('#advisoryClassModal .modal-body').innerHTML = 
      '<div class="alert alert-danger">Teacher not found</div>';
    return;
  }
  
  let html = '<div class="p-3">';
  
  // Advisory Section
  if (teacher.section_name) {
    html += `
      <div class="mb-4">
        <h6 class="fw-semibold text-dark mb-3"><i class="bi bi-star-fill me-2 dash-icon-inline"></i>Advisory Class</h6>
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">${teacher.section_name}</h5>
            <p class="card-text"><strong>Grade Level:</strong> ${teacher.grade_level}</p>
          </div>
        </div>
      </div>
    `;
  }
  
  // Subject Classes
  if (teacher.teaching_sections && teacher.teaching_sections.length > 0) {
    html += `
      <div>
        <h6 class="text-info mb-3"><i class="bi bi-book me-2"></i>Subject Classes</h6>
        <div class="row g-3">
    `;
    
    teacher.teaching_sections.forEach(section => {
      html += `
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title">${section.section_name}</h6>
              <p class="card-text mb-1"><strong>Grade:</strong> ${section.grade_level}</p>
              <p class="card-text mb-0"><strong>Subject:</strong> ${section.subject_name}</p>
            </div>
          </div>
        </div>
      `;
    });
    
    html += '</div></div>';
  }
  
  if (!teacher.section_name && (!teacher.teaching_sections || teacher.teaching_sections.length === 0)) {
    html += '<div class="alert alert-info">No classes assigned to this teacher</div>';
  }
  
  html += '</div>';
  
  document.querySelector('#advisoryClassModal .modal-body').innerHTML = html;
}

// Delete teacher function
function deleteTeacher(teacherId, teacherName) {
  showDeleteConfirmation(teacherId, teacherName);
}

function getDashboardModalPortal() {
  return document.getElementById('dashboard-modal-portal') || document.body;
}

function createDashboardModal(id, title, bodyHtml, confirmButtonId, confirmButtonClass, confirmButtonText) {
  const existing = document.getElementById(id);
  if (existing) existing.remove();

  const modalHtml = `
  <div class="modal fade" id="${id}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">${title}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          ${bodyHtml}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: darkgray; border-color: darkgray;">Cancel</button>
          <button type="button" class="btn ${confirmButtonClass}" id="${confirmButtonId}">${confirmButtonText}</button>
        </div>
      </div>
    </div>
  </div>`;

  getDashboardModalPortal().insertAdjacentHTML('beforeend', modalHtml);
  return document.getElementById(id);
}

function showDeleteConfirmation(teacherId, teacherName) {
  const modalEl = createDashboardModal(
    'deleteConfirmModal',
    'Confirm Action',
    `
      <div class="alert alert-warning border-0">
        <i class="bi bi-question-circle fs-2 mb-2 d-block text-center"></i>
        <p class="text-center mb-0">Are you sure you want to delete teacher "${teacherName}"? This action cannot be undone.</p>
      </div>
    `,
    'confirmDeleteBtn',
    'btn-primary',
    'Confirm'
  );

  const modal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: true, focus: true });
  const confirmBtn = modalEl.querySelector('#confirmDeleteBtn');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      confirmDeleteTeacher(teacherId);
    }, { once: true });
  }
  modal.show();
}

function confirmDeleteTeacher(teacherId) {
  const modal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
  if (modal) modal.hide();
  
  fetch(`<?= base_url('admin/teachers/delete') ?>/${teacherId}`, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showDeleteAlert(data.message);
      setTimeout(() => location.reload(), 1500);
    } else {
      showDeleteAlert('Error: ' + data.error, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showDeleteAlert('Failed to delete teacher', 'error');
  });
}

function showDeleteAlert(message, type = 'success') {
  const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
  const iconClass = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
  
  const modalEl = createDashboardModal(
    'deleteAlertModal',
    'Alert',
    `
      <div class="${alertClass} border-0 mb-3">
        <i class="${iconClass} fs-2 mb-2"></i>
        <p class="mb-0">${message}</p>
      </div>
    `,
    'deleteAlertOkBtn',
    'btn-primary',
    'OK'
  );

  const okBtn = modalEl.querySelector('#deleteAlertOkBtn');
  if (okBtn) {
    okBtn.setAttribute('data-bs-dismiss', 'modal');
  }

  const modal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: true, focus: true });
  modal.show();
}
</script>

<?= $this->endSection() ?>
