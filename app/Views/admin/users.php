<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
.section-status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
}
.section-assigned {
    background-color: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}
.section-unassigned {
    background-color: #fff3cd;
    color: #664d03;
    border: 1px solid #ffecb5;
}
.section-na {
    background-color: #e2e3e5;
    color: #41464b;
    border: 1px solid #c4c8cc;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Manage Users</h1>
  <div>
    <button type="button" class="btn btn-primary me-2" onclick="openAddUserModal()"><i class="bi bi-plus-lg me-2"></i>Add User</button>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (empty($users)): ?>
      <p class="text-muted mb-0">No users found.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Email</th>
              <th>Role</th>
              <th>Section Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= esc($u['id']) ?></td>
              <td><?= esc($u['email']) ?></td>
              <td><?= esc($u['user_role']) ?></td>
              <td>
                <?php if ($u['user_role'] === 'student'): ?>
                  <?php if ($u['section_assigned']): ?>
                    <span class="section-status-badge section-assigned">
                      <i class="bi bi-check-circle me-1"></i>Assigned: <?= esc($u['section_name']) ?>
                    </span>
                  <?php else: ?>
                    <span class="section-status-badge section-unassigned">
                      <i class="bi bi-exclamation-triangle me-1"></i>No Section Assigned
                    </span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="section-status-badge section-na">N/A</span>
                <?php endif; ?>
              </td>
              <td><?= esc($u['created_at']) ?></td>
              <td>
                <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit User"><i class="bi bi-pencil"></i></a>
                <?php if (! empty($isMasterAdmin) && ($u['user_role'] ?? '') === 'Admin staff'): ?>
                  <a href="<?= base_url('admin/users/staff-permissions/' . $u['id']) ?>" class="btn btn-sm btn-outline-secondary me-1" title="Page access"><i class="bi bi-ui-checks"></i></a>
                <?php endif; ?>
                <?php if ($u['user_role'] === 'student' && !$u['section_assigned']): ?>
                  <a href="<?= base_url('admin/students') ?>" class="btn btn-sm btn-outline-warning me-1" title="Assign Section"><i class="bi bi-person-plus"></i></a>
                <?php endif; ?>
                <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete User"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>



<script>
function buildAddUserModal() {
  const existing = document.getElementById('addUserModal');
  if (existing) existing.remove();
  const html = `
  <div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= base_url('admin/users/create') ?>">
        <?= str_replace(["\n","\r"], '', csrf_field()) ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
            <div class="form-text">Minimum 8 characters.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="">Select Role</option>
              <option value="admin">Master admin</option>
              <option value="teacher">Teacher</option>
              <option value="student">Student</option>
              <option value="parent">Parent</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Create User</button>
        </div>
      </form>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('addUserModal');
}

function openAddUserModal() {
  const el = buildAddUserModal();
  new bootstrap.Modal(el, { backdrop: true, keyboard: true, focus: true }).show();
}
</script>


<?= $this->endSection() ?>
