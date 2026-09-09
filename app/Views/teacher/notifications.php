<?= $this->extend('dashboard_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Notifications</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Send Notification Form -->
                    <div class="mb-4">
                        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#sendNotificationForm">
                            <i class="fas fa-plus"></i> Send Notification to Students
                        </button>
                    </div>

                    <div class="collapse mb-4" id="sendNotificationForm">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?= base_url('teacher/notifications/send-to-students') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="section_id" class="form-label">Section (Optional)</label>
                                        <select class="form-control" id="section_id" name="section_id">
                                            <option value="">All Students</option>
                                            <!-- Add section options here -->
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send Notification</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($notifications)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No notifications found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notification): ?>
                                        <tr class="<?= $notification['is_read'] ? '' : 'table-warning' ?>">
                                            <td>
                                                <?php if ($notification['is_read']): ?>
                                                    <span class="badge bg-secondary">Read</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Unread</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($notification['title']) ?></td>
                                            <td><?= esc(substr($notification['message'], 0, 100)) ?><?= strlen($notification['message']) > 100 ? '...' : '' ?></td>
                                            <td><?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?></td>
                                            <td>
                                                <?php if (!$notification['is_read']): ?>
                                                    <button class="btn btn-sm btn-outline-primary mark-read" data-id="<?= $notification['id'] ?>">
                                                        Mark as Read
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark as read functionality
    document.querySelectorAll('.mark-read').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            
            fetch('<?= base_url('teacher/notifications/mark-as-read') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `notification_id=${notificationId}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to mark notification as read');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        });
    });
});
</script>
<?= $this->endSection() ?>