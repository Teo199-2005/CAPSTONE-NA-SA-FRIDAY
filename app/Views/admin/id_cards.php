<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
#gridViewContainer {
  display: grid !important;
  grid-template-columns: repeat(3, 1fr) !important;
  gap: 1.5rem !important;
}
#gridViewContainer .col {
  width: 100% !important;
  max-width: 100% !important;
  flex: none !important;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h4 mb-0"><i class="bi bi-person-badge me-2"></i>Student ID Cards</h1>
  <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form method="GET" class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Grade Level</label>
        <select class="form-select" name="grade">
          <option value="">All Grades</option>
          <?php for ($grade = 7; $grade <= 12; $grade++): ?>
            <option value="<?= $grade ?>" <?= $gradeFilter == $grade ? 'selected' : '' ?>>Grade <?= $grade ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Section</label>
        <select class="form-select" name="section">
          <option value="">All Sections</option>
          <?php foreach ($allSections as $section): ?>
            <option value="<?= $section['id'] ?>" <?= $sectionFilter == $section['id'] ? 'selected' : '' ?>>
              Grade <?= $section['grade_level'] ?> - <?= esc($section['section_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Search Student</label>
        <input type="text" class="form-control" name="search" value="<?= esc($searchTerm) ?>" placeholder="Name or Student ID">
      </div>
      <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Student Cards -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Student ID Cards (<?= $totalStudents ?> students)</h5>
      <div class="col-md-4">
        <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search students..." onkeyup="searchStudents()">
      </div>
    </div>
  </div>
  <div class="card-body">
    <?php if (empty($students)): ?>
      <div class="text-center py-5">
        <i class="bi bi-person-badge fs-1 text-muted mb-3"></i>
        <h5 class="text-muted">No Students Found</h5>
        <p class="text-muted">Try adjusting your filters or search criteria.</p>
      </div>
    <?php else: ?>
      <!-- Grid View -->
      <div id="gridViewContainer">
        <?php foreach ($students as $student): ?>
        <div class="col">
          <div class="card border shadow-sm h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                  <?php if (!empty($student['photo']) && file_exists(FCPATH . 'uploads/enrollment_documents/' . $student['photo'])): ?>
                    <img src="<?= base_url('uploads/enrollment_documents/' . $student['photo']) ?>" alt="Photo" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                  <?php else: ?>
                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                      <i class="bi bi-person text-white"></i>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1"><?= esc($student['last_name'] . ', ' . $student['first_name']) ?></h6>
                  <small class="text-muted">ID: <?= esc($student['student_id'] ?? 'Pending') ?></small>
                </div>
              </div>
              
              <div class="mb-3">
                <span class="badge bg-primary me-2">Grade <?= esc($student['grade_level']) ?></span>
                <?php if ($student['section_name']): ?>
                  <span class="badge bg-secondary"><?= esc($student['section_name']) ?></span>
                <?php endif; ?>
              </div>
              
              <div class="d-grid gap-2 mt-2">
                <a href="<?= base_url('admin/id-cards/view/' . $student['id']) ?>" class="btn btn-light btn-sm">
                  <i class="bi bi-eye me-1"></i>View ID Card
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      

    <?php endif; ?>
  </div>
  
  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="card-footer">
    <nav>
      <ul class="pagination justify-content-center mb-0">
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>">
            <i class="bi bi-chevron-left"></i> Previous
          </a>
        </li>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>">
            Next <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <?php endif; ?>
</div>

<script>

</script>

<script>
function searchStudents() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();
  const studentCards = document.querySelectorAll('#gridViewContainer .col');
  
  studentCards.forEach(card => {
    const studentName = card.querySelector('h6').textContent.toLowerCase();
    const studentId = card.querySelector('small').textContent.toLowerCase();
    
    if (studentName.includes(searchTerm) || studentId.includes(searchTerm)) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
}
</script>

<?= $this->endSection() ?>



