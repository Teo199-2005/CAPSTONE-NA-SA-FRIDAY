<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-image dash-icon-inline"></i> 
                        Document Viewer
                    </h5>
                    <div>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body text-center p-0">
                    <img src="<?= $fileUrl ?>" 
                         alt="Document" 
                         class="img-fluid"
                         style="max-height: 80vh; width: auto;">
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>