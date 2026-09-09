<?php
/**
 * Thank-you modal after platform rating submission.
 *
 * @var bool   $showOnLoad     Auto-open when true
 * @var int    $submittedStars 1-5 if known
 * @var string $dashboardUrl   Link for primary button
 * @var string $primaryLabel   Button label
 * @var bool   $autoRedirect   If set with redirectUrl, navigate after delay
 * @var string $redirectUrl
 * @var int    $redirectDelayMs
 */
$showOnLoad      = ! empty($showOnLoad);
$submittedStars  = isset($submittedStars) ? (int) $submittedStars : 0;
$dashboardUrl    = $dashboardUrl ?? base_url();
$primaryLabel    = $primaryLabel ?? 'Back to dashboard';
$autoRedirect    = ! empty($autoRedirect);
$redirectUrl     = $redirectUrl ?? '';
$redirectDelayMs = (int) ($redirectDelayMs ?? 2800);
?>
<div class="modal fade" id="platformRatingThankYouModal" tabindex="-1" aria-labelledby="platformRatingThankYouTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg overflow-hidden">
      <div class="modal-body text-center px-4 py-5">
        <div class="platform-rating-thankyou-icon mb-3" aria-hidden="true">
          <i class="bi bi-check-circle-fill text-success"></i>
        </div>
        <h2 class="h4 fw-bold mb-2" id="platformRatingThankYouTitle">Thank you!</h2>
        <p class="text-muted mb-3" id="platformRatingThankYouMessage">
          Your feedback has been recorded. It helps Cauayan South Central School improve CSCS Tap n Track.
        </p>
        <div class="platform-rating-thankyou-stars mb-4 text-warning fs-4" id="platformRatingThankYouStars" aria-hidden="true"></div>
        <a href="<?= esc($dashboardUrl) ?>" class="btn btn-primary px-4" id="platformRatingThankYouBtn" data-bs-dismiss="modal">
          <?= esc($primaryLabel) ?>
        </a>
      </div>
    </div>
  </div>
</div>

<style>
.platform-rating-thankyou-icon .bi {
  font-size: 3.75rem;
  line-height: 1;
  filter: drop-shadow(0 8px 20px rgba(16, 185, 129, 0.25));
}
</style>

<script>
(function () {
  window.showPlatformRatingThankYou = function (opts) {
    opts = opts || {};
    const modalEl = document.getElementById('platformRatingThankYouModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    const starsEl = document.getElementById('platformRatingThankYouStars');
    const msgEl = document.getElementById('platformRatingThankYouMessage');
    const btn = document.getElementById('platformRatingThankYouBtn');
    const stars = parseInt(opts.stars, 10) || 0;

    if (starsEl && stars >= 1 && stars <= 5) {
      starsEl.innerHTML = '';
      for (let i = 1; i <= 5; i++) {
        const icon = document.createElement('i');
        icon.className = 'bi ' + (i <= stars ? 'bi-star-fill' : 'bi-star') + ' me-1';
        starsEl.appendChild(icon);
      }
      starsEl.setAttribute('aria-label', stars + ' out of 5 stars');
    }

    if (msgEl && opts.message) {
      msgEl.textContent = opts.message;
    }

    if (btn && opts.buttonLabel) {
      btn.textContent = opts.buttonLabel;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: true });
    modal.show();

    if (opts.autoRedirect && opts.redirectUrl) {
      const delay = parseInt(opts.redirectDelayMs, 10) || 2800;
      window.setTimeout(function () {
        window.location.href = opts.redirectUrl;
      }, delay);
    }
  };

  <?php if ($showOnLoad): ?>
  document.addEventListener('DOMContentLoaded', function () {
    window.showPlatformRatingThankYou({
      stars: <?= (int) $submittedStars ?>,
      <?php if ($autoRedirect && $redirectUrl !== ''): ?>
      autoRedirect: true,
      redirectUrl: <?= json_encode($redirectUrl) ?>,
      redirectDelayMs: <?= (int) $redirectDelayMs ?>,
      buttonLabel: 'Continuing...',
      message: 'Thank you for rating CSCS Tap n Track. You may continue shortly.',
      <?php endif; ?>
    });
  });
  <?php endif; ?>
})();
</script>
