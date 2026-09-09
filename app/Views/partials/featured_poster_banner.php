<?php
/**
 * Featured dashboard poster banner (teacher or student).
 *
 * @var string $posterUrl      Public image URL (empty if none)
 * @var string $posterLabel    e.g. "Teacher Dashboard"
 * @var string $minHeight      CSS min-height, e.g. "280px" or "440px"
 * @var string $wrapperClass   Optional extra class on outer card/banner
 */
$posterUrl     = $posterUrl ?? '';
$posterLabel   = $posterLabel ?? 'Dashboard';
$minHeight     = $minHeight ?? '280px';
$wrapperClass  = trim($wrapperClass ?? '');
?>
<div class="featured-poster-banner <?= esc($wrapperClass) ?>"
     style="<?= $minHeight !== '280px' ? 'height:' . esc($minHeight) . ';min-height:' . esc($minHeight) . ';max-height:' . esc($minHeight) . ';' : '' ?>">
  <?php if ($posterUrl !== ''): ?>
    <img
      src="<?= esc($posterUrl) ?>"
      alt="Featured poster"
      class="featured-poster-banner__img"
      onerror="this.style.display='none';"
    >
  <?php endif; ?>
  <div class="featured-poster-banner__label" style="position:absolute; left:16px; bottom:16px; color:white; z-index:1; pointer-events:none;">
    <div style="font-weight:900; font-size:1.15rem; line-height:1.2;">Featured</div>
    <div style="font-size:0.9rem; opacity:0.95;"><?= esc($posterLabel) ?></div>
    <?php if ($posterUrl === ''): ?>
      <div style="font-size:0.8rem; opacity:0.9;">(No poster uploaded yet)</div>
    <?php endif; ?>
  </div>
</div>
