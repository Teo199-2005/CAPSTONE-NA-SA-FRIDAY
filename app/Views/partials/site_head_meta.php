<?php
/**
 * Favicon (browser tab) and Open Graph / Twitter meta for link previews (e.g. Facebook).
 *
 * Optional view data:
 *   $headMetaTitle       — page title for og:title
 *   $headMetaDescription — short description for og:description
 *   $headMetaUrl         — canonical URL (defaults to current_url())
 */
helper('asset');

$headMetaTitle = (string) ($headMetaTitle ?? $title ?? 'CSCS Tap n Track');
$headMetaDescription = (string) ($headMetaDescription ?? 'Cauayan South Central School — CSCS Tap n Track school management portal.');
$headMetaUrl = (string) ($headMetaUrl ?? (function_exists('current_url') ? current_url() : ''));
$logoUrl = school_logo_absolute_url();
$logoType = 'image/png';
?>
<link rel="icon" href="<?= esc($logoUrl, 'attr') ?>" type="<?= esc($logoType, 'attr') ?>" sizes="any" />
<link rel="shortcut icon" href="<?= esc($logoUrl, 'attr') ?>" type="<?= esc($logoType, 'attr') ?>" />
<link rel="apple-touch-icon" href="<?= esc($logoUrl, 'attr') ?>" />
<meta name="description" content="<?= esc($headMetaDescription, 'attr') ?>" />
<meta property="og:type" content="website" />
<meta property="og:site_name" content="CSCS Tap n Track" />
<meta property="og:title" content="<?= esc($headMetaTitle, 'attr') ?>" />
<meta property="og:description" content="<?= esc($headMetaDescription, 'attr') ?>" />
<meta property="og:image" content="<?= esc($logoUrl, 'attr') ?>" />
<meta property="og:image:type" content="<?= esc($logoType, 'attr') ?>" />
<meta property="og:image:alt" content="Cauayan South Central School logo" />
<?php if ($headMetaUrl !== ''): ?>
<meta property="og:url" content="<?= esc($headMetaUrl, 'attr') ?>" />
<?php endif; ?>
<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="<?= esc($headMetaTitle, 'attr') ?>" />
<meta name="twitter:description" content="<?= esc($headMetaDescription, 'attr') ?>" />
<meta name="twitter:image" content="<?= esc($logoUrl, 'attr') ?>" />
