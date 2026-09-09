<div class="modal fade citizens-charter-modal" id="citizensCharterModal" tabindex="-1" aria-labelledby="citizensCharterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl modal-fullscreen-lg-down">
    <div class="modal-content citizens-charter-modal__content">
      <div class="modal-header citizens-charter-modal__header">
        <div>
          <h2 class="modal-title h5 mb-0" id="citizensCharterModalLabel">Citizen's Charter</h2>
          <p class="mb-0 small text-muted">Cauayan South Central School — DepEd Region II</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body citizens-charter-modal__body p-0">
        <div class="citizens-charter-gallery">
          <figure class="citizens-charter-page-wrap">
            <img
              src="<?= asset_url('citizens.png') ?>"
              alt="Citizen's Charter — page 1: introduction and enrollment services"
              class="citizens-charter-page"
              width="1200"
              height="1600"
              loading="eager"
              decoding="async"
            >
            <figcaption class="visually-hidden">Page 1</figcaption>
          </figure>
          <figure class="citizens-charter-page-wrap">
            <img
              src="<?= asset_url('citizens2.png') ?>"
              alt="Citizen's Charter — page 2: transfer, LIS, diploma, and service pledge"
              class="citizens-charter-page"
              width="1200"
              height="1600"
              loading="lazy"
              decoding="async"
            >
            <figcaption class="visually-hidden">Page 2</figcaption>
          </figure>
        </div>
      </div>
      <div class="modal-footer citizens-charter-modal__footer py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
