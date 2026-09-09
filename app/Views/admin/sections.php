<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Manage Sections</h1>
  <div>
    <button class="btn btn-warning me-2" onclick="showBulkEditSections()">
      <i class="bi bi-pencil-square"></i> Edit Sections
    </button>
    <button class="btn btn-primary me-2" onclick="showCreateSection()">
      <i class="bi bi-plus-circle"></i> Create Section
    </button>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

<!-- Summary Statistics -->
<div class="card mb-4">
  <div class="card-body">
    <div class="row sections-summary-stats text-center g-3">
      <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center justify-content-center">
          <div class="text-primary me-3">
            <i class="bi bi-building fs-2"></i>
          </div>
          <div>
            <h4 class="mb-0"><?= count($sections) ?></h4>
            <small class="text-muted">Total Sections</small>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center justify-content-center">
          <div class="text-success me-3">
            <i class="bi bi-person-check fs-2"></i>
          </div>
          <div>
            <h4 class="mb-0"><?= count(array_filter($sections, fn($s) => !empty($s['adviser_name']))) ?></h4>
            <small class="text-muted">With Advisers</small>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center justify-content-center">
          <div class="text-warning me-3">
            <i class="bi bi-person-x fs-2"></i>
          </div>
          <div>
            <h4 class="mb-0"><?= count(array_filter($sections, fn($s) => empty($s['adviser_name']))) ?></h4>
            <small class="text-muted">Need Advisers</small>
          </div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="d-flex align-items-center justify-content-center">
          <div class="text-info me-3">
            <i class="bi bi-people fs-2"></i>
          </div>
          <div>
            <h4 class="mb-0"><?= array_sum(array_column($sections, 'max_capacity')) ?></h4>
            <small class="text-muted">Total Capacity</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Options -->
<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <label class="form-label">Grade Level</label>
    <select name="grade" class="form-select" onchange="this.form.submit()">
      <option value="">All Grades</option>
      <?php foreach (grade_level_options() as $g): ?>
        <option value="<?= $g ?>" <?= (($gradeFilter ?? '') == $g ? 'selected' : '') ?>><?= esc(grade_level_label($g)) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label">Adviser Status</label>
    <select name="adviser_status" class="form-select" onchange="this.form.submit()">
      <option value="">All Sections</option>
      <option value="with_adviser" <?= (($adviserFilter ?? '') == 'with_adviser' ? 'selected' : '') ?>>With Adviser</option>
      <option value="no_adviser" <?= (($adviserFilter ?? '') == 'no_adviser' ? 'selected' : '') ?>>Need Adviser</option>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label">Search</label>
    <input type="text" name="search" class="form-control" value="<?= esc($searchTerm ?? '') ?>" placeholder="Section name or teacher">
  </div>
  <div class="col-auto align-self-end">
    <button class="btn btn-primary">Filter</button>
    <?php if (!empty($gradeFilter) || !empty($adviserFilter) || !empty($searchTerm)): ?>
      <a href="<?= base_url('admin/sections') ?>" class="btn btn-outline-secondary">Clear</a>
    <?php endif; ?>
  </div>
</form>

<!-- Sections Table -->
<?php if (!empty($sections)): ?>
  <?php
  // Group sections by grade level
  $sectionsByGrade = [];
  foreach ($sections as $section) {
    $sectionsByGrade[$section['grade_level']][] = $section;
  }
  ksort($sectionsByGrade);
  ?>

  <?php foreach ($sectionsByGrade as $gradeLevel => $gradeSections): ?>
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 fw-semibold">
          <i class="bi bi-mortarboard"></i> <?= esc(grade_level_label((int) $gradeLevel)) ?> Sections
          <span class="badge bg-secondary ms-2"><?= count($gradeSections) ?> sections</span>
        </h6>
        <button class="btn btn-primary btn-sm" onclick="autoAssignStudents(<?= $gradeLevel ?>)">
          <i class="bi bi-distribute-vertical"></i> Auto-Assign
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
                      <table class="table table-striped table-hover mb-0 table-sections-list" data-no-enhance="1">
              <thead>
                <tr>
                  <th>Section Name</th>
                  <th>Grading Type</th>
                  <th>Enrollment</th>
                  <th>Capacity</th>
                  <th>Section Adviser</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($gradeSections as $section): ?>
                  <tr>
                    <td>
                      <div class="fw-semibold"><?= esc($section['section_name']) ?></div>
                      <small class="text-muted">School Year: <?= esc($section['school_year']) ?></small>
                    </td>
                    <td>
                      <?php if (($section['grading_type'] ?? 'numerical') === 'non_numerical'): ?>
                        <span class="badge bg-success"><i class="bi bi-symbols me-1"></i>Non-Numerical</span>
                      <?php else: ?>
                        <span class="badge bg-primary"><i class="bi bi-123 me-1"></i>Numerical</span>
                      <?php endif; ?>
                    </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <span class="me-2"><?= $section['current_enrollment'] ?>/<?= $section['max_capacity'] ?></span>
                      <div class="progress" style="width: 60px; height: 8px;">
                        <?php
                        $percentage = $section['max_capacity'] > 0 ? ($section['current_enrollment'] / $section['max_capacity']) * 100 : 0;
                        $progressClass = $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success');
                        ?>
                        <div class="progress-bar <?= $progressClass ?>" style="width: <?= $percentage ?>%"></div>
                      </div>
                    </div>
                  </td>
                  <td><?= $section['max_capacity'] ?> students</td>
                  <td>
                    <?php if (!empty($section['adviser_name'])): ?>
                      <div class="fw-semibold text-success"><?= esc($section['adviser_name']) ?></div>
                      <?php if (!empty($section['adviser_email'])): ?>
                        <small class="text-muted"><?= esc($section['adviser_email']) ?></small>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i> No Adviser
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-<?= $section['is_active'] ? 'success' : 'secondary' ?>">
                      <?= $section['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                  </td>
                  <td>
                    <div class="table-actions-grid" role="group" aria-label="Section actions">
                      <button type="button" class="btn btn-sm table-action-btn table-action-btn--adviser" onclick="assignAdviser(<?= $section['id'] ?>, '<?= esc($section['section_name']) ?>', <?= $gradeLevel ?>)" title="Assign adviser">
                        <i class="bi bi-person-plus" aria-hidden="true"></i><span class="table-action-label">Adviser</span>
                      </button>
                      <button type="button" class="btn btn-sm table-action-btn table-action-btn--teachers" onclick="viewSectionTeachers(<?= $section['id'] ?>, '<?= esc($section['section_name']) ?>')" title="View teachers">
                        <i class="bi bi-person-video3" aria-hidden="true"></i><span class="table-action-label">Teachers</span>
                      </button>
                      <button type="button" class="btn btn-sm table-action-btn table-action-btn--students" onclick="viewSectionStudents(<?= $section['id'] ?>)" title="View students">
                        <i class="bi bi-people-fill" aria-hidden="true"></i><span class="table-action-label">Students</span>
                      </button>
                      <button type="button" class="btn btn-sm table-action-btn table-action-btn--enroll" onclick="assignStudents(<?= $section['id'] ?>, '<?= esc($section['section_name']) ?>', <?= $section['grade_level'] ?>)" title="Assign students">
                        <i class="bi bi-person-plus-fill" aria-hidden="true"></i><span class="table-action-label">Enroll</span>
                      </button>
                      <button type="button" class="btn btn-sm table-action-btn table-action-btn--subjects" onclick="viewSectionSubjects(<?= $section['id'] ?>, '<?= esc($section['section_name']) ?>', <?= $section['grade_level'] ?>)" title="View subjects">
                        <i class="bi bi-book" aria-hidden="true"></i><span class="table-action-label">Subjects</span>
                      </button>
                      <button type="button" class="btn btn-sm table-action-btn table-action-btn--edit" onclick="editSection(<?= $section['id'] ?>)" title="Edit section">
                        <i class="bi bi-pencil" aria-hidden="true"></i><span class="table-action-label">Edit</span>
                      </button>
                      <button type="button" class="btn btn-sm table-action-btn table-action-btn--delete" onclick="deleteSection(<?= $section['id'] ?>, '<?= esc($section['section_name']) ?>', <?= $section['current_enrollment'] ?>)" title="Delete section">
                        <i class="bi bi-trash" aria-hidden="true"></i><span class="table-action-label">Delete</span>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <div class="card">
    <div class="card-body text-center py-5">
      <i class="bi bi-building fs-1 text-muted mb-3"></i>
      <h5 class="text-muted">No sections found</h5>
      <p class="text-muted">Create your first section to get started with class management.</p>
      <button class="btn btn-primary" onclick="showCreateSection()">
        <i class="bi bi-plus-circle"></i> Create First Section
      </button>
    </div>
  </div>
<?php endif; ?>

<!-- Assign Adviser Modal is generated dynamically and appended to <body> by JS -->

<!-- Section Students Modal is generated dynamically and appended to <body> by JS -->

<style>
/* Custom styling for assign students modal close button */
#assignStudentsModal .modal-footer .btn-danger {
  background-color: #dc3545 !important;
  border-color: #dc3545 !important;
  color: white !important;
}

#assignStudentsModal .modal-footer .btn-danger:hover {
  background-color: #c82333 !important;
  border-color: #c82333 !important;
  color: white !important;
}

/* Custom styling for confirmation modal cancel button */
#confirmationModal .modal-footer .btn-secondary {
  background-color: #6c757d !important;
  border-color: #6c757d !important;
  color: white !important;
}

#confirmationModal .modal-footer .btn-secondary:hover {
  background-color: #5a6268 !important;
  border-color: #545b62 !important;
  color: white !important;
}

/* Custom styling for edit section modal close button */
#editSectionModal .modal-footer .btn-secondary {
  background-color: #6c757d !important;
  border-color: #6c757d !important;
  color: white !important;
}

#editSectionModal .modal-footer .btn-secondary:hover {
  background-color: #5a6268 !important;
  border-color: #545b62 !important;
  color: white !important;
}

/* Custom styling for remove adviser modal cancel button */
#removeAdviserModal .modal-footer .btn-secondary {
  background-color: #495057 !important;
  border-color: #495057 !important;
  color: white !important;
}

#removeAdviserModal .modal-footer .btn-secondary:hover {
  background-color: #3d4043 !important;
  border-color: #3d4043 !important;
  color: white !important;
}
</style>

<script>
// Store sections data for quick access
const sectionsData = <?= json_encode($sections) ?>;
const availableTeachers = <?= json_encode($availableTeachers ?? []) ?>;
const gradeLevelOptions = <?= json_encode(grade_level_options()) ?>;
const gradeLevelLabels = <?= json_encode(grade_level_js_labels()) ?>;

function formatGradeLevel(gradeLevel) {
  return gradeLevelLabels[gradeLevel] || 'Grade ' + gradeLevel;
}

// DEBUG: Log available teachers data
console.log('Available teachers data:', availableTeachers);
console.log('Number of available teachers:', availableTeachers.length);
availableTeachers.forEach((teacher, index) => {
  console.log(`Teacher ${index + 1}:`, {
    id: teacher.id,
    name: `${teacher.first_name} ${teacher.last_name}`,
    email: teacher.email,
    assigned_section: teacher.assigned_section,
    assigned_grade: teacher.assigned_grade
  });
});

function buildAssignTeachersModal({ sectionId, sectionName, gradeLevel }) {
  const existing = document.getElementById('assignTeachersModal');
  if (existing) existing.remove();

  const modalHtml = `
  <div class="modal fade" id="assignTeachersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Assign Teachers - ${sectionName}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-4 p-3 bg-light rounded">
            <h6 class="mb-1">${sectionName}</h6>
            <small class="text-muted">${formatGradeLevel(gradeLevel)} • School Year <?= date('Y') . '-' . (date('Y') + 1) ?></small>
          </div>
          
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#adviserTab">Section Adviser</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#subjectTeachersTab">Subject Teachers</button>
            </li>
          </ul>
          
          <div class="tab-content">
            <div class="tab-pane fade show active" id="adviserTab">
              <form id="assignAdviserForm" method="post" action="<?= base_url('admin/sections/assign-adviser/') ?>${sectionId}">
                <?= csrf_field() ?>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Select Section Adviser</label>
                  <select name="adviser_id" class="form-select" required>
                    <option value="">Choose a teacher...</option>
                    ${ (availableTeachers || []).map(t => `<option value="${t.id}">${t.first_name} ${t.last_name}${t.email ? ` (${t.email})` : ''}</option>`).join('') }
                  </select>
                  <div class="form-text">The section adviser manages the class and students</div>
                </div>
                <button type="submit" class="btn btn-success w-100">
                  <i class="bi bi-person-check"></i> Assign Adviser
                </button>
              </form>
            </div>
            
            <div class="tab-pane fade" id="subjectTeachersTab">
              <div id="subjectTeachersContent">
                <div class="text-center py-4">
                  <div class="spinner-border" role="status"></div>
                  <p class="mt-2">Loading subjects...</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057;">Close</button>
        </div>
      </div>
    </div>
  </div>`;

  document.body.insertAdjacentHTML('beforeend', modalHtml);
  return document.getElementById('assignTeachersModal');
}

function assignAdviser(sectionId, sectionName, gradeLevel) {
  const modalEl = buildAssignTeachersModal({ sectionId, sectionName, gradeLevel });
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
  
  // Load subject teachers when tab is clicked
  document.querySelector('[data-bs-target="#subjectTeachersTab"]').addEventListener('click', () => {
    loadSubjectTeachersAssignment(sectionId, gradeLevel);
  });
}


function viewSectionTeachers(sectionId, sectionName) {
  const modalEl = buildSectionTeachersModal(sectionId, sectionName);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
  
  // Load teachers
  fetch(`<?= base_url('admin/sections/teachers/') ?>${sectionId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displaySectionTeachers(data.adviser, data.subjectTeachers, sectionId);
      } else {
        document.getElementById('sectionTeachersList').innerHTML = `<div class="alert alert-danger">${data.message || 'Failed to load teachers'}</div>`;
      }
    })
    .catch(error => {
      document.getElementById('sectionTeachersList').innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}

function buildSectionTeachersModal(sectionId, sectionName) {
  const existing = document.getElementById('sectionTeachersModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="sectionTeachersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-people me-2"></i>${sectionName} - Teachers</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="sectionTeachersList">
            <div class="text-center py-4">
              <div class="spinner-border" role="status"></div>
              <p class="mt-2">Loading teachers...</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #6c757d; border-color: #6c757d;">Close</button>
        </div>
      </div>
    </div>
  </div>`;
  
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('sectionTeachersModal');
}

function displaySectionTeachers(adviser, subjectTeachers, sectionId) {
  let html = '';
  
  if (adviser) {
    html += `
      <div class="card mb-3">
        <div class="card-header bg-success text-white">
          <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Section Adviser</h6>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-1">${adviser.first_name} ${adviser.last_name}</h6>
              <small class="text-muted">${adviser.email || 'No email'}</small>
            </div>
            <button class="btn btn-danger btn-sm" onclick="removeAdviser(${sectionId}, '${adviser.first_name} ${adviser.last_name}')">
              <i class="bi bi-person-x me-1"></i>Remove
            </button>
          </div>
        </div>
      </div>
    `;
  }
  
  if (subjectTeachers && subjectTeachers.length > 0) {
    const teacherGroups = {};
    subjectTeachers.forEach(teacher => {
      const key = `${teacher.first_name}_${teacher.last_name}`;
      if (!teacherGroups[key]) {
        teacherGroups[key] = {
          first_name: teacher.first_name,
          last_name: teacher.last_name,
          email: teacher.email,
          schedules: []
        };
      }
      teacherGroups[key].schedules.push({
        subject_name: teacher.subject_name,
        day_of_week: teacher.day_of_week,
        start_time: teacher.start_time,
        end_time: teacher.end_time,
        schedule_id: teacher.schedule_id
      });
    });
    
    html += `
      <div class="card">
        <div class="card-header bg-info text-white">
          <h6 class="mb-0"><i class="bi bi-book me-2"></i>Subject Teachers</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Teacher</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
    `;
    
    Object.values(teacherGroups).forEach(teacherGroup => {
      const teacherKey = `${teacherGroup.first_name}_${teacherGroup.last_name}`.replace(/[^a-zA-Z0-9]/g, '_');
      const teacherSubjects = [...new Set(teacherGroup.schedules.map(s => s.subject_name).filter(s => s && s !== 'N/A'))];
      html += `
        <tr>
          <td>
            <div>${teacherGroup.first_name} ${teacherGroup.last_name}</div>
            <small class="text-muted">${teacherGroup.email || ''}</small>
          </td>
          <td>
            <button class="btn btn-primary btn-sm" onclick="toggleScheduleView('${teacherKey}')">
              <i class="bi bi-calendar3"></i> View Schedule
            </button>
            <button class="btn btn-info btn-sm ms-1" onclick="viewTeacherSubjectClasses('${teacherGroup.first_name}', '${teacherGroup.last_name}', ${JSON.stringify(teacherSubjects).replace(/"/g, '&quot;')})">
              <i class="bi bi-book"></i> View Subject Classes
            </button>
            <button class="btn btn-danger btn-sm ms-1" onclick="removeSelectedSchedules('${teacherKey}')">
              <i class="bi bi-trash"></i> Remove Selected
            </button>
          </td>
        </tr>
        <tr id="schedule_${teacherKey}" style="display: none;">
          <td colspan="2">
            <div class="p-2 bg-light">
              <table class="table table-sm table-bordered mb-0">
                <thead>
                  <tr>
                    <th><input class="form-check-input" type="checkbox" onchange="toggleTeacherSchedules('${teacherKey}', this)"></th>
                    <th>Subject</th>
                    <th>Day</th>
                    <th>Time</th>
                  </tr>
                </thead>
                <tbody>
      `;
      
      // Filter out schedules with no valid time and remove duplicates
      const validSchedules = teacherGroup.schedules.filter(schedule => {
        const startTime = schedule.start_time || '';
        const endTime = schedule.end_time || '';
        // Only show schedules that have valid times (not 00:00:00-00:00:00 and not empty)
        return !(startTime === '00:00:00' && endTime === '00:00:00') && startTime && endTime;
      });
      
      // Remove duplicates based on subject, day, and time
      const uniqueSchedules = validSchedules.filter((schedule, index, arr) => {
        return arr.findIndex(s => 
          s.subject_name === schedule.subject_name && 
          s.day_of_week === schedule.day_of_week && 
          s.start_time === schedule.start_time && 
          s.end_time === schedule.end_time
        ) === index;
      });
      
      uniqueSchedules.forEach(schedule => {
        const startTime = schedule.start_time || '';
        const endTime = schedule.end_time || '';
        
        html += `
                  <tr>
                    <td><input class="form-check-input schedule-checkbox" type="checkbox" value="${schedule.schedule_id}"></td>
                    <td>${schedule.subject_name || 'N/A'}</td>
                    <td>${schedule.day_of_week || ''}</td>
                    <td><small>${startTime}-${endTime}</small></td>
                  </tr>
        `;
      });
      
      html += `
                </tbody>
              </table>
            </div>
          </td>
        </tr>
      `;
    });
    
    html += `
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }
  
  if (!adviser && (!subjectTeachers || subjectTeachers.length === 0)) {
    html = '<div class="alert alert-info">No teachers assigned to this section.</div>';
  }
  
  document.getElementById('sectionTeachersList').innerHTML = html;
}

function toggleScheduleView(teacherKey) {
  const scheduleRow = document.getElementById(`schedule_${teacherKey}`);
  if (scheduleRow) {
    scheduleRow.style.display = scheduleRow.style.display === 'none' ? 'table-row' : 'none';
  }
}

function toggleTeacherSchedules(teacherKey, selectAllCheckbox) {
  const checkboxes = document.querySelectorAll(`#schedule_${teacherKey} .schedule-checkbox`);
  checkboxes.forEach(checkbox => {
    checkbox.checked = selectAllCheckbox.checked;
  });
}

function removeSelectedSchedules(teacherKey) {
  const selected = Array.from(document.querySelectorAll(`#schedule_${teacherKey} .schedule-checkbox:checked`)).map(cb => cb.value);
  
  if (selected.length === 0) {
    showNotification('Please select at least one schedule to remove', 'warning');
    return;
  }
  
  showConfirmationModal(
    'Confirm Action',
    `Remove ${selected.length} selected schedule(s)?`,
    () => {
      let completed = 0;
      let failed = 0;
      
      selected.forEach(scheduleId => {
        fetch(`<?= base_url('admin/sections/remove-subject-teacher/') ?>${scheduleId}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) completed++;
          else failed++;
          
          if (completed + failed === selected.length) {
            if (completed > 0) {
              showNotification(`Successfully removed ${completed} schedule(s)`, 'success');
              setTimeout(() => location.reload(), 1000);
            }
            if (failed > 0) {
              showNotification(`Failed to remove ${failed} schedule(s)`, 'error');
            }
          }
        })
        .catch(error => {
          failed++;
          if (completed + failed === selected.length && failed > 0) {
            showNotification('Some removals failed', 'error');
          }
        });
      });
    }
  );
}

function viewTeacherSubjectClasses(firstName, lastName, subjects = null) {
  const teacherName = `${firstName} ${lastName}`;
  let teacherSchedules = [];
  
  // If subjects are passed directly, use them
  if (subjects && Array.isArray(subjects)) {
    teacherSchedules = subjects.map(subject => ({ subject: subject }));
  } else {
    // Fallback: Get all schedules from the teacher group data
    const teacherKey = `${firstName}_${lastName}`.replace(/[^a-zA-Z0-9]/g, '_');
    const scheduleTable = document.querySelector(`#schedule_${teacherKey}`);
    
    if (scheduleTable) {
      const scheduleRows = scheduleTable.querySelectorAll('tbody tr');
      scheduleRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
          const subject = cells[1].textContent.trim();
          
          if (subject && subject !== 'N/A' && subject !== '') {
            teacherSchedules.push({ subject: subject });
          }
        }
      });
    }
  }
  
  // Create and show modal
  const modalHtml = `
  <div class="modal fade" id="teacherSubjectClassesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-book me-2"></i>${teacherName} - Subject Classes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="teacherSubjectClassesList"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057; color: white;">Close</button>
        </div>
      </div>
    </div>
  </div>`;
  
  const existing = document.getElementById('teacherSubjectClassesModal');
  if (existing) existing.remove();
  
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  const modal = new bootstrap.Modal(document.getElementById('teacherSubjectClassesModal'));
  modal.show();
  
  
  // Display the schedules
  displayTeacherSubjectClassesFromSchedules(teacherSchedules);
}

function displayTeacherSubjectClassesFromSchedules(schedules) {
  if (!schedules || schedules.length === 0) {
    document.getElementById('teacherSubjectClassesList').innerHTML = `
      <div class="text-center py-4">
        <i class="bi bi-book fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No Subject Classes</h6>
        <p class="text-muted">This teacher is not assigned to any subjects.</p>
      </div>
    `;
    return;
  }
  
  // Extract unique subjects only
  const uniqueSubjects = [...new Set(schedules.map(schedule => schedule.subject))];
  
  let html = '<div class="row">';
  
  uniqueSubjects.forEach(subject => {
    html += `
      <div class="col-md-6 mb-3">
        <div class="card">
          <div class="card-body text-center">
            <h6 class="card-title">${subject}</h6>
          </div>
        </div>
      </div>
    `;
  });
  
  html += '</div>';
  html += `<div class="mt-3"><small class="text-muted">Total: ${uniqueSubjects.length} subject(s)</small></div>`;
  
  document.getElementById('teacherSubjectClassesList').innerHTML = html;
}

function removeAdviser(sectionId, teacherName) {
  showConfirmationModal(
    'Confirm Action',
    `Remove ${teacherName} from this section?`,
    () => {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `<?= base_url('admin/sections/remove-adviser/') ?>${sectionId}`;
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '<?= csrf_token() ?>';
      csrfInput.value = '<?= csrf_hash() ?>';
      form.appendChild(csrfInput);
      document.body.appendChild(form);
      form.submit();
    }
  );
}

function removeSubjectTeacher(scheduleId, teacherName) {
  showConfirmationModal(
    'Confirm Action',
    `Remove ${teacherName} from this section?`,
    () => {
      fetch(`<?= base_url('admin/sections/remove-subject-teacher/') ?>${scheduleId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Teacher removed successfully', 'success');
          bootstrap.Modal.getInstance(document.getElementById('sectionTeachersModal')).hide();
          setTimeout(() => location.reload(), 1000);
        } else {
          showNotification(data.message || 'Failed to remove teacher', 'error');
        }
      })
      .catch(error => {
        showNotification('Error: ' + error.message, 'error');
      });
    }
  );
}



function viewSectionStudents(sectionId) {
  const section = sectionsData.find(s => s.id == sectionId);

  if (!section) {
    alert('Section not found.');
    return;
  }

  // Remove any existing modal first
  const existing = document.getElementById('sectionStudentsModal');
  if (existing) existing.remove();

  // Build and show modal dynamically
  const modalEl = buildSectionStudentsModal(section.section_name);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });

  // Show loading
  const listEl = modalEl.querySelector('#sectionStudentsList');
  listEl.innerHTML = `
    <div class="text-center">
      <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
      <p class="mt-2">Loading students...</p>
    </div>
  `;

  modal.show();

  // Fetch students via AJAX
  fetch(`<?= base_url('admin/sections/students/') ?>${sectionId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displaySectionStudents(data.students);
      } else {
        document.getElementById('sectionStudentsList').innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> ${data.message || 'Failed to load students'}
          </div>
        `;
      }
    })
    .catch(error => {
      document.getElementById('sectionStudentsList').innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle"></i> Error loading students: ${error.message}
        </div>
      `;
    });
}

function buildSectionStudentsModal(sectionName) {
  const existing = document.getElementById('sectionStudentsModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="sectionStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">${sectionName} Students</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="sectionStudentsList"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="removeSelectedStudents()"><i class="bi bi-trash"></i> Remove Selected</button>
        <button type="button" class="btn" data-bs-dismiss="modal" style="background-color: #6c757d !important; border-color: #6c757d !important; color: white !important;">Close</button>
      </div>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('sectionStudentsModal');
}

function displaySectionStudents(students) {
  if (students.length === 0) {
    document.getElementById('sectionStudentsList').innerHTML = `
      <div class="text-center py-4">
        <i class="bi bi-people fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No students enrolled</h6>
        <p class="text-muted">This section doesn't have any students yet.</p>
      </div>
    `;
    return;
  }

  let studentsHtml = `
    <div class="mb-3 d-flex justify-content-between align-items-center">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="selectAllEnrolled" onchange="toggleAllEnrolledStudents()">
        <label class="form-check-label fw-semibold" for="selectAllEnrolled">
          Select All (${students.length} students)
        </label>
      </div>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeSelectedStudents()"><i class="bi bi-trash"></i> Remove Selected</button>
    </div>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th width="50">Select</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Enrolled Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
  `;

  students.forEach(student => {
    studentsHtml += `
      <tr>
        <td>
          <div class="form-check">
            <input class="form-check-input enrolled-student-checkbox" type="checkbox" value="${student.id}">
          </div>
        </td>
        <td>${student.lrn || student.student_id || 'N/A'}</td>
        <td>${student.first_name} ${student.last_name}</td>
        <td><span class="badge bg-success">Enrolled</span></td>
        <td>${student.created_at ? new Date(student.created_at).toLocaleDateString() : 'N/A'}</td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="removeStudentFromSection(${student.id}, '${student.first_name.replace(/'/g, '\\\'')} ${student.last_name.replace(/'/g, '\\\'')}')">Remove</button>
        </td>
      </tr>
    `;
  });

  studentsHtml += `
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <small class="text-muted">Total: ${students.length} students</small>
    </div>
  `;

  document.getElementById('sectionStudentsList').innerHTML = studentsHtml;
}

function toggleAllEnrolledStudents() {
  const selectAll = document.getElementById('selectAllEnrolled');
  const checkboxes = document.querySelectorAll('.enrolled-student-checkbox');
  checkboxes.forEach(checkbox => {
    checkbox.checked = selectAll.checked;
  });
}

function removeSelectedStudents() {
  const selected = Array.from(document.querySelectorAll('.enrolled-student-checkbox:checked')).map(cb => cb.value);
  if (selected.length === 0) {
    showNotification('Please select at least one student to remove', 'warning');
    return;
  }
  showConfirmationModal(
    'Confirm Action',
    `Remove ${selected.length} selected student(s) from this section?`,
    () => {
      fetch(`<?= base_url('admin/sections/remove-students-bulk') ?>`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        },
        body: JSON.stringify({ student_ids: selected })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(data.message || 'Students removed successfully', 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showNotification(data.error || 'Failed to remove students', 'error');
        }
      })
      .catch(error => {
        showNotification('Error removing students: ' + error.message, 'error');
      });
    }
  );
}

function buildEditSectionModal(section) {
  const existing = document.getElementById('editSectionModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="<?= base_url('admin/sections/update/') ?>${section.id}">
        <?= str_replace(["\n","\r"], '', csrf_field()) ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Section Name</label>
            <input type="text" name="section_name" class="form-control" required value="${section.section_name || ''}">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Grade Level</label>
              <select name="grade_level" class="form-select" required>
                ${gradeLevelOptions.map(g => `<option value="${g}" ${section.grade_level == g ? 'selected' : ''}>${gradeLevelLabels[g] || g}</option>`).join('')}
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">School Year</label>
              <input type="text" name="school_year" class="form-control" value="${section.school_year}" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Max Capacity</label>
            <input type="number" name="max_capacity" class="form-control" min="1" required value="${section.max_capacity || 40}">
            <div class="form-text">Current: ${section.current_enrollment || 0} students</div>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="isActiveSwitch" name="is_active" ${section.is_active ? 'checked' : ''}>
            <label class="form-check-label" for="isActiveSwitch">Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
        </div>
      </form>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('editSectionModal');
}

function editSection(sectionId) {
  const section = sectionsData.find(s => s.id == sectionId);
  if (!section) { alert('Section not found.'); return; }
  const modalEl = buildEditSectionModal(section);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
}

// Handle form submissions
document.addEventListener('submit', function(e) {
  if (e.target.id === 'createSectionForm') {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        return response.text().then(text => {
          console.log('Error response:', text);
          throw new Error(`HTTP ${response.status}: ${text}`);
        });
      }
      return response.text();
    })
    .then(html => {
      console.log('Response HTML:', html.substring(0, 500));
      if (html.includes('alert-success') || html.includes('successfully') || html.includes('Section created')) {
        bootstrap.Modal.getInstance(document.getElementById('createSectionModal')).hide();
        showNotification('Section created successfully!', 'success');
        setTimeout(() => location.reload(), 1000);
      } else if (html.includes('alert-danger') || html.includes('error') || html.includes('validation')) {
        const errorMatch = html.match(/alert-danger[^>]*>([^<]+)</i) || html.match(/error[^>]*>([^<]+)</i);
        const errorMsg = errorMatch ? errorMatch[1].trim() : 'Failed to create section. Please check your input.';
        showNotification(errorMsg, 'error');
      } else {
        // If we can't determine success/failure from HTML, reload to see the result
        showNotification('Section creation submitted. Refreshing page...', 'info');
        setTimeout(() => location.reload(), 1000);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Error: ' + error.message, 'error');
    })
    .finally(() => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  } else if (e.target.id === 'assignAdviserForm' || e.target.id === 'assignSubjectTeacherForm') {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    const adviserSelect = form.querySelector('select[name="adviser_id"]');
    const adviserId = adviserSelect ? adviserSelect.value : '';
    
    if (!adviserId) {
      alert('Please select a teacher to assign as adviser.');
      return;
    }
    
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Assigning...';
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      return response.text();
    })
    .then(html => {
      // Check if response contains success or error message
      if (html.includes('alert-success') || html.includes('successfully')) {
        const modalId = e.target.id === 'assignAdviserForm' ? 'assignTeachersModal' : 'assignSubjectTeacherModal';
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modalInstance) modalInstance.hide();
        showNotification('Teacher successfully assigned as section adviser!', 'success');
        setTimeout(() => location.reload(), 1000);
      } else if (html.includes('alert-danger') || html.includes('error')) {
        const errorMatch = html.match(/alert-danger[^>]*>([^<]+)</i);
        const errorMsg = errorMatch ? errorMatch[1].trim() : 'Failed to assign teacher as adviser.';
        showNotification(errorMsg, 'error');
      } else {
        // Fallback - reload page to show server response
        location.reload();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Network error occurred. Please try again.', 'error');
    })
    .finally(() => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  }
});

// Show notification function
function showNotification(message, type = 'info') {
  const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : type === 'warning' ? 'alert-warning' : 'alert-info';
  const iconClass = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-triangle' : type === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle';
  
  const notification = document.createElement('div');
  notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
  notification.style.cssText = 'top: 20px; right: 20px; z-index: 999999; min-width: 300px;';
  notification.innerHTML = `
    <i class="bi ${iconClass} me-2"></i>${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  
  document.body.appendChild(notification);
  
  // Auto-remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 5000);
}

let currentGradeLevel = null;
let currentSectionId = null;
let allStudents = []; // Store all students for searching

function loadAllStudentsInitially() {
  loadStudentsPage(1, '');
}



function assignStudents(sectionId, sectionName, gradeLevel) {
  try {
    // Validate parameters
    if (!sectionId || !sectionName || !gradeLevel) {
      console.error('Invalid parameters:', { sectionId, sectionName, gradeLevel });
      showNotification('Invalid section information', 'error');
      return;
    }
    
    if (gradeLevel < 1 || gradeLevel > 6) {
      console.error('Invalid grade level:', gradeLevel);
      showNotification('Invalid grade level', 'error');
      return;
    }
    
    // Reset search state
    currentSearchTerm = '';
    currentGradeLevel = gradeLevel;
    currentSectionId = sectionId;
    
    console.log('Opening assign students modal for:', { sectionId, sectionName, gradeLevel });
    
    const modalEl = buildAssignStudentsModal({ sectionId, sectionName, gradeLevel });
    if (!modalEl) {
      throw new Error('Failed to create modal');
    }
    
    const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
    modal.show();
    
    // Load first page of students after modal is shown
    setTimeout(() => {
      loadAllStudentsInitially();
    }, 100);
    
  } catch (error) {
    console.error('Error in assignStudents:', error);
    showNotification('Error opening student assignment modal', 'error');
  }
}

function loadStudentsPage(page = 1, search = '') {
  const listEl = document.getElementById('unassignedStudentsList');
  
  if (!listEl) {
    console.error('Student list element not found');
    return;
  }
  
  if (!currentGradeLevel) {
    console.error('Grade level not set');
    listEl.innerHTML = `
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> Error: Grade level not specified
      </div>
    `;
    return;
  }
  
  // Update current search term
  if (search !== undefined) {
    currentSearchTerm = search;
  }
  
  // Show loading state
  listEl.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
      <p class="mt-2">Loading students${currentSearchTerm ? ` matching "${currentSearchTerm}"` : ''}...</p>
    </div>
  `;
  
  const params = new URLSearchParams({
    page: page,
    ...(currentSearchTerm && { search: currentSearchTerm })
  });
  
  const url = `<?= base_url('admin/sections/unassigned-students/') ?>${currentGradeLevel}?${params}`;
  console.log('Fetching URL:', url);
  console.log('Current search term:', currentSearchTerm);
  console.log('Page:', page);
  
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
  
  fetch(url, { signal: controller.signal })
    .then(response => {
      clearTimeout(timeoutId);
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      return response.json();
    })
    .then(data => {
      console.log('API Response:', data);
      console.log('Search term sent:', currentSearchTerm);
      console.log('Search term received:', data.search_term);
      console.log('Students count:', data.students ? data.students.length : 0);
      console.log('Total students:', data.pagination ? data.pagination.totalStudents : 'N/A');
      
      if (data.success) {
        if (data.students && Array.isArray(data.students)) {
          // Always display the results, even if empty array
          displayUnassignedStudents(data.students, currentSectionId, data.pagination);
        } else {
          throw new Error('Invalid response format: students data missing or invalid');
        }
      } else {
        listEl.innerHTML = `
          <div class="mb-3">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="selectAllStudents" disabled>
                  <label class="form-check-label fw-semibold" for="selectAllStudents">
                    Select All (0 students)
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input-group">
                  <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or LRN..." onkeypress="if(event.key==='Enter'){event.preventDefault(); searchStudents();}">
                  <button class="btn btn-outline-primary" type="button" onclick="searchStudents()">
                    <i class="bi bi-search"></i> Search
                  </button>
                  ${currentSearchTerm ? '<button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"><i class="bi bi-x"></i></button>' : ''}
                </div>
              </div>
            </div>
          </div>
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> ${data.message || 'No unassigned students found for this grade level'}
          </div>
        `;
        
        // Restore search term
        const searchInput = document.getElementById('studentSearch');
        if (searchInput && currentSearchTerm) {
          searchInput.value = currentSearchTerm;
        }
      }
    })
    .catch(error => {
      clearTimeout(timeoutId);
      console.error('Fetch error:', error);
      
      let errorMessage = 'Unknown error occurred';
      if (error.name === 'AbortError') {
        errorMessage = 'Request timed out. Please try again.';
      } else if (error.message) {
        errorMessage = error.message;
      }
      
      listEl.innerHTML = `
        <div class="mb-3">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllStudents" disabled>
                <label class="form-check-label fw-semibold" for="selectAllStudents">
                  Select All (0 students)
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or LRN..." onkeypress="if(event.key==='Enter'){event.preventDefault(); searchStudents();}">
                <button class="btn btn-outline-primary" type="button" onclick="searchStudents()">
                  <i class="bi bi-search"></i> Search
                </button>
                <button class="btn btn-outline-secondary" type="button" onclick="loadStudentsPage(1, '')" title="Retry">
                  <i class="bi bi-arrow-clockwise"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle"></i> Error loading students: ${errorMessage}
          <br><small class="text-muted">Click the refresh button to try again.</small>
        </div>
      `;
      
      // Restore search term
      const searchInput = document.getElementById('studentSearch');
      if (searchInput && currentSearchTerm) {
        searchInput.value = currentSearchTerm;
      }
    });
}

function searchStudents() {
  const searchInput = document.getElementById('studentSearch');
  if (!searchInput) {
    console.error('Search input not found');
    return;
  }
  
  const searchTerm = searchInput.value.trim().toLowerCase();
  console.log('Searching for:', searchTerm);
  
  currentSearchTerm = searchTerm;
  
  if (!searchTerm) {
    loadStudentsPage(1, '');
    return;
  }
  
  // Use server-side search with pagination
  loadStudentsPage(1, searchTerm);
}





function displayFilteredStudents(students, searchTerm) {
  const listEl = document.getElementById('unassignedStudentsList');
  
  if (students.length === 0) {
    listEl.innerHTML = `
      <div class="mb-3">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="selectAllStudents" disabled>
              <label class="form-check-label fw-semibold" for="selectAllStudents">
                Select All (0 students)
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-group">
              <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or LRN..." value="${searchTerm}" onkeypress="if(event.key==='Enter'){event.preventDefault(); searchStudents();}">
              <button class="btn btn-outline-primary" type="button" onclick="searchStudents()">
                <i class="bi bi-search"></i> Search
              </button>
              <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"><i class="bi bi-x"></i></button>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center py-4">
        <i class="bi bi-search fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No students found matching "${searchTerm}"</h6>
        <p class="text-muted">Try a different search term or clear the search.</p>
      </div>
    `;
    return;
  }
  
  let studentsHtml = `
    <div class="mb-3">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAllStudents" onchange="toggleAllStudents()">
            <label class="form-check-label fw-semibold" for="selectAllStudents">
              Select All (${students.length} students)
            </label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="input-group">
            <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or LRN..." value="${searchTerm}" onkeypress="if(event.key==='Enter'){event.preventDefault(); searchStudents();}">
            <button class="btn btn-outline-primary" type="button" onclick="searchStudents()">
              <i class="bi bi-search"></i> Search
            </button>
            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"><i class="bi bi-x"></i></button>
          </div>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th width="50">Select</th>
            <th>LRN</th>
            <th>Name</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
  `;
  
  students.forEach(student => {
    studentsHtml += `
      <tr>
        <td>
          <div class="form-check">
            <input class="form-check-input student-checkbox" type="checkbox" value="${student.id}" id="student_${student.id}">
          </div>
        </td>
        <td>${student.lrn || 'N/A'}</td>
        <td>
          <div>${student.first_name} ${student.last_name}</div>
          <small class="text-muted">${formatGradeLevel(currentGradeLevel)}</small>
        </td>
        <td><span class="badge bg-warning">Unassigned</span></td>
      </tr>
    `;
  });
  
  studentsHtml += `
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <small class="text-muted">Found ${students.length} students matching "${searchTerm}"</small>
    </div>
  `;
  
  listEl.innerHTML = studentsHtml;
}

function filterStudentsOnFrontend(searchTerm) {
  const listEl = document.getElementById('unassignedStudentsList');
  const allRows = listEl.querySelectorAll('tbody tr');
  let visibleCount = 0;
  
  allRows.forEach(row => {
    const nameCell = row.cells[2]; // Name column
    const lrnCell = row.cells[1];  // LRN column
    
    if (nameCell && lrnCell) {
      const name = nameCell.textContent.toLowerCase();
      const lrn = lrnCell.textContent.toLowerCase();
      
      if (name.includes(searchTerm) || lrn.includes(searchTerm)) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    }
  });
  
  // Update the select all label
  const selectAllLabel = document.querySelector('label[for="selectAllStudents"]');
  if (selectAllLabel) {
    selectAllLabel.textContent = `Select All (${visibleCount} students)`;
  }
  
  // Show message if no results
  if (visibleCount === 0) {
    const tbody = listEl.querySelector('tbody');
    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center py-4">
            <i class="bi bi-search fs-1 text-muted mb-3"></i>
            <h6 class="text-muted">No students found matching "${searchTerm}"</h6>
            <p class="text-muted">Try a different search term.</p>
          </td>
        </tr>
      `;
    }
  }
}

let currentSearchTerm = '';

function clearSearch() {
  try {
    currentSearchTerm = '';
    allStudents = [];
    const searchInput = document.getElementById('studentSearch');
    if (searchInput) {
      searchInput.value = '';
    }
    console.log('Clearing search, loading paginated students');
    loadStudentsPage(1, '');
  } catch (error) {
    console.error('Error clearing search:', error);
  }
}

function buildAssignStudentsModal({ sectionId, sectionName, gradeLevel }) {
  const existing = document.getElementById('assignStudentsModal');
  if (existing) existing.remove();
  
  const html = `
  <div class="modal fade" id="assignStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign Students to ${sectionName}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <div class="p-3 bg-light rounded">
            <h6 class="mb-1">${sectionName}</h6>
            <small class="text-muted">${formatGradeLevel(gradeLevel)} • Unassigned students only</small>
          </div>
        </div>
        <div id="unassignedStudentsList"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="assignSelectedStudents(${sectionId})">
          <i class="bi bi-person-check"></i> Assign Selected
        </button>
      </div>
    </div></div>
  </div>`;
  
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('assignStudentsModal');
}

function displayUnassignedStudents(students, sectionId, pagination = null) {
  const listEl = document.getElementById('unassignedStudentsList');
  
  // Check if backend says there are 0 total students (search found nothing)
  if (pagination && pagination.totalStudents === 0) {
    const searchTerm = currentSearchTerm ? ` matching "${currentSearchTerm}"` : '';
    listEl.innerHTML = `
      <div class="mb-3">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="selectAllStudents" disabled>
              <label class="form-check-label fw-semibold" for="selectAllStudents">
                Select All (0 students)
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-group">
              <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or LRN..." onkeypress="if(event.key==='Enter'){event.preventDefault(); searchStudents();}">
              <button class="btn btn-outline-primary" type="button" onclick="searchStudents()">
                <i class="bi bi-search"></i> Search
              </button>
              ${currentSearchTerm ? '<button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"><i class="bi bi-x"></i></button>' : ''}
            </div>
          </div>
        </div>
      </div>
      <div class="text-center py-4">
        <i class="bi bi-people fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No students found${searchTerm}</h6>
        <p class="text-muted">${currentSearchTerm ? 'Try a different search term or clear the search.' : 'All students in this grade level are already assigned to sections.'}</p>
        ${currentSearchTerm ? '<button class="btn btn-outline-secondary btn-sm" onclick="clearSearch()">Clear Search</button>' : ''}
      </div>
    `;
    
    // Restore search term in input field
    const searchInput = document.getElementById('studentSearch');
    if (searchInput && currentSearchTerm) {
      searchInput.value = currentSearchTerm;
    }
    return;
  }
  
  // Fallback check for empty students array
  if (students.length === 0) {
    const searchTerm = currentSearchTerm ? ` matching "${currentSearchTerm}"` : '';
    listEl.innerHTML = `
      <div class="mb-3">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="selectAllStudents" disabled>
              <label class="form-check-label fw-semibold" for="selectAllStudents">
                Select All (0 students)
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-group">
              <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or LRN..." onkeypress="if(event.key==='Enter'){event.preventDefault(); searchStudents();}">
              <button class="btn btn-outline-primary" type="button" onclick="searchStudents()">
                <i class="bi bi-search"></i> Search
              </button>
              ${currentSearchTerm ? '<button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"><i class="bi bi-x"></i></button>' : ''}
            </div>
          </div>
        </div>
      </div>
      <div class="text-center py-4">
        <i class="bi bi-people fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No students found${searchTerm}</h6>
        <p class="text-muted">${currentSearchTerm ? 'Try a different search term.' : 'All students in this grade level are already assigned to sections.'}</p>
      </div>
    `;
    
    // Restore search term in input field
    const searchInput = document.getElementById('studentSearch');
    if (searchInput && currentSearchTerm) {
      searchInput.value = currentSearchTerm;
    }
    return;
  }
  
  let studentsHtml = `
    <div class="mb-3">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAllStudents" onchange="toggleAllStudents()">
            <label class="form-check-label fw-semibold" for="selectAllStudents">
              Select All (${students.length} students)
            </label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="input-group">
            <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or LRN..." onkeypress="if(event.key==='Enter'){event.preventDefault(); searchStudents();}">
            <button class="btn btn-outline-primary" type="button" onclick="searchStudents()">
              <i class="bi bi-search"></i> Search
            </button>
            ${currentSearchTerm ? '<button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"><i class="bi bi-x"></i></button>' : ''}
          </div>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th width="50">Select</th>
            <th>LRN</th>
            <th>Name</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
  `;
  
  students.forEach(student => {
    studentsHtml += `
      <tr>
        <td>
          <div class="form-check">
            <input class="form-check-input student-checkbox" type="checkbox" value="${student.id}" id="student_${student.id}">
          </div>
        </td>
        <td>${student.lrn || 'N/A'}</td>
        <td>
          <div>${student.first_name} ${student.last_name}</div>
          <small class="text-muted">${formatGradeLevel(currentGradeLevel)}</small>
        </td>
        <td><span class="badge bg-warning">Unassigned</span></td>
      </tr>
    `;
  });
  
  studentsHtml += `
        </tbody>
      </table>
    </div>
  `;
  
  // Add pagination if provided
  if (pagination && pagination.totalPages > 1) {
    studentsHtml += `
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
          Showing ${((pagination.currentPage - 1) * pagination.perPage) + 1} to ${Math.min(pagination.currentPage * pagination.perPage, pagination.totalStudents)} of ${pagination.totalStudents} students${currentSearchTerm ? ` (filtered by "${currentSearchTerm}")` : ''}
        </div>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            ${pagination.currentPage > 1 ? `<li class="page-item"><a class="page-link" href="#" onclick="loadStudentsPage(${pagination.currentPage - 1}, currentSearchTerm)">Previous</a></li>` : ''}
            ${Array.from({length: Math.min(5, pagination.totalPages)}, (_, i) => {
              const page = Math.max(1, pagination.currentPage - 2) + i;
              if (page <= pagination.totalPages) {
                return `<li class="page-item ${page === pagination.currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="loadStudentsPage(${page}, currentSearchTerm)">${page}</a></li>`;
              }
              return '';
            }).join('')}
            ${pagination.currentPage < pagination.totalPages ? `<li class="page-item"><a class="page-link" href="#" onclick="loadStudentsPage(${pagination.currentPage + 1}, currentSearchTerm)">Next</a></li>` : ''}
          </ul>
        </nav>
      </div>
    `;
  }
  
  listEl.innerHTML = studentsHtml;
  
  // Restore search term in input field
  const searchInput = document.getElementById('studentSearch');
  if (searchInput && currentSearchTerm) {
    searchInput.value = currentSearchTerm;
  }
}

function toggleAllStudents() {
  const selectAll = document.getElementById('selectAllStudents');
  const checkboxes = document.querySelectorAll('.student-checkbox');
  
  checkboxes.forEach(checkbox => {
    checkbox.checked = selectAll.checked;
  });
}

function assignSelectedStudents(sectionId) {
  const selectedStudents = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
  
  if (selectedStudents.length === 0) {
    showStyledAlert('Please select at least one student to assign.', 'warning');
    return;
  }
  
  // Check section capacity
  const section = sectionsData.find(s => s.id == sectionId);
  if (section) {
    const availableSlots = section.max_capacity - section.current_enrollment;
    if (selectedStudents.length > availableSlots) {
      showStyledAlert(`Cannot assign ${selectedStudents.length} students. Only ${availableSlots} slots available in this section.`, 'error');
      return;
    }
  }
  
  // Send assignment request directly without confirmation
  fetch(`<?= base_url('admin/sections/assign-students/') ?>${sectionId}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    },
    body: JSON.stringify({ student_ids: selectedStudents })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification(data.message || 'Students assigned successfully!', 'success');
      // Clear selected checkboxes
      document.querySelectorAll('.student-checkbox:checked').forEach(cb => cb.checked = false);
      document.getElementById('selectAllStudents').checked = false;
      // Refresh the student list to remove assigned students
      setTimeout(() => {
        loadStudentsPage(1, currentSearchTerm);
      }, 1000);
      // Also reload the main page to update section enrollment counts
      setTimeout(() => location.reload(), 1500);
    } else {
      showNotification(data.message || 'Failed to assign students', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Network error occurred. Please try again.', 'error');
  });
}

function showStyledAlert(message, type = 'info') {
  const alertClass = type === 'warning' ? 'alert-warning' : type === 'error' ? 'alert-danger' : 'alert-info';
  const iconClass = type === 'warning' ? 'bi-exclamation-triangle' : type === 'error' ? 'bi-x-circle' : 'bi-info-circle';
  
  const modalHtml = `
  <div class="modal fade" id="styledAlertModal" tabindex="-1" style="z-index: 999999;">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body text-center p-4">
          <div class="${alertClass} border-0 mb-3">
            <i class="${iconClass} fs-2 mb-2"></i>
            <p class="mb-0">${message}</p>
          </div>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>`;
  
  const existing = document.getElementById('styledAlertModal');
  if (existing) existing.remove();
  
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  const modal = new bootstrap.Modal(document.getElementById('styledAlertModal'));
  modal.show();
  document.body.style.overflow = 'hidden';
}

function showConfirmationModal(title, message, onConfirm) {
  const modalHtml = `
  <div class="modal fade" id="confirmationModal" tabindex="-1" style="z-index: 999999;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">${title}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning border-0">
            <i class="bi bi-question-circle fs-2 mb-2 d-block text-center"></i>
            <p class="text-center mb-0">${message}</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="confirmAction()">Delete</button>
        </div>
      </div>
    </div>
  </div>`;
  
  const existing = document.getElementById('confirmationModal');
  if (existing) existing.remove();
  
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  
  window.confirmAction = () => {
    bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
    document.body.style.overflow = '';
    onConfirm();
  };
  
  const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
  modal.show();
  document.body.style.overflow = 'hidden';
  
  // Handle modal close events
  document.getElementById('confirmationModal').addEventListener('hidden.bs.modal', () => {
    document.body.style.overflow = '';
  });
}

function removeStudentFromSection(studentId, studentName) {
  showConfirmationModal(
    'Confirm Action',
    `Are you sure you want to remove ${studentName} from this section? This will unassign them from the section.`,
    () => {
      fetch(`<?= base_url('admin/sections/remove-student/') ?>${studentId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
      })
        .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(`${studentName} has been removed from the section.`, 'success');
          // Refresh the current modal view
          const modal = bootstrap.Modal.getInstance(document.getElementById('sectionStudentsModal'));
          if (modal) {
            const sectionId = currentSectionId || sectionsData.find(s => s.section_name)?.id;
            if (sectionId) {
              viewSectionStudents(sectionId);
            }
          }
          // Reload page after a short delay to update counts
          setTimeout(() => location.reload(), 1500);
        } else {
          showNotification(data.error || 'Failed to remove student. Please try again.', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
      });
    }
  );
}

function viewSectionSubjects(sectionId, sectionName, gradeLevel) {
  window.currentSectionId = sectionId;
  window.currentGradeLevel = gradeLevel;
  
  // Find section to check grading type
  const section = sectionsData.find(s => s.id == sectionId);
  const gradingType = section ? (section.grading_type || 'numerical') : 'numerical';
  
  // For non-numerical/custom sections, show domain management instead of subjects
  if (gradingType === 'non_numerical' || gradingType === 'custom') {
    window.location.href = `<?= base_url('teacher/sned/') ?>`;
    return;
  }
  
  const modalEl = buildSectionSubjectsModal(sectionName, gradeLevel);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  
  // Show loading
  const listEl = modalEl.querySelector('#sectionSubjectsList');
  listEl.innerHTML = `
    <div class="text-center">
      <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
      <p class="mt-2">Loading subjects...</p>
    </div>
  `;
  
  modal.show();
  
  // Fetch section-specific subjects
  fetch(`<?= base_url('admin/sections/subjects/') ?>${sectionId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displaySectionSubjects(data.subjects);
      } else {
        listEl.innerHTML = `
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No subjects assigned to this section yet.
          </div>
        `;
      }
    })
    .catch(error => {
      listEl.innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle"></i> Error loading subjects: ${error.message}
        </div>
      `;
    });
}

function buildSectionSubjectsModal(sectionName, gradeLevel) {
  const existing = document.getElementById('sectionSubjectsModal');
  if (existing) existing.remove();
  
  const html = `
  <div class="modal fade" id="sectionSubjectsModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-book me-2"></i>${sectionName} - ${formatGradeLevel(gradeLevel)} Subjects</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="sectionSubjectsList"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" onclick="addSubjectToSection()"><i class="bi bi-book me-1"></i>Add Subject</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057;">Close</button>
      </div>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('sectionSubjectsModal');
}

function displaySectionSubjects(subjects) {
  if (subjects.length === 0) {
    document.getElementById('sectionSubjectsList').innerHTML = `
      <div class="text-center py-4">
        <i class="bi bi-book fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No subjects found</h6>
        <p class="text-muted">No subjects are linked to this section yet.</p>
        <button class="btn btn-primary" onclick="addSubjectToSection()">
          <i class="bi bi-plus-circle me-1"></i>Add Subjects from Grade Level
          <i class="bi bi-plus-circle me-1"></i>Add Subjects from Grade Level
        </button>
      </div>
    `;
    return;
  }
  
  let subjectsHtml = `
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
  `;
  
  subjects.forEach(subject => {
    subjectsHtml += `
      <tr>
        <td><strong>${subject.subject_code}</strong></td>
        <td>${subject.subject_name}</td>
        <td><span class="badge bg-${subject.is_active == 1 ? 'success' : 'secondary'}">${subject.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
        <td>
          <button class="btn btn-primary btn-sm me-1" onclick="editSubject(${subject.id}, '${subject.subject_code.replace(/'/g, '\\\'')}', '${subject.subject_name.replace(/'/g, '\\\'')}', ${subject.is_active == 1}, ${subject.section_subject_id})" title="Edit Subject">Edit</button>
          <button class="btn btn-danger btn-sm" onclick="deleteSubject(${subject.id}, '${subject.subject_code.replace(/'/g, '\\\'')}')">Delete</button>
        </td>
      </tr>
    `;
  });
  
  subjectsHtml += `
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <small class="text-muted">Total: ${subjects.length} subjects</small>
    </div>
  `;
  
  document.getElementById('sectionSubjectsList').innerHTML = subjectsHtml;
}

function addSubjectToSection() {
  const gradeLevel = window.currentGradeLevel;
  const sectionId = window.currentSectionId;
  
  // Fetch currently assigned subjects first
  fetch(`<?= base_url('admin/sections/subjects/') ?>${sectionId}`)
    .then(r => r.json())
    .then(assignedData => {
      const assignedSubjectIds = assignedData.success && assignedData.subjects ? assignedData.subjects.map(s => s.id) : [];
      
      // Fetch all available subjects for this grade level from Settings
      fetch(`<?= base_url('admin/settings/get-grade-subjects/') ?>${gradeLevel}`)
        .then(r => r.json())
        .then(data => {
          if (!data.success || !data.subjects || data.subjects.length === 0) {
            showNotification('No subjects available for this grade level. Please add subjects in Settings first.', 'warning');
            return;
          }
          
          // Filter out already assigned subjects
          const availableSubjects = data.subjects.filter(s => !assignedSubjectIds.includes(s.id));
          
          if (availableSubjects.length === 0) {
            showNotification('All subjects are already assigned to this section.', 'info');
            return;
          }
          
          const subjectCheckboxes = availableSubjects.map(s => `
            <div class="form-check mb-2">
              <input class="form-check-input subject-checkbox" type="checkbox" value="${s.id}" id="subject_${s.id}">
              <label class="form-check-label" for="subject_${s.id}">
                <strong>${s.subject_code}</strong> - ${s.subject_name}
              </label>
            </div>
          `).join('');
          
          const modalHtml = `
          <div class="modal fade" id="addSubjectModal" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-book me-2"></i>Add Subjects - ${formatGradeLevel(gradeLevel)}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllSubjects" onchange="toggleAllSubjects()">
                    <label class="form-check-label fw-semibold" for="selectAllSubjects">
                      Select All (${availableSubjects.length} subjects)
                    </label>
                  </div>
                </div>
                <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                  ${subjectCheckboxes}
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057;">Cancel</button>
                <button type="button" class="btn btn-success" onclick="saveSelectedSubjects()"><i class="bi bi-check-circle me-1"></i>Add Selected</button>
              </div>
            </div></div>
          </div>`;
          
          const existing = document.getElementById('addSubjectModal');
          if (existing) existing.remove();
          document.body.insertAdjacentHTML('beforeend', modalHtml);
          new bootstrap.Modal(document.getElementById('addSubjectModal')).show();
        });
    });
}

function toggleAllSubjects() {
  const selectAll = document.getElementById('selectAllSubjects');
  const checkboxes = document.querySelectorAll('.subject-checkbox');
  checkboxes.forEach(checkbox => {
    checkbox.checked = selectAll.checked;
  });
}

function saveSelectedSubjects() {
  const selected = Array.from(document.querySelectorAll('.subject-checkbox:checked')).map(cb => cb.value);
  const sectionId = window.currentSectionId;
  
  if (selected.length === 0) {
    showNotification('Please select at least one subject', 'warning');
    return;
  }
  
  fetch(`<?= base_url('admin/sections/assign-subjects') ?>`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    },
    body: JSON.stringify({ section_id: sectionId, subject_ids: selected })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showNotification(`${selected.length} subject(s) added to this section`, 'success');
      bootstrap.Modal.getInstance(document.getElementById('addSubjectModal')).hide();
      setTimeout(() => {
        fetch(`<?= base_url('admin/sections/subjects/') ?>${sectionId}`)
          .then(r => r.json())
          .then(data => { if (data.success) displaySectionSubjects(data.subjects); });
      }, 500);
    } else {
      showNotification(data.message || 'Failed to add subjects', 'danger');
    }
  });
}

function editSubject(id, code, name, isActive, sectionSubjectId) {
  window.currentSectionSubjectId = sectionSubjectId;
  const modalHtml = `
  <div class="modal fade" id="editSubjectModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Subject</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form onsubmit="saveSubject(event, 'edit', null, ${id})">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Subject Code</label>
            <input type="text" class="form-control" id="subjectCode" value="${code}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Subject Name</label>
            <input type="text" class="form-control" id="subjectName" value="${name}" required>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="isActive" ${isActive ? 'checked' : ''}>
            <label class="form-check-label" for="isActive">Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057;">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Subject</button>
        </div>
      </form>
    </div></div>
  </div>`;
  
  const existing = document.getElementById('editSubjectModal');
  if (existing) existing.remove();
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  new bootstrap.Modal(document.getElementById('editSubjectModal')).show();
}

function deleteSubject(id, code) {
  showConfirmationModal(
    'Confirm Action',
    `Are you sure you want to delete subject "${code}"? This action cannot be undone.`,
    () => {
      fetch(`<?= base_url('admin/subjects/delete/') ?>${id}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Subject deleted successfully!', 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showNotification(data.error || 'Failed to delete subject', 'error');
        }
      })
      .catch(error => {
        showNotification('Error deleting subject', 'error');
      });
    }
  );
}

function saveSubject(event, action, gradeLevel, id = null) {
  event.preventDefault();
  
  const code = document.getElementById('subjectCode').value;
  const name = document.getElementById('subjectName').value;
  const isActive = document.getElementById('isActive').checked ? 1 : 0;
  
  const url = action === 'add' ? '<?= base_url('admin/subjects/add') ?>' : `<?= base_url('admin/subjects/edit/') ?>${id}`;
  const data = { subject_code: code, subject_name: name, is_active: isActive };
  if (action === 'add') data.grade_level = gradeLevel;
  if (action === 'edit' && window.currentSectionSubjectId) data.section_subject_id = window.currentSectionSubjectId;
  
  const formData = new FormData();
  Object.keys(data).forEach(key => formData.append(key, data[key]));
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
  
  fetch(url, {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification(`Subject ${action === 'add' ? 'added' : 'updated'} successfully!`, 'success');
      bootstrap.Modal.getInstance(document.getElementById(action === 'add' ? 'addSubjectModal' : 'editSubjectModal')).hide();
      // Refresh the subjects list
      setTimeout(() => {
        fetch(`<?= base_url('admin/sections/subjects/') ?>${window.currentSectionId}`)
          .then(response => response.json())
          .then(data => {
            if (data.success) displaySectionSubjects(data.subjects);
          });
      }, 500);
    } else {
      showNotification(data.error || `Failed to ${action} subject`, 'error');
    }
  })
  .catch(error => {
    showNotification(`Error ${action === 'add' ? 'adding' : 'updating'} subject`, 'error');
  });
}

function toggleCreateGradeLevelCustom(selectEl) {
  const wrap = document.getElementById('createGradeLevelCustomWrap');
  if (selectEl.value === '99') {
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
    document.getElementById('createGradeLevelCustom').value = '';
  }
}

function showBulkEditSections() {
  const modalEl = buildBulkEditSectionsModal();
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
}

function buildBulkEditSectionsModal() {
  const existing = document.getElementById('bulkEditSectionsModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="bulkEditSectionsModal" tabindex="-1">
    <div class="modal-dialog modal-xl"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Edit Sections</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          <strong>Quick Edit Mode:</strong> Select a grade level and school year to bulk edit all sections at once.
        </div>
        
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Select Grade Level</label>
            <select id="bulkEditGradeLevel" class="form-select" onchange="loadSectionsForBulkEdit()">
              <option value="">Select Grade Level</option>
              <?php foreach (grade_level_options() as $g): ?>
                <option value="<?= $g ?>"><?= esc(grade_level_label($g)) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Grading Type</label>
            <select id="bulkEditGradingType" class="form-select" onchange="updateGradingTypeForAllSections()">
              <option value="numerical">Numerical</option>
              <option value="non_numerical">Non-Numerical</option>
            </select>
            <div class="form-text">All sections in this grade level will use this grading type</div>
          </div>
        </div>

        <div id="bulkEditSectionsList">
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Please select a grade level first to load sections.
          </div>
        </div>
        
        <div class="mt-3">
          <button type="button" class="btn btn-info" onclick="manageGradingSymbols()">
            <i class="bi bi-symbols"></i> Manage Grading Symbols & Guide
          </button>
          <small class="text-muted d-block mt-1">Configure symbols and meanings for non-numerical grading</small>
        </div>
      </div>
    </div></div>
  </div>`;
  
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('bulkEditSectionsModal');
}

function loadSectionsForBulkEdit() {
  const gradeLevel = document.getElementById('bulkEditGradeLevel').value;
  const listEl = document.getElementById('bulkEditSectionsList');
  
  if (!gradeLevel) {
    listEl.innerHTML = `
      <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Please select a grade level first to load sections.
      </div>
    `;
    return;
  }

  const sections = sectionsData.filter(s => s.grade_level == gradeLevel);
  
  if (sections.length === 0) {
    listEl.innerHTML = `
      <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> No sections found for ${formatGradeLevel(gradeLevel)}.
      </div>
    `;
    return;
  }

  // Check if all sections have the same grading type
  const gradingTypes = [...new Set(sections.map(s => s.grading_type || 'numerical'))];
  const gradingTypeSelect = document.getElementById('bulkEditGradingType');
  if (gradingTypes.length === 1) {
    gradingTypeSelect.value = gradingTypes[0];
  }

  let html = `
    <div class="mb-3">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-semibold">School Year</label>
            <input type="text" id="bulkEditSchoolYear" class="form-control" value="${sections[0].school_year}">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label fw-semibold">Max Capacity</label>
            <input type="number" id="bulkEditMaxCapacity" class="form-control" value="${sections[0].max_capacity}" min="1">
          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Section Name</th>
            <th>Grading Type</th>
            <th>Current Enrollment</th>
            <th>Adviser</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
  `;

  sections.forEach(section => {
    html += `
      <tr>
        <td><strong>${section.section_name}</strong></td>
        <td>
          <span class="badge bg-${section.grading_type === 'non_numerical' ? 'success' : 'primary'}">
            ${section.grading_type === 'non_numerical' ? 'Non-Numerical' : 'Numerical'}
          </span>
        </td>
        <td>${section.current_enrollment}/${section.max_capacity}</td>
        <td>
          ${section.adviser_name 
            ? `<div class="text-success"><i class="bi bi-person-check"></i> ${section.adviser_name}</div>` 
            : `<div class="text-danger"><i class="bi bi-person-x"></i> No Adviser</div>`
          }
        </td>
        <td>
          <span class="badge bg-${section.is_active ? 'success' : 'secondary'}">
            ${section.is_active ? 'Active' : 'Inactive'}
          </span>
        </td>
      </tr>
    `;
  });

  html += `
        </tbody>
      </table>
    </div>

    <div class="mt-3 d-flex justify-content-between">
      <div class="form-text">
        <i class="bi bi-info-circle"></i> Editing ${sections.length} section(s) at once
      </div>
      <button type="button" class="btn btn-primary" onclick="saveBulkEdit()">
        <i class="bi bi-save"></i> Save Changes
      </button>
    </div>
  `;

  listEl.innerHTML = html;
  
  // Store current sections being edited
  window.currentBulkEditSections = sections;
}

function updateGradingTypeForAllSections() {
  const gradingType = document.getElementById('bulkEditGradingType').value;
  const sections = window.currentBulkEditSections || [];
  
  if (sections.length === 0) return;
  
  // Show confirmation
  if (!confirm(`Change all ${sections.length} sections to ${gradingType === 'non_numerical' ? 'Non-Numerical' : 'Numerical'} grading? This will update all sections in this grade level.`)) {
    // Revert the select
    const currentTypes = [...new Set(sections.map(s => s.grading_type || 'numerical'))];
    if (currentTypes.length === 1) {
      document.getElementById('bulkEditGradingType').value = currentTypes[0];
    }
    return;
  }
  
  let completed = 0;
  let failed = 0;
  let total = sections.length;
  
  sections.forEach(section => {
    const formData = new FormData();
    formData.append('section_name', section.section_name);
    formData.append('grade_level', section.grade_level);
    formData.append('school_year', section.school_year);
    formData.append('max_capacity', section.max_capacity);
    formData.append('grading_type', gradingType);
    formData.append('is_active', section.is_active ? '1' : '0');
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch(`<?= base_url('admin/sections/update/') ?>${section.id}`, {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        completed++;
      } else {
        failed++;
      }
      
      // Show notification when all requests complete
      if (completed + failed === total) {
        if (completed > 0 && failed === 0) {
          showNotification(`Successfully updated ${completed} section(s) to ${gradingType === 'non_numerical' ? 'Non-Numerical' : 'Numerical'} grading`, 'success');
          setTimeout(() => location.reload(), 1500);
        } else if (completed > 0 && failed > 0) {
          showNotification(`Updated ${completed} section(s), but ${failed} failed`, 'warning');
          setTimeout(() => location.reload(), 2000);
        } else {
          showNotification(`Failed to update ${failed} section(s)`, 'error');
        }
      }
    })
    .catch(error => {
      failed++;
      console.error('Update error:', error);
      
      // Show notification when all requests complete
      if (completed + failed === total) {
        if (completed > 0 && failed > 0) {
          showNotification(`Updated ${completed} section(s), but ${failed} failed`, 'warning');
          setTimeout(() => location.reload(), 2000);
        } else if (failed === total) {
          showNotification(`Failed to update ${failed} section(s)`, 'error');
        }
      }
    });
  });
}

function saveBulkEdit() {
  const schoolYear = document.getElementById('bulkEditSchoolYear').value;
  const maxCapacity = parseInt(document.getElementById('bulkEditMaxCapacity').value);
  const gradingType = document.getElementById('bulkEditGradingType').value;
  const sections = window.currentBulkEditSections || [];
  
  if (!schoolYear) {
    showNotification('Please enter a school year', 'warning');
    return;
  }
  
  if (!maxCapacity || maxCapacity < 1) {
    showNotification('Please enter a valid max capacity', 'warning');
    return;
  }
  
  let completed = 0;
  let failed = 0;
  let total = sections.length;
  
  sections.forEach(section => {
    const formData = new FormData();
    formData.append('section_name', section.section_name);
    formData.append('grade_level', section.grade_level);
    formData.append('school_year', schoolYear);
    formData.append('max_capacity', maxCapacity);
    formData.append('grading_type', gradingType);
    formData.append('is_active', section.is_active ? '1' : '0');
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch(`<?= base_url('admin/sections/update/') ?>${section.id}`, {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        completed++;
      } else {
        failed++;
      }
      
      // Show notification when all requests complete
      if (completed + failed === total) {
        if (completed > 0 && failed === 0) {
          showNotification(`Successfully updated ${completed} section(s)`, 'success');
          setTimeout(() => location.reload(), 1500);
        } else if (completed > 0 && failed > 0) {
          showNotification(`Updated ${completed} section(s), but ${failed} failed`, 'warning');
          setTimeout(() => location.reload(), 2000);
        } else {
          showNotification(`Failed to update ${failed} section(s)`, 'error');
        }
      }
    })
    .catch(error => {
      failed++;
      console.error('Update error:', error);
      
      // Show notification when all requests complete
      if (completed + failed === total) {
        if (completed > 0 && failed > 0) {
          showNotification(`Updated ${completed} section(s), but ${failed} failed`, 'warning');
          setTimeout(() => location.reload(), 2000);
        } else if (failed === total) {
          showNotification(`Failed to update ${failed} section(s)`, 'error');
        }
      }
    });
  });
}

function manageGradingSymbols() {
  const gradeLevel = document.getElementById('bulkEditGradeLevel').value;
  
  if (!gradeLevel) {
    showNotification('Please select a grade level first', 'warning');
    return;
  }
  
  // Open grading symbols management modal
  const modalHtml = `
  <div class="modal fade" id="gradingSymbolsModal" tabindex="-1">
    <div class="modal-dialog modal-xl"><div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="bi bi-symbols me-2"></i>Manage Grading Symbols - ${formatGradeLevel(gradeLevel)}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          <strong>Grading Guide:</strong> Define the symbols and their meanings for non-numerical grading in this grade level.
          These symbols will be used across all non-numerical sections.
        </div>
        
        <div class="mb-3">
          <h6 class="fw-semibold">Default Symbols:</h6>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Symbol</th>
                  <th>Label</th>
                  <th>Description / Meaning</th>
                  <th>Color</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong class="text-primary">P</strong></td>
                  <td>Proficient</td>
                  <td>The student consistently demonstrates the skill independently.</td>
                  <td><span class="badge bg-primary">Primary</span></td>
                </tr>
                <tr>
                  <td><strong class="text-success">AP</strong></td>
                  <td>Approaching Proficiency</td>
                  <td>The student is developing the skill with minimal assistance.</td>
                  <td><span class="badge bg-success">Success</span></td>
                </tr>
                <tr>
                  <td><strong class="text-warning">D</strong></td>
                  <td>Developing</td>
                  <td>The student is beginning to develop the skill with guidance.</td>
                  <td><span class="badge bg-warning">Warning</span></td>
                </tr>
                <tr>
                  <td><strong class="text-danger">B</strong></td>
                  <td>Beginning</td>
                  <td>The student needs significant support to develop the skill.</td>
                  <td><span class="badge bg-danger">Danger</span></td>
                </tr>
                <tr>
                  <td><strong class="text-secondary">NO/NA</strong></td>
                  <td>Not Observed / Not Applicable</td>
                  <td>The skill has not been observed or is not applicable at this time.</td>
                  <td><span class="badge bg-secondary">Secondary</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <div class="alert alert-warning">
          <i class="bi bi-lightbulb me-2"></i>
          <strong>Note:</strong> These are the standard DepEd symbols for non-numerical grading. 
          You can customize the labels and descriptions per section in the section edit modal.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div></div>
  </div>`;
  
  const existing = document.getElementById('gradingSymbolsModal');
  if (existing) existing.remove();
  
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  new bootstrap.Modal(document.getElementById('gradingSymbolsModal')).show();
}

function showCreateSection() {
  const modalEl = buildCreateSectionModal();
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
}

function deleteSection(sectionId, sectionName, currentEnrollment) {
  if (currentEnrollment > 0) {
    showNotification(`Cannot delete section "${sectionName}" because it has ${currentEnrollment} enrolled student(s). Please move students to other sections first.`, 'error');
    return;
  }
  
  showConfirmationModal(
    'Delete Section',
    `Are you sure you want to delete section "${sectionName}"? This action cannot be undone.`,
    () => {
      fetch(`<?= base_url('admin/sections/delete/') ?>${sectionId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification(`Section "${sectionName}" deleted successfully!`, 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showNotification(data.message || 'Failed to delete section', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Network error occurred. Please try again.', 'error');
      });
    }
  );
}

function autoAssignStudents(gradeLevel) {
  // Fetch unassigned students and section capacities
  fetch(`<?= base_url('admin/sections/auto-assign-preview/') ?>${gradeLevel}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showAutoAssignModal(gradeLevel, data);
      } else {
        showNotification(data.message || 'No unassigned students found for this grade level', 'info');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Error loading auto-assign preview', 'error');
    });
}

function showAutoAssignModal(gradeLevel, data) {
  const { students, sections, assignments } = data;
  
  let assignmentHtml = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Section</th><th>Current</th><th>Capacity</th><th>Will Assign</th><th>After</th></tr></thead><tbody>';
  
  assignments.forEach(assignment => {
    const section = sections.find(s => s.id === assignment.section_id);
    if (section) {
      assignmentHtml += `
        <tr>
          <td><strong>${section.section_name}</strong></td>
          <td>${section.current_enrollment}</td>
          <td>${section.max_capacity}</td>
          <td><span class="badge bg-primary">${assignment.count}</span></td>
          <td>${section.current_enrollment + assignment.count}/${section.max_capacity}</td>
        </tr>
      `;
    }
  });
  
  assignmentHtml += '</tbody></table></div>';
  
  const modalHtml = `
  <div class="modal fade" id="autoAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-distribute-vertical me-2"></i>Auto-Assign Students - ${formatGradeLevel(gradeLevel)}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          <strong>${students.length} unassigned students</strong> will be distributed evenly across sections based on available capacity.
        </div>
        <h6 class="mb-3">Assignment Preview:</h6>
        ${assignmentHtml}
        <div class="mt-3">
          <small class="text-muted"><i class="bi bi-lightbulb me-1"></i>Students will be assigned proportionally based on each section's available capacity.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057;">Cancel</button>
        <button type="button" class="btn btn-success" onclick="confirmAutoAssign(${gradeLevel})">
          <i class="bi bi-check-circle me-1"></i>Confirm & Assign (${students.length} students)
        </button>
      </div>
    </div></div>
  </div>`;
  
  const existing = document.getElementById('autoAssignModal');
  if (existing) existing.remove();
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  new bootstrap.Modal(document.getElementById('autoAssignModal')).show();
}

function confirmAutoAssign(gradeLevel) {
  const modal = bootstrap.Modal.getInstance(document.getElementById('autoAssignModal'));
  modal.hide();
  
  fetch(`<?= base_url('admin/sections/auto-assign-execute/') ?>${gradeLevel}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification(data.message || 'Students assigned successfully!', 'success');
      setTimeout(() => location.reload(), 1500);
    } else {
      showNotification(data.message || 'Failed to assign students', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Error executing auto-assign', 'error');
  });
}

function rebalanceGrade(gradeLevel) {
  if (confirm(`Rebalance all ${formatGradeLevel(gradeLevel)} students? This will redistribute all assigned students evenly across sections.`)) {
    window.location.href = `<?= base_url('admin/sections/rebalance/') ?>${gradeLevel}`;
  }
}

function loadSubjectTeachersAssignment(sectionId, gradeLevel) {
  const contentEl = document.getElementById('subjectTeachersContent');
  
  console.log('Loading subjects for section:', sectionId, 'grade:', gradeLevel);
  
  fetch(`<?= base_url('admin/sections/subjects/') ?>${sectionId}`)
    .then(r => r.json())
    .then(data => {
      console.log('Subjects API response:', data);
      
      if (!data.success || !data.subjects || data.subjects.length === 0) {
        console.warn('No subjects found. Response:', data);
        contentEl.innerHTML = `
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> No subjects found for this section.
            <br><small>Please add subjects to this section first from the main sections page.</small>
            <br><small class="text-muted">Debug: Section ID = ${sectionId}, Grade = ${gradeLevel}</small>
          </div>
        `;
        return;
      }
      
      // Fetch all teachers
      fetch(`<?= base_url('admin/sections/all-teachers') ?>`)
        .then(r => r.json())
        .then(teacherData => {
          console.log('Teachers API response:', teacherData);
          
          if (!teacherData.success) {
            console.error('Failed to load teachers:', teacherData);
            contentEl.innerHTML = '<div class="alert alert-danger">Failed to load teachers</div>';
            return;
          }
          
          // Fetch current assignments
          fetch(`<?= base_url('admin/sections/subject-assignments/') ?>${sectionId}`)
            .then(r => r.json())
            .then(assignData => {
              console.log('Assignments API response:', assignData);
              const assignments = assignData.success ? assignData.assignments : [];
              displaySubjectTeacherAssignment(data.subjects, teacherData.teachers, assignments, sectionId);
            });
        });
    })
    .catch(error => {
      console.error('Error loading subject teachers:', error);
      contentEl.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}

function displaySubjectTeacherAssignment(subjects, teachers, assignments, sectionId) {
  const contentEl = document.getElementById('subjectTeachersContent');
  
  // Get the section's adviser ID to exclude from subject teacher list
  const section = sectionsData.find(s => s.id == sectionId);
  const adviserId = section ? section.adviser_id : null;
  
  // Filter out the adviser from the teachers list
  const availableTeachers = adviserId ? teachers.filter(t => t.id != adviserId) : teachers;
  
  let html = '<form id="bulkAssignForm" onsubmit="assignMultipleTeachers(event, ' + sectionId + ')">';
  html += '<div class="list-group mb-3">';
  
  subjects.forEach(subject => {
    const assigned = assignments.find(a => a.subject_id == subject.id);
    
    html += `
      <div class="list-group-item">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="mb-1" style="font-size: 16px;">${subject.subject_name}</h6>
            <span class="text-muted" style="font-size: 14px;">${subject.subject_code}</span>
          </div>
          ${assigned ? `<span class="badge bg-success" style="font-size: 14px;">Assigned</span>` : `<span class="badge bg-warning" style="font-size: 14px;">Not Assigned</span>`}
        </div>
        
        ${assigned ? `
          <div class="alert alert-success mb-2 py-2">
            <strong style="font-size: 15px;">${assigned.teacher_name}</strong>
            <button type="button" class="btn btn-sm btn-danger float-end" onclick="removeSubjectTeacherAssignment(${assigned.schedule_id}, ${sectionId})">
              <i class="bi bi-x"></i> Remove
            </button>
          </div>
        ` : `
          <select name="teacher_${subject.id}" class="form-select" style="font-size: 15px; padding: 8px;" data-subject-id="${subject.id}">
            <option value="">Select teacher...</option>
            ${availableTeachers.map(t => `<option value="${t.id}">${t.first_name} ${t.last_name}</option>`).join('')}
          </select>
        `}
      </div>
    `;
  });
  
  html += '</div>';
  html += '<button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-circle me-2"></i>Assign Teachers</button>';
  html += '</form>';
  contentEl.innerHTML = html;
}

function assignSubjectTeacher(event, sectionId, subjectId) {
  event.preventDefault();
  const form = event.target;
  const formData = new FormData(form);
  formData.append('section_id', sectionId);
  formData.append('subject_id', subjectId);
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
  
  fetch('<?= base_url('admin/sections/assign-subject-teacher') ?>', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      showNotification('Teacher assigned successfully', 'success');
      loadSubjectTeachersAssignment(sectionId, window.currentGradeLevel);
    } else {
      showNotification(data.message || 'Failed to assign teacher', 'error');
    }
  })
  .catch(error => {
    showNotification('Error: ' + error.message, 'error');
  });
}

function assignMultipleTeachers(event, sectionId) {
  event.preventDefault();
  const form = event.target;
  const formData = new FormData(form);
  
  const assignments = [];
  for (let [key, value] of formData.entries()) {
    if (key.startsWith('teacher_') && value) {
      const subjectId = key.replace('teacher_', '');
      assignments.push({ subject_id: subjectId, teacher_id: value });
    }
  }
  
  if (assignments.length === 0) {
    showNotification('Please select at least one teacher to assign', 'warning');
    return;
  }
  
  let completed = 0;
  let failed = 0;
  
  assignments.forEach(assignment => {
    const data = new FormData();
    data.append('section_id', sectionId);
    data.append('subject_id', assignment.subject_id);
    data.append('teacher_id', assignment.teacher_id);
    data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch('<?= base_url('admin/sections/assign-subject-teacher-only') ?>', {
      method: 'POST',
      body: data
    })
    .then(r => r.json())
    .then(result => {
      if (result.success) completed++;
      else failed++;
      
      if (completed + failed === assignments.length) {
        if (completed > 0) {
          showNotification(`Successfully assigned ${completed} teacher(s)`, 'success');
          loadSubjectTeachersAssignment(sectionId, window.currentGradeLevel);
        }
        if (failed > 0) {
          showNotification(`Failed to assign ${failed} teacher(s)`, 'error');
        }
      }
    })
    .catch(error => {
      failed++;
      if (completed + failed === assignments.length && failed > 0) {
        showNotification('Some assignments failed', 'error');
      }
    });
  });
}

function removeSubjectTeacherAssignment(scheduleId, sectionId) {
  showConfirmationModal(
    'Confirm Action',
    'Remove this teacher assignment?',
    () => {
      fetch(`<?= base_url('admin/sections/remove-subject-teacher/') ?>${scheduleId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          showNotification('Teacher removed successfully', 'success');
          loadSubjectTeachersAssignment(sectionId, window.currentGradeLevel);
        } else {
          showNotification(data.message || 'Failed to remove teacher', 'error');
        }
      });
    }
  );
}

function buildCreateSectionModal() {
  const existing = document.getElementById('createSectionModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="createSectionModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Create New Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= base_url('admin/sections/create') ?>" id="createSectionForm">
        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Section Name *</label>
            <input type="text" name="section_name" class="form-control" required placeholder="e.g. 1-A, 1-B">
            <div class="form-text">Enter a unique name for the section</div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Grade Level *</label>
              <select name="grade_level" id="createGradeLevel" class="form-select" required onchange="toggleCreateGradeLevelCustom(this)">
                <option value="">Select Grade</option>
                <?php foreach (grade_level_options() as $g): ?>
                  <option value="<?= $g ?>"><?= esc(grade_level_label($g)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3" id="createGradeLevelCustomWrap" style="display:none;">
              <label class="form-label fw-semibold">Custom Grade/Class Name *</label>
              <input type="text" name="grade_level_custom" id="createGradeLevelCustom" class="form-control" placeholder="e.g. SPED, ALS, Transition">
              <div class="form-text">Enter a custom name for this section's grade/class level</div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">School Year *</label>
              <input type="text" name="school_year" class="form-control" value="<?= get_current_school_year() ?>" required readonly>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Grading Type *</label>
            <div class="d-flex gap-4 p-3 bg-light rounded">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="grading_type" id="gradingTypeNumerical" value="numerical" checked>
                <label class="form-check-label fw-semibold" for="gradingTypeNumerical">
                  <i class="bi bi-123 text-primary"></i> Numerical
                </label>
                <div class="form-text mt-0 ms-3">Subjects with numerical scores</div>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="grading_type" id="gradingTypeNonNumerical" value="non_numerical">
                <label class="form-check-label fw-semibold" for="gradingTypeNonNumerical">
                  <i class="bi bi-symbols text-success"></i> Non-Numerical
                </label>
                <div class="form-text mt-0 ms-3">Categories with custom symbols</div>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="grading_type" id="gradingTypeCustom" value="custom">
                <label class="form-check-label fw-semibold" for="gradingTypeCustom">
                  <i class="bi bi-gear text-info"></i> Custom
                </label>
                <div class="form-text mt-0 ms-3">User-defined grading system</div>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Max Capacity *</label>
            <input type="number" name="max_capacity" class="form-control" min="1" max="50" value="40" required>
            <div class="form-text">Maximum number of students (1-50)</div>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="isActiveCreate" name="is_active" checked>
            <label class="form-check-label" for="isActiveCreate">Active Section</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #495057; border-color: #495057;">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Section
          </button>
        </div>
      </form>
    </div></div>
  </div>`;
  
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('createSectionModal');
}
</script>

<?= $this->endSection() ?>