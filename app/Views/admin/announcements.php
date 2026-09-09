<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
/* Dashboard-style announcements page */
.announcement-card {
  transition: all 0.2s ease;
}

.announcement-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.status-badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}

.target-badge {
  font-size: 0.7rem;
  padding: 0.2rem 0.4rem;
}

.announcement-preview {
  max-height: 60px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.stats-card {
  border-radius: 12px;
  border: none;
}

.blue-divider {
  height: 3px;
  background: linear-gradient(90deg, #007bff, #0056b3);
  border-radius: 2px;
  margin: 1rem 0;
}
</style>

<!-- Header Section -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">Announcements & Notifications</h1>
      <p class="text-muted mb-0 small">Manage and publish announcements & notifications to your community</p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" style="padding: 12px 24px !important; font-size: 18px !important;" onclick="openCreateAnnouncementModal()">
        <i class="bi bi-plus-circle me-2"></i>New Announcement
      </button>
    </div>
  </div>

  <!-- Blue Divider -->
  <div class="blue-divider"></div>
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
<?php if ($error = session('error')): ?>
  <div class="alert alert-danger"><?= esc($error) ?></div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="d-flex gap-3 mb-4">
  <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
    <div class="card-body text-center p-3">
      <div class="stats-icon bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
        <i class="bi bi-megaphone text-white fs-5"></i>
      </div>
      <h4 class="stats-number text-primary mb-1 small" id="totalAnnouncements"><?= $stats['total'] ?></h4>
      <p class="stats-label text-muted fw-medium mb-0 small">Total Announcements & Notifications</p>
    </div>
  </div>

  <div class="stats-card bg-white border-0 shadow-sm rounded-3 flex-fill">
    <div class="card-body text-center p-3">
      <div class="stats-icon bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">
        <i class="bi bi-check-circle text-white fs-5"></i>
      </div>
      <h4 class="stats-number text-success mb-1 small" id="publishedAnnouncements"><?= $stats['published'] ?></h4>
      <p class="stats-label text-muted fw-medium mb-0 small">Published</p>
    </div>
  </div>


</div>

<!-- Blue Divider -->
<div class="blue-divider mb-4"></div>

<?php 
$analyticsReports = array_filter($announcements, function($announcement) {
    return strpos($announcement['title'], 'Class Analytics Report') !== false;
});
$regularAnnouncements = array_filter($announcements, function($announcement) {
    return strpos($announcement['title'], 'Class Analytics Report') === false;
});

// Helper function to get human-readable target name
function getTargetDisplayName($targetRoles) {
    if (strpos($targetRoles, 'section_') === 0) {
        $sectionId = str_replace('section_', '', $targetRoles);
        $db = \Config\Database::connect();
        $section = $db->table('sections')
            ->select('section_name, grade_level')
            ->where('id', $sectionId)
            ->get()
            ->getRow();
        return $section ? $section->section_name . ' (' . grade_level_label((int) $section->grade_level) . ')' : 'Section #' . $sectionId;
    } elseif (strpos($targetRoles, 'grade_') === 0) {
        // Check if multiple grades (comma-separated)
        if (strpos($targetRoles, ',') !== false) {
            $grades = explode(',', $targetRoles);
            $labels = array_map(function($g) {
                $gradeNum = (int) str_replace('grade_', '', $g);
                return grade_level_label($gradeNum);
            }, $grades);
            return implode(', ', $labels);
        } else {
            $grade = (int) str_replace('grade_', '', $targetRoles);
            return grade_level_label($grade);
        }
    }
    return ucfirst(str_replace('_', ' ', $targetRoles));
}
?>

<!-- Class Analytics Reports -->
<?php if (!empty($analyticsReports)): ?>
<div class="card bg-white border-0 shadow-sm rounded-3 mb-4">
  <div class="card-header bg-transparent border-0 p-3">
    <h4 class="card-title mb-0 small">
      <i class="bi bi-bar-chart me-2 text-success"></i>
      Class Analytics Reports & Notifications (<?= count($analyticsReports) ?>)
    </h4>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th class="border-0 fw-medium small">Title</th>
            <th class="border-0 fw-medium small">Preview</th>
            <th class="border-0 fw-medium small">Target</th>
            <th class="border-0 fw-medium small">Status</th>
            <th class="border-0 fw-medium small">Created</th>
            <th class="border-0 fw-medium small text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($analyticsReports as $announcement): ?>
            <tr>
              <td class="py-2">
                <div class="fw-medium text-dark small"><?= esc($announcement['title']) ?></div>
              </td>
              <td class="py-2">
                <div class="announcement-preview text-muted small">
                  <?= esc(substr(strip_tags($announcement['body']), 0, 100)) ?><?= strlen($announcement['body']) > 100 ? '...' : '' ?>
                </div>
              </td>
              <td class="py-2">
                <span class="badge bg-secondary target-badge"><?= esc(getTargetDisplayName($announcement['target_roles'])) ?></span>
              </td>
              <td class="py-2">
                <span class="badge bg-success status-badge">
                  <i class="bi bi-check-circle me-1"></i>Published
                </span>
              </td>
              <td class="py-2">
                <small class="text-muted"><?= date('M j, Y', strtotime($announcement['created_at'])) ?></small>
              </td>
              <td class="py-2 text-center">
                <div class="btn-group" role="group">
                  <a href="<?= base_url('admin/announcements/show/' . $announcement['id']) ?>"
                     class="btn btn-outline-info btn-sm" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="<?= base_url('admin/announcements/download-pdf/' . $announcement['id']) ?>"
                     class="btn btn-outline-success btn-sm" title="Download PDF">
                    <i class="bi bi-download"></i>
                  </a>
                  <button onclick="deleteAnnouncement(<?= $announcement['id'] ?>, '<?= esc($announcement['title']) ?>')"
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
  </div>
</div>
<?php endif; ?>

<!-- Regular Announcements -->
<div class="card bg-white border-0 shadow-sm rounded-3">
  <div class="card-header bg-transparent border-0 p-3">
    <h4 class="card-title mb-0 small">
      <i class="bi bi-list-ul me-2 dash-icon-inline"></i>
      Regular Announcements & Notifications (<?= count($regularAnnouncements) ?>)
    </h4>
  </div>
  <div class="card-body p-0">
    <?php if (!empty($regularAnnouncements)): ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th class="border-0 fw-medium small">Title</th>
              <th class="border-0 fw-medium small">Preview</th>
              <th class="border-0 fw-medium small">Target</th>
              <th class="border-0 fw-medium small">Status</th>
              <th class="border-0 fw-medium small">Created</th>
              <th class="border-0 fw-medium small text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($regularAnnouncements as $announcement): ?>
              <tr>
                <td class="py-2">
                  <div class="fw-medium text-dark small"><?= esc($announcement['title']) ?></div>
                </td>
                <td class="py-2">
                  <div class="announcement-preview text-muted small">
                    <?= esc(substr(strip_tags($announcement['body']), 0, 100)) ?><?= strlen($announcement['body']) > 100 ? '...' : '' ?>
                  </div>
                </td>
                <td class="py-2">
                  <span class="badge bg-secondary target-badge"><?= esc(getTargetDisplayName($announcement['target_roles'])) ?></span>
                </td>
                <td class="py-2">
                  <span class="badge bg-success status-badge">
                    <i class="bi bi-check-circle me-1"></i>Published
                  </span>
                </td>
                <td class="py-2">
                  <small class="text-muted"><?= date('M j, Y', strtotime($announcement['created_at'])) ?></small>
                </td>
                <td class="py-2 text-center">
                  <div class="btn-group" role="group">
                    <a href="<?= base_url('admin/announcements/show/' . $announcement['id']) ?>"
                       class="btn btn-outline-info btn-sm" title="View">
                      <i class="bi bi-eye"></i>
                    </a>
                    <button class="btn btn-outline-warning btn-sm" title="Edit" onclick="openEditAnnouncementModal(<?= $announcement['id'] ?>)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button onclick="deleteAnnouncement(<?= $announcement['id'] ?>, '<?= esc($announcement['title']) ?>')"
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
        <i class="bi bi-megaphone display-1 text-muted"></i>
        <h5 class="text-muted mt-3">No regular announcements yet</h5>
        <p class="text-muted">Create your first announcement to get started.</p>
        <button class="btn btn-primary" onclick="openCreateAnnouncementModal()">
          <i class="bi bi-plus-circle me-2"></i>Create Announcement
        </button>
      </div>
    <?php endif; ?>
  </div>
</div>


<script>
// Build Create Announcement Modal
function buildCreateAnnouncementModal() {
  const existing = document.getElementById('createAnnouncementModal');
  if (existing) existing.remove();
  const html = `
  <div class="modal fade" id="createAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-megaphone me-2"></i>New Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= base_url('admin/announcements/store') ?>">
        <?= str_replace(["\n","\r"], '', csrf_field()) ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" maxlength="255" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Target Audience</label>
              <select name="target_roles" id="modalTargetRoles" class="form-select" required>
                <option value="">Select target</option>
                <option value="all">All Users</option>
                <option value="admin">Administrators</option>
                <option value="teacher">All Teachers</option>
                <option value="student">All Students</option>
                <option value="specific_grade">Grade Level (Multiple)</option>
                <option value="specific_section">Section</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Priority</label>
              <select name="priority" class="form-select">
                <option value="normal">Normal</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
          </div>
          <div class="row" id="modalGradeLevelRow" style="display:none;">
            <div class="col-md-6 mb-3">
              <label class="form-label">Grade Level</label>
              <select name="grade_level" id="modalGradeLevel" class="form-select">
                <option value="">Select Grade</option>
                <?php foreach (grade_level_options() as $g): ?>
                  <option value="<?= $g ?>"><?= esc(grade_level_label($g)) ?></option>
                <?php endforeach; ?>
              </select>
              <div id="modalGradeLevelCheckboxes" class="mt-2" style="display:none; max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px; background-color: #f8f9fa;">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="grade_levels[]" value="0" id="grade0">
                  <label class="form-check-label small" for="grade0">Kindergarten</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="grade_levels[]" value="1" id="grade1">
                  <label class="form-check-label small" for="grade1">Grade 1</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="grade_levels[]" value="2" id="grade2">
                  <label class="form-check-label small" for="grade2">Grade 2</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="grade_levels[]" value="3" id="grade3">
                  <label class="form-check-label small" for="grade3">Grade 3</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="grade_levels[]" value="4" id="grade4">
                  <label class="form-check-label small" for="grade4">Grade 4</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="grade_levels[]" value="5" id="grade5">
                  <label class="form-check-label small" for="grade5">Grade 5</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="grade_levels[]" value="6" id="grade6">
                  <label class="form-check-label small" for="grade6">Grade 6</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="grade_levels[]" value="7" id="grade7">
                  <label class="form-check-label small" for="grade7">SNED (Special Needs Education)</label>
                </div>
              </div>
              <small class="form-text text-muted" id="gradeSelectHint">Select a single grade or use checkboxes for multiple grades</small>
            </div>
            <div class="col-md-6 mb-3" id="modalSectionCol" style="display:none;">
              <label class="form-label">Section</label>
              <select name="section_id" id="modalSectionId" class="form-select">
                <option value="">Select Section</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="body" class="form-control" rows="6" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" style="background-color: #6c757d; color: white;" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Publish</button>
        </div>
      </form>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('createAnnouncementModal');
}

function openCreateAnnouncementModal() {
  const el = buildCreateAnnouncementModal();
  const modal = new bootstrap.Modal(el, { backdrop: true, keyboard: true, focus: true });
  modal.show();
  
  // Setup event listeners after modal is shown
  setTimeout(() => {
    const targetRoles = document.getElementById('modalTargetRoles');
    const gradeLevel = document.getElementById('modalGradeLevel');
    
    if (targetRoles) {
      targetRoles.addEventListener('change', handleModalTargetChange);
    }
    if (gradeLevel) {
      gradeLevel.addEventListener('change', loadModalSections);
    }
  }, 100);
}

  function handleModalTargetChange() {
  const target = document.getElementById('modalTargetRoles').value;
  const gradeRow = document.getElementById('modalGradeLevelRow');
  const sectionCol = document.getElementById('modalSectionCol');
  const gradeSelect = document.getElementById('modalGradeLevel');
  const gradeCheckboxes = document.getElementById('modalGradeLevelCheckboxes');
  const gradeHint = document.getElementById('gradeSelectHint');
  const sectionSelect = document.getElementById('modalSectionId');
  
  gradeSelect.value = '';
  // Uncheck all grade checkboxes
  const checkboxes = document.querySelectorAll('input[name="grade_levels[]"]');
  checkboxes.forEach(cb => cb.checked = false);
  sectionSelect.value = '';
  sectionSelect.innerHTML = '<option value="">Select Section</option>';
  
  if (target === 'specific_grade') {
    gradeRow.style.display = 'flex';
    sectionCol.style.display = 'none';
    gradeSelect.required = false;
    gradeCheckboxes.style.display = 'block';
    gradeHint.style.display = 'block';
    sectionSelect.required = false;
  } else if (target === 'specific_section') {
    gradeRow.style.display = 'flex';
    sectionCol.style.display = 'block';
    gradeSelect.required = true;
    gradeCheckboxes.style.display = 'none';
    gradeHint.style.display = 'none';
    sectionSelect.required = true;
  } else {
    gradeRow.style.display = 'none';
    sectionCol.style.display = 'none';
    gradeSelect.required = false;
    gradeCheckboxes.style.display = 'none';
    gradeHint.style.display = 'none';
    sectionSelect.required = false;
  }
}

  function loadModalSections() {
  const gradeLevel = document.getElementById('modalGradeLevel').value;
  const sectionSelect = document.getElementById('modalSectionId');
  
  if (!gradeLevel) {
    sectionSelect.innerHTML = '<option value="">Select Section</option>';
    return;
  }
  
  sectionSelect.innerHTML = '<option value="">Loading...</option>';
  
  fetch(`<?= base_url('admin/announcements/get-sections') ?>?grade_level=${gradeLevel}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      console.log('Sections data:', data);
      if (data.success) {
        if (data.sections && data.sections.length > 0) {
          let options = '<option value="">Select Section</option>';
          data.sections.forEach(section => {
            options += `<option value="${section.id}">${section.section_name} (${formatGradeLevel(section.grade_level)})</option>`;
          });
          sectionSelect.innerHTML = options;
        } else {
          sectionSelect.innerHTML = '<option value="">No sections found for Grade ' + gradeLevel + '</option>';
        }
      } else {
        sectionSelect.innerHTML = '<option value="">Error: ' + (data.message || 'Unknown error') + '</option>';
        console.error('API Error:', data.message);
      }
    })
    .catch(error => {
      console.error('Fetch Error:', error);
      sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
    });
}

  function formatGradeLevel(gradeLevel) {
    if (gradeLevel == 0) return 'Kindergarten';
    if (gradeLevel == 7) return 'SNED';
    return 'Grade ' + gradeLevel;
  }

// Build Edit Modal
function buildEditAnnouncementModal(announcement) {
  const existing = document.getElementById('editAnnouncementModal');
  if (existing) existing.remove();
  const html = `
  <div class="modal fade" id="editAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= base_url('admin/announcements/update/') ?>${announcement.id}">
        <?= str_replace(["\n","\r"], '', csrf_field()) ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" maxlength="255" required value="${announcement.title || ''}">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Target Audience</label>
              <select name="target_roles" class="form-select" required>
                ${['all','admin','teacher','student','grade_0','grade_1','grade_2','grade_3','grade_4','grade_5','grade_6'].map(opt => {
                  const label = opt.startsWith('grade_') ? formatGradeLevel(opt.split('_')[1]) : (opt.charAt(0).toUpperCase()+opt.slice(1));
                  return `<option value="${opt}" ${announcement.target_roles===opt?'selected':''}>${label}</option>`;
                }).join('')}
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Published At</label>
              <input type="text" class="form-control" value="${announcement.published_at || ''}" readonly>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="body" class="form-control" rows="6" required>${announcement.body || ''}</textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn" style="background-color: #6c757d; color: white;" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
        </div>
      </form>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('editAnnouncementModal');
}

function openEditAnnouncementModal(id) {
  fetch('<?= base_url('admin/announcements/get/') ?>' + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
    .then(r => r.json())
    .then(data => {
      if (!data.success) { alert(data.message || 'Failed to load announcement'); return; }
      const el = buildEditAnnouncementModal(data.announcement);
      new bootstrap.Modal(el, { backdrop: true, keyboard: true, focus: true }).show();
    })
    .catch(err => alert('Error: ' + err.message));
}
</script>

<script>
// Delete announcement function
function deleteAnnouncement(id, title) {
  if (confirm(`Are you sure you want to delete the announcement "${title}"? This action cannot be undone.`)) {
    // Create a form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= base_url('admin/announcements/delete/') ?>' + id;

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
  fetch('<?= base_url('admin/announcements/getStats') ?>', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    document.getElementById('totalAnnouncements').textContent = data.total;
    document.getElementById('publishedAnnouncements').textContent = data.published;
  })
  .catch(error => {
    console.error('Error refreshing stats:', error);
  });
}

// Auto-refresh stats every 60 seconds
setInterval(refreshStats, 60000);
</script>

<?= $this->endSection() ?>
