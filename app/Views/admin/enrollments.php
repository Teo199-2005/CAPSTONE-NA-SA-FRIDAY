<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
.action-buttons {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
  align-items: center;
}

/* Enhanced Custom Modal Styles */
.custom-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(6px);
  animation: fadeIn 0.3s ease-out;
}

.custom-modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: transparent;
}

.custom-modal-content {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
  animation: slideIn 0.3s ease-out;
  border: 2px solid #e2e8f0;
}

.custom-modal-large {
  max-width: 900px;
}

.custom-modal-header {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 3px solid #1e40af;
}

.custom-modal-header.approval {
  background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  border-bottom-color: #059669;
}

.custom-modal-header.rejection {
  background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  border-bottom-color: #dc2626;
}

.custom-modal-header h5 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #ffffff !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.custom-modal-header h5 i {
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
}

.custom-modal-footer {
  background: #f1f5f9;
  padding: 1.5rem 2rem;
  border-top: 2px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

/* Loading Spinner */
.loading-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Student Info Sections */
.student-info-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 2px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.student-info-title {
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

.student-info-display {
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.student-info-display .student-name {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 0.5rem;
}

.student-info-display .student-meta {
  color: #6b7280;
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.student-info-display .student-status {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
}

.section-selection,
.rejection-reason {
  margin-bottom: 1.5rem;
}

.section-selection label,
.rejection-reason label {
  display: block;
  margin-bottom: 0.75rem;
  font-weight: 600;
  color: #374151;
  font-size: 1rem;
}

.form-control {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.95rem;
  background: #ffffff;
  transition: all 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-text {
  font-size: 0.875rem;
  color: #6b7280;
  margin-top: 0.5rem;
  font-style: italic;
}

.alert {
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
  border: 2px solid transparent;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

.alert-warning {
  color: #92400e;
  background-color: #fef3c7;
  border-color: #fbbf24;
}

.alert i {
  font-size: 1.2rem;
}

/* Document Viewer Modal */
.document-viewer-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.9);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.document-viewer-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.document-viewer-content {
  background: white;
  border-radius: 12px;
  max-width: 90vw;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
}

.document-viewer-header {
  background: #1e40af;
  color: white;
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.document-viewer-header h5 {
  margin: 0;
  color: white;
}

.document-viewer-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.document-viewer-body {
  padding: 1rem;
  text-align: center;
}

.document-viewer-image {
  max-width: 100%;
  max-height: 70vh;
  object-fit: contain;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: 2px solid transparent;
  border-radius: 8px;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-secondary {
  color: #6b7280;
  background-color: #f9fafb;
  border-color: #d1d5db;
}

.btn-secondary:hover {
  background-color: #f3f4f6;
  border-color: #9ca3af;
  color: #374151;
}

.btn-success {
  color: #fff;
  background: linear-gradient(135deg, #10b981, #059669);
  border-color: #059669;
}

.btn-success:hover {
  background: linear-gradient(135deg, #059669, #047857);
  border-color: #047857;
}

.btn-danger {
  color: #fff;
  background: linear-gradient(135deg, #ef4444, #dc2626);
  border-color: #dc2626;
}

.btn-danger:hover {
  background: linear-gradient(135deg, #dc2626, #b91c1c);
  border-color: #b91c1c;
}

/* Student Details Table */
.student-details-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.student-details-table td {
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: top;
}

.student-details-table td:first-child {
  font-weight: 600;
  color: #374151;
  width: 35%;
}

.student-details-table td:last-child {
  color: #6b7280;
}

.student-details-table tr:last-child td {
  border-bottom: none;
}

/* Ensure modal appears above everything */
.custom-modal {
  z-index: 999999 !important;
}

.custom-modal * {
  z-index: inherit !important;
}

/* Hide scrollbar on body when modal is open */
body.modal-open {
  overflow: hidden;
}

/* Ensure footer doesn't interfere */
.modern-footer {
  z-index: 1000 !important;
}

.app-sidebar-wrapper {
  z-index: 1030 !important;
}

/* Additional modal positioning */
.custom-modal-content {
  transform: translateY(0);
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Increased main content height */
.main-content {
  min-height: calc(100vh + 200px);
  padding-bottom: 4rem;
}

/* Increased page content */
.enrollments-page-content {
  min-height: 80vh;
  padding-bottom: 4rem;
}

/* When modal is open, prevent body scroll and ensure modal is on top */
body.modal-open {
  overflow: hidden !important;
  position: fixed !important;
  width: 100% !important;
}

/* Ensure modal appears above everything */
.custom-modal {
  position: fixed !important;
  z-index: 999999 !important;
}
</style>
<div class="enrollments-page-content">
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Registration Applications</h1>
  <div>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
  </div>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" onchange="this.form.submit()">
      <?php $statuses = ['pending','approved','rejected','enrolled']; ?>
      <?php foreach ($statuses as $s): ?>
        <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label">Grade</label>
    <select name="grade" class="form-select" onchange="this.form.submit()">
      <option value="">All</option>
      <?php foreach (grade_level_options() as $g): ?>
        <option value="<?= $g ?>" <?= ($gradeLevel==$g?'selected':'') ?>><?= esc(grade_level_label($g)) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</form>

<div class="card">
  <div class="card-body p-0">
    <?php if (!empty($students)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Grade</th>
              <th>Section</th>
              <th>Status</th>
              <th>Applied</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $st): ?>
              <tr>
                <td><?= esc($st['first_name'].' '.$st['last_name']) ?></td>
                <td><?= esc(grade_level_label((int) ($st['grade_level'] ?? 0))) ?></td>
                <td><?= esc($st['section_name'] ?? '—') ?></td>
                <td><span class="badge bg-<?= $st['enrollment_status']==='pending'?'warning':($st['enrollment_status']==='approved'?'info':($st['enrollment_status']==='enrolled'?'success':'secondary')) ?>">
                  <?= ucfirst(esc($st['enrollment_status'])) ?></span></td>
                <td><?= $st['created_at'] ? date('M j, Y', strtotime($st['created_at'])) : '' ?></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary me-1" onclick="showStudentDetails(<?= $st['id'] ?>)" title="View student details">
                    <i class="bi bi-eye"></i> View
                  </button>
                  <?php if ($status === 'pending'): ?>
                    <div class="d-flex gap-1 justify-content-end">
                      <!-- Approve Button -->
                      <button class="btn btn-sm btn-success" onclick="showApprovalModal(<?= $st['id'] ?>, <?= $st['grade_level'] ?>)" title="Approve and assign section">
                        <i class="bi bi-check-circle"></i> Approve
                      </button>

                      <!-- Reject Button -->
                      <button class="btn btn-sm btn-outline-danger" onclick="showRejectionModal(<?= $st['id'] ?>)" title="Reject enrollment">
                        <i class="bi bi-x-circle"></i> Reject
                      </button>
                    </div>
                  <?php elseif ($status === 'approved'): ?>
                    <form action="<?= base_url('admin/enrollments/approve/'.$st['id']) ?>" method="post" class="d-flex gap-2 justify-content-end">
                      <?= csrf_field() ?>
                      <select name="section_id" class="form-select form-select-sm" style="max-width:220px">
                        <option value="">Assign Section (optional)</option>
                        <?php $list = $availableSections[$st['grade_level']] ?? []; ?>
                        <?php foreach ($list as $sec): ?>
                          <option value="<?= $sec['id'] ?>"><?= esc($sec['section_name']) ?> (<?= $sec['current_enrollment'] ?>/<?= $sec['max_capacity'] ?>)</option>
                        <?php endforeach; ?>
                      </select>
                      <button class="btn btn-sm btn-primary">Save</button>
                    </form>
                  <?php else: ?>
                    <?php if ($st['enrollment_status'] === 'enrolled' && !empty($st['section_name'])): ?>
                      <div class="text-center">
                        <small class="text-success">
                          <i class="bi bi-check-circle-fill"></i> Enrolled
                        </small>
                        <br>
                        <small class="text-muted">
                          Section: <?= esc($st['section_name']) ?>
                        </small>
                      </div>
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="p-4 text-center text-muted">No records found.</div>
    <?php endif; ?>
  </div>
</div>

<!-- Enhanced Approval Modal -->
<div id="approvalModal" class="custom-modal" style="display: none;">
  <div class="custom-modal-overlay" onclick="closeApprovalModal()"></div>
  <div class="custom-modal-content">
    <div class="custom-modal-header approval">
      <h5>
        <i class="bi bi-check-circle"></i>
        Approve Registration
      </h5>
      <button type="button" class="custom-modal-close" onclick="closeApprovalModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <form id="approvalForm" method="post">
      <?= csrf_field() ?>
      <div class="custom-modal-body">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-person-circle"></i>
            Student Information
          </div>
          <div id="approvalStudentInfo" class="student-info-display">
            <!-- Student info will be populated here -->
          </div>
        </div>

        <div class="section-selection">
          <label for="approvalSectionSelect">
            <i class="bi bi-grid-3x3-gap"></i>
            Section Assignment
          </label>
          <select name="section_id" id="approvalSectionSelect" class="form-control">
            <option value="">Auto-assign to best available section</option>
          </select>
          <small class="form-text">Leave blank for automatic assignment based on availability and capacity.</small>
        </div>
      </div>
      <div class="custom-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeApprovalModal()">
          <i class="bi bi-x-circle"></i> Cancel
        </button>
        <button type="submit" class="btn btn-success">
          <i class="bi bi-check-circle"></i> Approve Registration
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Enhanced Rejection Modal -->
<div id="rejectionModal" class="custom-modal" style="display: none;">
  <div class="custom-modal-overlay" onclick="closeRejectionModal()"></div>
  <div class="custom-modal-content">
    <div class="custom-modal-header rejection">
      <h5>
        <i class="bi bi-x-circle"></i>
        Reject Registration
      </h5>
      <button type="button" class="custom-modal-close" onclick="closeRejectionModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <form id="rejectionForm" method="post">
      <?= csrf_field() ?>
      <div class="custom-modal-body">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-person-circle"></i>
            Student Information
          </div>
          <div id="rejectionStudentInfo" class="student-info-display">
            <!-- Student info will be populated here -->
          </div>
        </div>

        <div class="rejection-reason">
          <label for="rejectionReason">
            <i class="bi bi-chat-square-text"></i>
            Reason for Rejection
          </label>
          <textarea name="reason" id="rejectionReason" class="form-control" rows="4" placeholder="Please provide a detailed reason for rejecting this enrollment application..." required></textarea>
          <small class="form-text">This reason will be sent to the student via email notification.</small>
        </div>

        <div class="alert alert-warning">
          <i class="bi bi-exclamation-triangle"></i>
          <div>
            <strong>Warning:</strong> This action cannot be undone. The student will be automatically notified of the rejection via email.
          </div>
        </div>
      </div>
      <div class="custom-modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeRejectionModal()">
          <i class="bi bi-arrow-left"></i> Cancel
        </button>
        <button type="submit" class="btn btn-danger">
          <i class="bi bi-x-circle"></i> Reject Registration
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Enhanced Student Details Modal -->
<div id="studentDetailsModal" class="custom-modal" style="display: none;">
  <div class="custom-modal-overlay" onclick="closeStudentDetailsModal()"></div>
  <div class="custom-modal-content custom-modal-large">
    <div class="custom-modal-header">
      <h5>
        <i class="bi bi-person-lines-fill"></i>
        Student Details
      </h5>
      <button type="button" class="custom-modal-close" onclick="closeStudentDetailsModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="custom-modal-body">
      <div id="studentDetailsContent">
        <div class="loading-spinner">
          <div class="spinner"></div>
          <p>Loading student details...</p>
        </div>
      </div>
    </div>
    <div class="custom-modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeStudentDetailsModal()">
        <i class="bi bi-x-circle"></i> Close
      </button>
    </div>
  </div>
</div>

<!-- Document Viewer Modal -->
<div id="documentViewerModal" class="document-viewer-modal" style="display: none;">
  <div class="document-viewer-overlay" onclick="closeDocumentModal()"></div>
  <div class="document-viewer-content">
    <div class="document-viewer-header">
      <h5 id="documentViewerTitle">Document Viewer</h5>
      <button type="button" class="document-viewer-close" onclick="closeDocumentModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="document-viewer-body">
      <img id="documentViewerImage" src="" alt="Document" class="document-viewer-image">
    </div>
  </div>
</div>

</div> <!-- Close enrollments-page-content -->

<script>
// Store student data for quick access
const studentsData = <?= json_encode($students) ?>;
const availableSections = <?= json_encode($availableSections ?? []) ?>;

// Debug: Log the data to console
console.log('Students data loaded:', studentsData.length, 'students');
console.log('Available sections loaded:', availableSections);

function viewStudent(studentId) {
  const student = studentsData.find(s => s.id == studentId);

  if (!student) {
    document.getElementById('studentDetails').innerHTML = '<div class="alert alert-danger">Student not found.</div>';
    return;
  }

  const detailsHtml = `
    <div class="row">
      <div class="col-md-6">
        <h6 class="text-primary mb-3">Personal Information</h6>
        <table class="table table-sm">
          <tr><td><strong>Full Name:</strong></td><td>${student.first_name} ${student.middle_name || ''} ${student.last_name} ${student.suffix || ''}</td></tr>
          <tr><td><strong>Gender:</strong></td><td>${student.gender || 'N/A'}</td></tr>
          <tr><td><strong>Date of Birth:</strong></td><td>${student.date_of_birth ? new Date(student.date_of_birth).toLocaleDateString() : 'N/A'}</td></tr>
          <tr><td><strong>Place of Birth:</strong></td><td>${student.place_of_birth || 'N/A'}</td></tr>
          <tr><td><strong>Nationality:</strong></td><td>${student.nationality || 'N/A'}</td></tr>
          <tr><td><strong>Religion:</strong></td><td>${student.religion || 'N/A'}</td></tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6 class="text-primary mb-3">Contact Information</h6>
        <table class="table table-sm">
          <tr><td><strong>Email:</strong></td><td>${student.email || 'N/A'}</td></tr>
          <tr><td><strong>Contact Number:</strong></td><td>${student.contact_number || 'N/A'}</td></tr>
          <tr><td><strong>Address:</strong></td><td>${student.address || 'N/A'}</td></tr>
        </table>

        <h6 class="text-primary mb-3 mt-4">Emergency Contact</h6>
        <table class="table table-sm">
          <tr><td><strong>Name:</strong></td><td>${student.emergency_contact_name || 'N/A'}</td></tr>
          <tr><td><strong>Number:</strong></td><td>${student.emergency_contact_number || 'N/A'}</td></tr>
          <tr><td><strong>Relationship:</strong></td><td>${student.emergency_contact_relationship || 'N/A'}</td></tr>
        </table>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-12">
        <h6 class="text-primary mb-3">Application Information</h6>
        <table class="table table-sm">
          <tr><td><strong>Grade Level:</strong></td><td>${formatGradeLevel(student.grade_level)}</td></tr>
          <tr><td><strong>Section:</strong></td><td>${student.section_name || 'Not assigned'}</td></tr>
          <tr><td><strong>School Year:</strong></td><td>${student.school_year || 'N/A'}</td></tr>
          <tr><td><strong>Status:</strong></td><td><span class="badge bg-${student.enrollment_status === 'pending' ? 'warning' : (student.enrollment_status === 'approved' ? 'info' : (student.enrollment_status === 'enrolled' ? 'success' : 'secondary'))}">${student.enrollment_status.charAt(0).toUpperCase() + student.enrollment_status.slice(1)}</span></td></tr>
          <tr><td><strong>Applied Date:</strong></td><td>${student.created_at ? new Date(student.created_at).toLocaleDateString() : 'N/A'}</td></tr>
        </table>
      </div>
    </div>
  `;

  document.getElementById('studentDetails').innerHTML = detailsHtml;
}

// Simple Modal Functions
function showApprovalModal(studentId, gradeLevel) {
  const student = studentsData.find(s => s.id == studentId);

  if (!student) {
    alert('Student not found.');
    return;
  }

  // Update form action
  const form = document.getElementById('approvalForm');
  form.action = `<?= base_url('admin/enrollments/approve/') ?>${studentId}`;

  // Populate student info with enhanced design
  const studentInfo = `
    <div class="student-name">${student.first_name} ${student.middle_name || ''} ${student.last_name}</div>
    <div class="student-meta">
      <i class="bi bi-mortarboard"></i> ${formatGradeLevel(student.grade_level)} •
      <i class="bi bi-calendar-event"></i> Applied: ${student.created_at ? new Date(student.created_at).toLocaleDateString() : 'N/A'}
    </div>
    <div class="student-status" style="background: #fef3c7; color: #92400e; border: 1px solid #fbbf24;">
      <i class="bi bi-clock"></i> Pending Approval
    </div>
  `;
  document.getElementById('approvalStudentInfo').innerHTML = studentInfo;

  // Populate section options
  const sectionSelect = document.getElementById('approvalSectionSelect');
  sectionSelect.innerHTML = '<option value="">Auto-assign to best available section</option>';

  const sections = availableSections[gradeLevel] || [];
  if (sections.length > 0) {
    sections.forEach(section => {
      const option = document.createElement('option');
      option.value = section.id;
      option.textContent = `${section.section_name} (${section.current_enrollment}/${section.max_capacity} students)`;

      if (section.current_enrollment >= section.max_capacity * 0.9) {
        option.textContent += ' - Nearly Full';
        option.style.color = '#dc3545';
      } else if (section.current_enrollment >= section.max_capacity * 0.7) {
        option.textContent += ' - Filling Up';
        option.style.color = '#fd7e14';
      }

      sectionSelect.appendChild(option);
    });
  }

  // Show modal with animation
  const modal = document.getElementById('approvalModal');
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeApprovalModal() {
  document.getElementById('approvalModal').style.display = 'none';
  document.body.style.overflow = '';
}

function showRejectionModal(studentId) {
  const student = studentsData.find(s => s.id == studentId);

  if (!student) {
    alert('Student not found.');
    return;
  }

  // Update form action
  const form = document.getElementById('rejectionForm');
  form.action = `<?= base_url('admin/enrollments/reject/') ?>${studentId}`;

  // Populate student info with enhanced design
  const studentInfo = `
    <div class="student-name">${student.first_name} ${student.middle_name || ''} ${student.last_name}</div>
    <div class="student-meta">
      <i class="bi bi-mortarboard"></i> ${formatGradeLevel(student.grade_level)} •
      <i class="bi bi-calendar-event"></i> Applied: ${student.created_at ? new Date(student.created_at).toLocaleDateString() : 'N/A'}
    </div>
    <div class="student-status" style="background: #fef3c7; color: #92400e; border: 1px solid #fbbf24;">
      <i class="bi bi-clock"></i> Pending Approval
    </div>
  `;
  document.getElementById('rejectionStudentInfo').innerHTML = studentInfo;

  // Clear previous reason
  document.getElementById('rejectionReason').value = '';

  // Show modal with animation
  const modal = document.getElementById('rejectionModal');
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeRejectionModal() {
  document.getElementById('rejectionModal').style.display = 'none';
  document.body.style.overflow = '';
}

function showStudentDetails(studentId) {
  const modal = document.getElementById('studentDetailsModal');
  const detailsContainer = document.getElementById('studentDetailsContent');

  // Show modal with loading state
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';

  // Show loading spinner
  detailsContainer.innerHTML = `
    <div class="loading-spinner">
      <div class="spinner"></div>
      <p>Loading student details...</p>
    </div>
  `;

  // Load student details with documents from server
  fetch(`<?= base_url('admin/enrollments/student/') ?>${studentId}`)
    .then(response => response.text())
    .then(html => {
      detailsContainer.innerHTML = html;
    })
    .catch(error => {
      console.error('Error loading student details:', error);

      // Fallback to local data if server request fails
      setTimeout(() => {
        loadStudentDetailsContent(studentId);
      }, 500);
    });
}

function loadStudentDetailsContent(studentId) {
  const student = studentsData.find(s => s.id == studentId);
  const detailsContainer = document.getElementById('studentDetailsContent');

  if (!student) {
    detailsContainer.innerHTML = `
      <div class="student-info-section">
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle"></i>
          Student not found.
        </div>
      </div>
    `;
    return;
  }

  const detailsHtml = `
    <div class="row">
      <div class="col-md-6">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-person-circle"></i>
            Personal Information
          </div>
          <table class="student-details-table">
            <tr><td>Full Name:</td><td>${student.first_name} ${student.middle_name || ''} ${student.last_name} ${student.suffix || ''}</td></tr>
            <tr><td>Gender:</td><td>${student.gender || 'N/A'}</td></tr>
            <tr><td>Date of Birth:</td><td>${student.date_of_birth ? new Date(student.date_of_birth).toLocaleDateString() : 'N/A'}</td></tr>
            <tr><td>Place of Birth:</td><td>${student.place_of_birth || 'N/A'}</td></tr>
            <tr><td>Nationality:</td><td>${student.nationality || 'N/A'}</td></tr>
            <tr><td>Religion:</td><td>${student.religion || 'N/A'}</td></tr>
          </table>
        </div>
      </div>
      <div class="col-md-6">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-telephone"></i>
            Contact Information
          </div>
          <table class="student-details-table">
            <tr><td>Email:</td><td>${student.email || 'N/A'}</td></tr>
            <tr><td>Contact Number:</td><td>${student.contact_number || 'N/A'}</td></tr>
            <tr><td>Address:</td><td>${student.address || 'N/A'}</td></tr>
          </table>
        </div>

        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-shield-exclamation"></i>
            Emergency Contact
          </div>
          <table class="student-details-table">
            <tr><td>Name:</td><td>${student.emergency_contact_name || 'N/A'}</td></tr>
            <tr><td>Number:</td><td>${student.emergency_contact_number || 'N/A'}</td></tr>
            <tr><td>Relationship:</td><td>${student.emergency_contact_relationship || 'N/A'}</td></tr>
          </table>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-mortarboard"></i>
            Application Information
          </div>
          <table class="student-details-table">
            <tr><td>Grade Level:</td><td>${formatGradeLevel(student.grade_level)}</td></tr>
            <tr><td>Section:</td><td>${student.section_name || 'Not assigned'}</td></tr>
            <tr><td>School Year:</td><td>${student.school_year || 'N/A'}</td></tr>
            <tr><td>Status:</td><td><span class="badge bg-${student.enrollment_status === 'pending' ? 'warning' : (student.enrollment_status === 'approved' ? 'info' : (student.enrollment_status === 'enrolled' ? 'success' : 'secondary'))}">${student.enrollment_status.charAt(0).toUpperCase() + student.enrollment_status.slice(1)}</span></td></tr>
            <tr><td>Applied Date:</td><td>${student.created_at ? new Date(student.created_at).toLocaleDateString() : 'N/A'}</td></tr>
          </table>
        </div>
      </div>
    </div>
  `;

  detailsContainer.innerHTML = detailsHtml;
}

function closeStudentDetailsModal() {
  document.getElementById('studentDetailsModal').style.display = 'none';
  document.body.style.overflow = '';
}

// Simple image viewer functions (global scope)
function showImageModal(imageUrl, title) {
  // Create modal if it doesn't exist
  let modal = document.getElementById('simpleImageModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'simpleImageModal';
    modal.innerHTML = `
      <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; display: flex; align-items: center; justify-content: center;" onclick="closeSimpleModal()">
        <div style="background: white; border-radius: 12px; max-width: 90vw; max-height: 90vh; overflow: hidden; position: relative;" onclick="event.stopPropagation()">
          <div style="background: #1e40af; color: white; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h5 style="margin: 0; color: white;" id="simpleModalTitle">Document Viewer</h5>
            <button onclick="closeSimpleModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">×</button>
          </div>
          <div style="padding: 1rem; text-align: center;">
            <img id="simpleModalImage" src="" alt="Document" style="max-width: 100%; max-height: 70vh; object-fit: contain;">
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  }

  // Set image and title
  const img = modal.querySelector('#simpleModalImage');
  const titleEl = modal.querySelector('#simpleModalTitle');

  if (img) img.src = imageUrl;
  if (titleEl) titleEl.textContent = title;

  // Show modal
  modal.style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeSimpleModal() {
  const modal = document.getElementById('simpleImageModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}



// Document viewer functions (global scope) - keeping for compatibility
function openDocumentModal(imageUrl, title) {
  console.log('openDocumentModal called, redirecting to showImageModal');
  showImageModal(imageUrl, title);
}

function closeDocumentModal() {
  console.log('Closing document modal');

  const modal = document.getElementById('documentViewerModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
    console.log('Document modal closed successfully');
  } else {
    console.error('Document viewer modal not found for closing');
  }
}

// Enhanced modal event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Event delegation for clickable documents
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('clickable-document') || e.target.closest('.clickable-document')) {
      e.preventDefault();
      e.stopPropagation();

      const element = e.target.classList.contains('clickable-document') ? e.target : e.target.closest('.clickable-document');
      const imageUrl = element.getAttribute('data-image-url');
      const imageTitle = element.getAttribute('data-image-title');

      console.log('Document clicked via event delegation:', imageUrl, imageTitle);

      if (imageUrl && imageTitle) {
        openDocumentModal(imageUrl, imageTitle);
      }
    }
  });

  // Close modals with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeApprovalModal();
      closeRejectionModal();
      closeStudentDetailsModal();

      // Also close document viewer modal
      const docModal = document.getElementById('documentViewerModal');
      if (docModal && docModal.style.display === 'flex') {
        closeDocumentModal();
      }
    }
  });

  // Prevent modal content clicks from closing the modal
  document.querySelectorAll('.custom-modal-content').forEach(content => {
    content.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  });
});
</script>

<?= $this->endSection() ?>