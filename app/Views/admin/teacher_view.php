<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge dash-icon-inline"></i> 
                        Teacher Details
                    </h5>
                    <div>
                        <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Teachers
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?= view('admin/partials/teacher_details_modal', ['teacher' => $teacher, 'sections' => $sections]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>