<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<link href="<?= asset_url('css/childpro-gad.css') ?>" rel="stylesheet" />

<section class="childpro-gad-page" id="landing">
    <?php helper('landing'); ?>
    <?php if (landing_strip_enabled()): ?>
    <div id="announcement-strip-cg">
        <div class="landing-announcement-strip__viewport">
            <div class="landing-announcement-strip__track">
                <div class="landing-announcement-strip__group">
                    <?php for ($repeat = 0; $repeat < 4; $repeat++): ?>
                    <span class="landing-announcement-strip__item">
                        <i class="bi bi-megaphone-fill announcement-strip-icon" aria-hidden="true"></i>
                        <span class="announcement-strip-text"><?= landing_announcement_strip_html() ?></span>
                    </span>
                    <span class="announcement-strip-sep" aria-hidden="true">◆</span>
                    <?php endfor; ?>
                </div>
                <div class="landing-announcement-strip__group" aria-hidden="true">
                    <?php for ($repeat = 0; $repeat < 4; $repeat++): ?>
                    <span class="landing-announcement-strip__item">
                        <i class="bi bi-megaphone-fill announcement-strip-icon" aria-hidden="true"></i>
                        <span class="announcement-strip-text"><?= landing_announcement_strip_html() ?></span>
                    </span>
                    <span class="announcement-strip-sep" aria-hidden="true">◆</span>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($heroUrl !== '' || $hero['title'] !== '' || $hero['description'] !== ''): ?>
    <?php if ($heroUrl !== ''): ?>
    <!-- Hero with background image -->
    <div class="cg-hero cg-hero--with-image">
        <div class="cg-hero__stage">
            <img
                src="<?= esc($heroUrl) ?>"
                alt="<?= esc($hero['title'] ?: programs_projects_tab_label($tab) . ' hero') ?>"
                class="cg-hero__img"
                loading="lazy"
                decoding="async"
                style="--hero-object-position: <?= esc($hero['position'] ?? '50% 50%') ?>; object-position: <?= esc($hero['position'] ?? '50% 50%') ?>; transform: scale(<?= esc(number_format((float)($hero['scale'] ?? 1), 2, '.', '')) ?>);"
            >
            <?php if ($hero['title'] !== '' || $hero['description'] !== ''): ?>
            <div class="cg-hero__overlay">
                <?php if ($hero['title'] !== ''): ?>
                <h1 class="cg-hero__title"><?= esc($hero['title']) ?></h1>
                <?php endif; ?>
                <?php if ($hero['description'] !== ''): ?>
                <p class="cg-hero__desc"><?= esc($hero['description']) ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <!-- Hero text-only -->
    <div class="cg-hero">
        <div class="cg-hero__inner">
            <?php if ($hero['title'] !== ''): ?>
            <h1 class="cg-hero__title"><?= esc($hero['title']) ?></h1>
            <?php endif; ?>
            <?php if ($hero['description'] !== ''): ?>
            <p class="cg-hero__desc"><?= esc($hero['description']) ?></p>
            <?php endif; ?>
            <div class="cg-hero__accent" aria-hidden="true"></div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Content Sections — only show sections that have a title filled in -->
    <?php
        $visibleSections = array_filter($sections ?? [], function($s) {
            return trim($s['title'] ?? '') !== '';
        });
    ?>
    <?php if (count($visibleSections) > 0): ?>
        <?php foreach ($visibleSections as $index => $section): ?>
        <div class="cg-section<?= $index % 2 === 0 ? ' section-light' : ' section-dark' ?>">
            <div class="cg-section__inner">
                <div class="cg-section__body<?= $index % 2 === 0 ? '' : ' cg-section__body--alt' ?>">
                    <div class="cg-section__content">
                        <h2 class="section-title"><?= esc($section['title']) ?></h2>
                        <p class="section-subtitle"><?= nl2br(esc($section['description'] ?? '')) ?></p>
                    </div>
                    <div class="cg-section__media">
                        <?php if (isset($section['media_type']) && $section['media_type'] === 'video' && !empty($section['is_youtube']) && !empty($section['embed_url'])): ?>
                            <iframe
                                src="<?= esc($section['embed_url']) ?>"
                                title="<?= esc($section['title']) ?>"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                loading="lazy"
                            ></iframe>
                        <?php elseif (isset($section['media_type']) && $section['media_type'] === 'video' && !empty($section['media_url'])): ?>
                            <video controls preload="metadata" aria-label="<?= esc($section['title']) ?>">
                                <source src="<?= esc($section['display_url']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php elseif (!empty($section['display_url'])): ?>
                            <img
                                src="<?= esc($section['display_url']) ?>"
                                alt="<?= esc($section['title']) ?>"
                                loading="lazy"
                                decoding="async"
                            >
                        <?php else: ?>
                            <div style="padding: 2rem; text-align: center; background: #e2e8f0; color: #94a3b8; border-radius: 12px;">
                                <i class="bi bi-image" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                                <span style="font-size: 0.85rem;">No media added</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
    <!-- Empty state — only show if there's no hero AND no sections -->
    <?php if ($heroUrl === '' && $hero['title'] === ''): ?>
    <div class="cg-empty">
        <i class="bi bi-file-earmark-plus" aria-hidden="true"></i>
        <h2>Content coming soon</h2>
        <p>The <?= esc(programs_projects_tab_label($tab)) ?> page content is being prepared. Please check back later.</p>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (landing_strip_enabled()): ?>
    <div id="announcement-strip-cg">
        <div class="landing-announcement-strip__viewport">
            <div class="landing-announcement-strip__track">
                <div class="landing-announcement-strip__group">
                    <?php for ($repeat = 0; $repeat < 4; $repeat++): ?>
                    <span class="landing-announcement-strip__item">
                        <i class="bi bi-megaphone-fill announcement-strip-icon" aria-hidden="true"></i>
                        <span class="announcement-strip-text"><?= landing_announcement_strip_html() ?></span>
                    </span>
                    <span class="announcement-strip-sep" aria-hidden="true">◆</span>
                    <?php endfor; ?>
                </div>
                <div class="landing-announcement-strip__group" aria-hidden="true">
                    <?php for ($repeat = 0; $repeat < 4; $repeat++): ?>
                    <span class="landing-announcement-strip__item">
                        <i class="bi bi-megaphone-fill announcement-strip-icon" aria-hidden="true"></i>
                        <span class="announcement-strip-text"><?= landing_announcement_strip_html() ?></span>
                    </span>
                    <span class="announcement-strip-sep" aria-hidden="true">◆</span>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>