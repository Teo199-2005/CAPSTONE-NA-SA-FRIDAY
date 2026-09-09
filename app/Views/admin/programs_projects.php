    <?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<link href="<?= asset_url('css/admin-landing-preview.css') ?>" rel="stylesheet" />

<div class="mb-4">
    <h1 class="h3 mb-2">Programs & Projects Management</h1>
    <p class="text-muted mb-0">Manage your Programs & Projects page content — hero banner, title, description, and up to 6 content sections.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
    $tab = 'programs';
    $tabData = $tabs[$tab] ?? [];
    $hero = $tabData['hero'] ?? [];
    $sections = $tabData['sections'] ?? [];
    $heroUrl = $tabData['heroUrl'] ?? '';
?>

<style>
.pp-row { display: flex; flex-wrap: wrap; gap: 1.25rem; margin-bottom: 1.5rem; }
.pp-col { flex: 0 0 calc(50% - 0.625rem); max-width: calc(50% - 0.625rem); }
.pp-card { height: 100%; display: flex; flex-direction: column; }
.pp-card .card-body { flex: 1 1 auto; overflow: auto; }
@media (max-width: 767px) {
    .pp-col { flex: 0 0 100%; max-width: 100%; }
}

.section-item {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #fff;
    position: relative;
}

.section-item .delete-section-btn {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}
</style>

<form method="post" action="<?= base_url('admin/programs-projects/update') ?>" enctype="multipart/form-data" id="programsForm">
    <?= csrf_field() ?>
    <input type="hidden" name="tab" value="<?= $tab ?>">

    <!-- Hero Section -->
    <div class="pp-row">
        <div class="pp-col">
            <div class="card border-0 shadow-sm pp-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-image me-2"></i>Programs & Projects — Hero Section</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hero Image</label>
                        <div class="mb-2">
                            <?php if ($heroUrl !== ''): ?>
                                <img src="<?= esc($heroUrl) ?>" alt="Hero preview" class="img-fluid w-100" style="max-height: 280px; object-fit: cover;">
                            <?php else: ?>
                                <div class="alert alert-light border">No image uploaded yet.</div>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control" name="hero_image" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">JPG, PNG, WEBP — max 50MB.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="hero_title" value="<?= esc($hero['title'] ?? '') ?>" placeholder="Hero title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="hero_description" rows="2" maxlength="500" placeholder="Hero description"><?= esc($hero['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="pp-row">
        <div class="pp-col" style="flex: 0 0 100%; max-width: 100%;">
            <h5 class="mb-3"><i class="bi bi-layers me-2"></i>Content Sections</h5>
            <div id="sectionsContainer">
                <?php foreach ($sections as $index => $section): ?>
                    <?php
                        $sectionId = $section['id'] ?? 'section_' . ($index + 1) . '_programs';
                        $sectionTitle = $section['title'] ?? '';
                        $sectionDesc = $section['description'] ?? '';
                        $mediaType = $section['media_type'] ?? 'image';
                        $mediaUrl = $section['media_url'] ?? '';
                    ?>
                    <div class="section-item" data-section-index="<?= $index ?>">
                        <input type="hidden" name="section_id[]" value="<?= esc($sectionId) ?>">
                        <input type="hidden" name="section_order[]" value="<?= $index + 1 ?>">
                        
                        <button type="button" class="btn btn-danger btn-sm delete-section-btn" onclick="deleteSection(this)" title="Delete section">
                            <i class="bi bi-trash"></i>
                        </button>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small">Title</label>
                                <input type="text" class="form-control form-control-sm" name="section_title[]" value="<?= esc($sectionTitle) ?>" placeholder="Section title" maxlength="200">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small">Media Type</label>
                                <select class="form-select form-select-sm" name="section_media_type[]">
                                    <option value="image" <?= $mediaType === 'image' ? 'selected' : '' ?>>Image</option>
                                    <option value="video" <?= $mediaType === 'video' ? 'selected' : '' ?>>YouTube Video</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small">Order</label>
                                <input type="number" class="form-control form-control-sm" name="section_order_<?= $index ?>" value="<?= $index + 1 ?>" min="1" max="10">
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="form-label small">Upload Image</label>
                            <?php if ($mediaUrl !== ''): ?>
                                <img src="<?= esc(programs_projects_media_url($mediaUrl)) ?>" alt="Section media" class="img-fluid mb-2" style="max-height: 80px; max-width: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <input type="file" class="form-control form-control-sm" name="section_media_<?= $sectionId ?>" accept="image/jpeg,image/png,image/webp">
                            <input type="hidden" name="section_media_url[]" value="<?= esc($mediaUrl) ?>">
                        </div>

                        <div class="mt-2">
                            <label class="form-label small">Description</label>
                            <textarea class="form-control form-control-sm" name="section_description[]" rows="3" placeholder="Section description..." maxlength="1000"><?= esc($sectionDesc) ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="button" class="btn btn-success btn-lg mt-3" id="addSectionBtn">
                <i class="bi bi-plus-lg me-2"></i>Add Section
            </button>
            <p class="text-muted small mt-2 mb-0">Click to add a new content section. Maximum 6 sections allowed.</p>
    </div>

    <!-- Save Button -->
    <div class="pp-row">
        <div class="pp-col" style="flex: 0 0 100%; max-width: 100%;">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Save Programs & Projects Page
            </button>
        </div>
    </div>
</form>

<script>
let sectionCounter = <?= count($sections) ?>;

document.getElementById('addSectionBtn').addEventListener('click', function() {
    const container = document.getElementById('sectionsContainer');
    const currentCount = container.querySelectorAll('.section-item').length;
    
    if (currentCount >= 6) {
        alert('Maximum 6 sections allowed.');
        return;
    }

    sectionCounter++;
    const sectionHtml = `
        <div class="section-item" data-section-index="${sectionCounter}">
            <input type="hidden" name="section_id[]" value="section_${sectionCounter}_programs">
            <input type="hidden" name="section_order[]" value="${currentCount + 1}">
            
            <button type="button" class="btn btn-danger btn-sm delete-section-btn" onclick="deleteSection(this)" title="Delete section">
                <i class="bi bi-trash"></i>
            </button>

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label small">Title</label>
                    <input type="text" class="form-control form-control-sm" name="section_title[]" placeholder="Section title" maxlength="200">
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Media Type</label>
                    <select class="form-select form-select-sm" name="section_media_type[]">
                        <option value="image">Image</option>
                        <option value="video">YouTube Video</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Order</label>
                    <input type="number" class="form-control form-control-sm" name="section_order_${sectionCounter}" value="${currentCount + 1}" min="1" max="10">
                </div>
            </div>

            <div class="mt-2">
                <label class="form-label small">Upload Image</label>
                <input type="file" class="form-control form-control-sm" name="section_media_section_${sectionCounter}_programs" accept="image/jpeg,image/png,image/webp">
                <input type="hidden" name="section_media_url[]" value="">
            </div>

            <div class="mt-2">
                <label class="form-label small">Description</label>
                <textarea class="form-control form-control-sm" name="section_description[]" rows="3" placeholder="Section description..." maxlength="1000"></textarea>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', sectionHtml);
});

function deleteSection(btn) {
    if (!confirm('Delete this section?')) return;
    const sectionItem = btn.closest('.section-item');
    sectionItem.remove();
    
    // Update order values
    const container = document.getElementById('sectionsContainer');
    const items = container.querySelectorAll('.section-item');
    items.forEach((item, index) => {
        item.querySelector('input[name="section_order[]"]').value = index + 1;
        const orderInput = item.querySelector('input[type="number"]');
        if (orderInput) orderInput.value = index + 1;
    });
}
</script>

<?= $this->endSection() ?>
</parameter>
</write_to_file>