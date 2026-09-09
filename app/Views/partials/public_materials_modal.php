<?php
helper('materials');
$websiteMaterials = public_website_materials();
?>
<div class="modal fade school-materials-modal" id="schoolMaterialsModal" tabindex="-1" aria-labelledby="schoolMaterialsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl modal-fullscreen-lg-down">
    <div class="modal-content school-materials-modal__content">
      <div class="modal-header school-materials-modal__header">
        <div>
          <h2 class="modal-title h5 mb-0" id="schoolMaterialsModalLabel">
            <i class="bi bi-journal-bookmark-fill me-2"></i>School Materials
          </h2>
          <p class="mb-0 small school-materials-modal__subtitle">Handbooks, rules, forms, and learning resources</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body school-materials-modal__body p-0">
        <!-- List panel -->
        <div id="schoolMaterialsListPanel" class="school-materials-list-panel">
          <?php if ($websiteMaterials === []): ?>
            <div class="school-materials-empty text-center py-5 px-4">
              <i class="bi bi-folder2-open display-4 text-muted"></i>
              <p class="mt-3 mb-0 text-muted">No materials are available yet. Please check back later or contact the school office.</p>
            </div>
          <?php else: ?>
            <div class="school-materials-grid">
              <?php foreach ($websiteMaterials as $material): ?>
                <?php
                  $canPreview = material_can_preview_inline($material->file_type);
                  $previewUrl = material_preview_url((int) $material->id);
                  $downloadUrl = material_download_url((int) $material->id);
                  $icon = material_file_icon($material->file_type);
                ?>
                <article
                  class="school-material-card<?= $canPreview ? ' school-material-card--previewable' : '' ?>"
                  data-material-id="<?= (int) $material->id ?>"
                  data-preview-url="<?= esc($previewUrl) ?>"
                  data-download-url="<?= esc($downloadUrl) ?>"
                  data-title="<?= esc($material->title) ?>"
                  data-type="<?= esc(strtolower((string) $material->file_type)) ?>"
                  data-can-preview="<?= $canPreview ? '1' : '0' ?>"
                >
                  <div class="school-material-card__icon" aria-hidden="true">
                    <i class="bi bi-<?= esc($icon) ?>"></i>
                  </div>
                  <div class="school-material-card__body">
                    <span class="school-material-card__category"><?= esc(material_category_label($material->category)) ?></span>
                    <h3 class="school-material-card__title"><?= esc($material->title) ?></h3>
                    <?php if (! empty($material->description)): ?>
                      <p class="school-material-card__desc"><?= esc($material->description) ?></p>
                    <?php endif; ?>
                    <div class="school-material-card__meta">
                      <span><?= esc(strtoupper((string) $material->file_type)) ?></span>
                      <span><?= esc(material_format_size($material->file_size)) ?></span>
                      <span><?= esc(date('M j, Y', strtotime((string) $material->created_at))) ?></span>
                    </div>
                  </div>
                  <div class="school-material-card__actions">
                    <?php if ($canPreview): ?>
                      <button
                        type="button"
                        class="btn btn-sm btn-primary school-material-preview-btn"
                        data-preview-url="<?= esc($previewUrl) ?>"
                        data-download-url="<?= esc($downloadUrl) ?>"
                        data-title="<?= esc($material->title) ?>"
                        data-type="<?= esc(strtolower((string) $material->file_type)) ?>"
                        data-can-preview="1"
                      >
                        <i class="bi bi-eye me-1"></i>View
                      </button>
                    <?php endif; ?>
                    <a href="<?= esc($downloadUrl) ?>" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                      <i class="bi bi-download me-1"></i>Download
                    </a>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Preview panel (hidden until View) -->
        <div id="schoolMaterialsPreviewPanel" class="school-materials-preview-panel d-none">
          <div class="school-materials-preview-toolbar">
            <button type="button" class="btn btn-sm btn-light school-materials-back-btn" id="schoolMaterialsBackBtn">
              <i class="bi bi-arrow-left me-1"></i>Back to list
            </button>
            <h3 class="school-materials-preview-title mb-0" id="schoolMaterialsPreviewTitle"></h3>
            <a href="#" class="btn btn-sm btn-warning ms-auto" id="schoolMaterialsPreviewDownload" target="_blank" rel="noopener">
              <i class="bi bi-download me-1"></i>Download
            </a>
          </div>
          <div class="school-materials-preview-frame-wrap" id="schoolMaterialsPreviewFrameWrap">
            <iframe id="schoolMaterialsPreviewFrame" class="school-materials-preview-frame" title="Document preview"></iframe>
            <img id="schoolMaterialsPreviewImage" class="school-materials-preview-image d-none" alt="">
          </div>
          <div id="schoolMaterialsPreviewFallback" class="school-materials-preview-fallback d-none">
            <i class="bi bi-file-earmark-arrow-down"></i>
            <p>This file type cannot be previewed in the browser.</p>
            <a href="#" class="btn btn-primary" id="schoolMaterialsPreviewFallbackDl" target="_blank" rel="noopener">Download file</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var modal = document.getElementById('schoolMaterialsModal');
  if (!modal) return;

  var listPanel = document.getElementById('schoolMaterialsListPanel');
  var previewPanel = document.getElementById('schoolMaterialsPreviewPanel');
  var previewTitle = document.getElementById('schoolMaterialsPreviewTitle');
  var previewFrame = document.getElementById('schoolMaterialsPreviewFrame');
  var previewImage = document.getElementById('schoolMaterialsPreviewImage');
  var previewFallback = document.getElementById('schoolMaterialsPreviewFallback');
  var previewFrameWrap = document.getElementById('schoolMaterialsPreviewFrameWrap');
  var previewDownload = document.getElementById('schoolMaterialsPreviewDownload');
  var previewFallbackDl = document.getElementById('schoolMaterialsPreviewFallbackDl');
  var backBtn = document.getElementById('schoolMaterialsBackBtn');

  function showList() {
    if (listPanel) listPanel.classList.remove('d-none');
    if (previewPanel) previewPanel.classList.add('d-none');
    if (previewFrame) previewFrame.src = 'about:blank';
    if (previewImage) {
      previewImage.src = '';
      previewImage.classList.add('d-none');
    }
  }

  function showPreview(url, downloadUrl, title, type, canPreview) {
    if (!previewPanel || !listPanel) return;
    listPanel.classList.add('d-none');
    previewPanel.classList.remove('d-none');
    if (previewTitle) previewTitle.textContent = title || 'Preview';
    if (previewDownload) {
      previewDownload.href = downloadUrl;
    }
    if (previewFallbackDl) {
      previewFallbackDl.href = downloadUrl;
    }

    var imageTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    var isImage = imageTypes.indexOf((type || '').toLowerCase()) !== -1;
    var isPdf = (type || '').toLowerCase() === 'pdf';

    if (canPreview && isImage && previewImage && previewFrameWrap) {
      previewFallback.classList.add('d-none');
      previewFrameWrap.classList.remove('d-none');
      previewFrame.classList.add('d-none');
      previewImage.src = url;
      previewImage.classList.remove('d-none');
    } else if (canPreview && isPdf && previewFrame && previewFrameWrap) {
      previewImage.classList.add('d-none');
      previewFallback.classList.add('d-none');
      previewFrameWrap.classList.remove('d-none');
      previewFrame.classList.remove('d-none');
      previewFrame.src = url + '#view=FitH';
    } else {
      previewFrameWrap.classList.add('d-none');
      previewImage.classList.add('d-none');
      previewFallback.classList.remove('d-none');
    }
  }

  modal.addEventListener('click', function (event) {
    var btn = event.target.closest('.school-material-preview-btn');
    if (btn) {
      showPreview(
        btn.getAttribute('data-preview-url'),
        btn.getAttribute('data-download-url'),
        btn.getAttribute('data-title'),
        btn.getAttribute('data-type'),
        btn.getAttribute('data-can-preview') === '1'
      );
      return;
    }

    var card = event.target.closest('.school-material-card--previewable');
    if (!card) {
      return;
    }

    // Do not hijack clicks on action buttons/links.
    if (event.target.closest('.school-material-card__actions')) {
      return;
    }

    showPreview(
      card.getAttribute('data-preview-url'),
      card.getAttribute('data-download-url'),
      card.getAttribute('data-title'),
      card.getAttribute('data-type'),
      card.getAttribute('data-can-preview') === '1'
    );
  });

  if (backBtn) {
    backBtn.addEventListener('click', showList);
  }

  modal.addEventListener('hidden.bs.modal', showList);
})();
</script>
