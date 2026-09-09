<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="mb-3">
    <h1 class="h4 mb-1">Landing Page</h1>
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
    $announcementStrip = $announcementStrip ?? '';
    $lifelines = landing_lifelines();
    $qrUrl = landing_qr_code_url();
?>

<style>
.lp-row { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0.75rem; }
.lp-col { flex: 0 0 calc(50% - 0.375rem); max-width: calc(50% - 0.375rem); }
.lp-card { height: 100%; display: flex; flex-direction: column; }
.lp-card .card-body { flex: 1 1 auto; overflow: auto; padding: 0.75rem; }
.lp-preview-frame { border: 1px solid #e5e7eb; border-radius: 0.375rem; background: #fff; padding: 0.75rem; min-height: 300px; }
@media (max-width: 767px) {
    .lp-col { flex: 0 0 100%; max-width: 100%; }
}

/* Slide position controls - compact */
.slide-position-controls {
    display: flex;
    gap: 0.35rem;
    align-items: center;
    flex-wrap: nowrap;
    margin-top: 0.35rem;
}

.slide-position-controls input[type="range"] {
    flex: 1;
    min-width: 40px;
    height: 4px;
    -webkit-appearance: none;
    appearance: none;
    background: #e2e8f0;
    border-radius: 2px;
    outline: none;
}

.slide-position-controls input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
    border: 2px solid white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.slide-preview-thumb {
    position: relative;
    overflow: hidden;
    border-radius: 6px;
    background: #0b1530;
    cursor: grab;
    touch-action: none;
}

.slide-preview-thumb:active {
    cursor: grabbing;
}

.slide-preview-thumb img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    pointer-events: none;
    transition: none;
}

/* Live Preview Container */
.live-preview-container {
    background: #0b1530;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    aspect-ratio: 1983 / 793;
    max-height: 400px;
    border: 2px solid #1e3a8a;
}

.live-preview-container .preview-slide {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.5s ease;
}

.live-preview-container .preview-slide.is-active {
    opacity: 1;
    z-index: 1;
}

.live-preview-container .preview-slide img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: var(--preview-pos, 50% 50%);
}

.live-preview-dots {
    position: absolute;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 5;
    display: flex;
    gap: 0.5rem;
}

.live-preview-dots button {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.85);
    background: transparent;
    padding: 0;
    cursor: pointer;
    transition: all 0.2s;
}

.live-preview-dots button.is-active {
    background: #fbbf24;
    border-color: #fbbf24;
    transform: scale(1.15);
}

.live-preview-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.28);
    border: none;
    color: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    cursor: pointer;
    z-index: 5;
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 14px rgba(0,0,0,0.24);
}

.live-preview-nav:hover {
    background: rgba(0,0,0,0.45);
}

.live-preview-nav.prev { left: 10px; }
.live-preview-nav.next { right: 10px; }

.live-preview-nav i { font-size: 0.95rem; }

/* Preview announcement strip */
.preview-announcement-strip {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 6;
    background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 45%, #1d4ed8 100%);
    color: #fff;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
    border-bottom: 2px solid #fbbf24;
    padding: 0.35rem 0;
    font-size: 0.78rem;
    font-weight: 600;
}

.preview-announcement-viewport {
    overflow: hidden;
    width: 100%;
    mask-image: linear-gradient(90deg, transparent 0%, #000 3%, #000 97%, transparent 100%);
    -webkit-mask-image: linear-gradient(90deg, transparent 0%, #000 3%, #000 97%, transparent 100%);
}

.preview-announcement-track {
    display: flex;
    width: max-content;
    min-width: 200%;
    animation: previewStripScroll 28s linear infinite;
    will-change: transform;
}

.preview-announcement-group {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    min-width: 50vw;
    justify-content: center;
    gap: 0.5rem;
    padding: 0 1.5rem;
    white-space: nowrap;
}

.preview-announcement-icon {
    color: #fbbf24;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.preview-announcement-text {
    white-space: nowrap;
    font-size: 0.75rem;
}

@keyframes previewStripScroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

/* Preview lifelines */
.preview-lifelines {
    position: absolute;
    top: 3.2rem;
    right: 10px;
    display: flex;
    gap: 0.3rem;
    align-items: center;
    padding: 0.25rem 0.35rem;
    background: rgba(0,0,0,0.36);
    border-radius: 8px;
    z-index: 25;
    box-shadow: 0 6px 18px rgba(0,0,0,0.35);
}

.preview-lifeline-item {
    background: rgba(0,0,0,0.45);
    border-radius: 6px;
    display: flex;
    gap: 0.3rem;
    align-items: center;
    padding: 0.2rem 0.4rem;
}

.preview-lifeline-icon i {
    font-size: 0.75rem;
    color: #22c55e;
}

.preview-lifeline-status {
    font-weight: 700;
    color: #22c55e;
    font-size: 0.65rem;
}

.preview-lifeline-item.is-down .preview-lifeline-icon i,
.preview-lifeline-item.is-down .preview-lifeline-status {
    color: #ff4d4f;
}

/* Drag crosshair overlay */
.slide-position-overlay {
    position: absolute;
    inset: 0;
    z-index: 2;
    cursor: crosshair;
}

.slide-position-crosshair {
    position: absolute;
    width: 24px;
    height: 24px;
    border: 2px solid rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    pointer-events: none;
    z-index: 3;
    box-shadow: 0 0 0 1px rgba(0,0,0,0.3), 0 0 12px rgba(0,0,0,0.3);
    background: rgba(255, 255, 255, 0.15);
}

.slide-position-coords {
    position: absolute;
    bottom: 4px;
    right: 4px;
    z-index: 4;
    background: rgba(0,0,0,0.6);
    color: #fbbf24;
    font-size: 0.65rem;
    font-family: monospace;
    padding: 2px 6px;
    border-radius: 4px;
    pointer-events: none;
}
</style>

<form method="post" action="<?= base_url('admin/landing-page/update') ?>" enctype="multipart/form-data" id="landingForm">
    <?= csrf_field() ?>

    <!-- Row 1: Announcement + Lifelines (left) | Hero Slideshow (right) -->
    <div class="lp-row">
        <div class="lp-col">
            <div class="card border-0 shadow-sm lp-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i>Announcement Strip</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Scrolling message above hero. Leave empty to hide.</p>
                    <label class="form-label fw-semibold" for="announcement_strip">Announcement Text</label>
                    <textarea class="form-control" id="announcement_strip" name="announcement_strip" rows="5" maxlength="500" placeholder="Enter announcement text..."><?= esc($announcementStrip) ?></textarea>
                    <small class="text-muted d-block mt-1">Maximum 500 characters.</small>

                    <hr class="my-4">

                    <h5 class="mb-4"><i class="bi bi-heart-pulse me-2"></i>Lifelines</h5>
                    <p class="text-muted small mb-4">Service status shown on public home page.</p>
                    <div class="d-flex flex-column gap-2 flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between w-100 py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-droplet-fill text-primary"></i>
                                <span class="small fw-semibold">Water</span>
                            </div>
                            <select name="lifeline_water" class="form-select form-select-sm" style="width: 160px;">
                                <option value="FUNCTIONAL"<?= $lifelines['water'] === 'FUNCTIONAL' ? ' selected' : '' ?>>FUNCTIONAL</option>
                                <option value="NOT FUNCTIONAL"<?= $lifelines['water'] === 'NOT FUNCTIONAL' ? ' selected' : '' ?>>NOT FUNCTIONAL</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between w-100 py-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-wifi text-success"></i>
                                <span class="small fw-semibold">Communication</span>
                            </div>
                            <select name="lifeline_communication" class="form-select form-select-sm" style="width: 160px;">
                                <option value="FUNCTIONAL"<?= $lifelines['communication'] === 'FUNCTIONAL' ? ' selected' : '' ?>>FUNCTIONAL</option>
                                <option value="NOT FUNCTIONAL"<?= $lifelines['communication'] === 'NOT FUNCTIONAL' ? ' selected' : '' ?>>NOT FUNCTIONAL</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center justify-content-between w-100 py-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-lightning-charge-fill text-warning"></i>
                                <span class="small fw-semibold">Electricity</span>
                            </div>
                            <select name="lifeline_electricity" class="form-select form-select-sm" style="width: 160px;">
                                <option value="FUNCTIONAL"<?= $lifelines['electricity'] === 'FUNCTIONAL' ? ' selected' : '' ?>>FUNCTIONAL</option>
                                <option value="NOT FUNCTIONAL"<?= $lifelines['electricity'] === 'NOT FUNCTIONAL' ? ' selected' : '' ?>>NOT FUNCTIONAL</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4"><i class="bi bi-qr-code me-2"></i>Footer QR Code</h5>
                    <p class="text-muted small mb-2">QR code shown in the site footer beside the Facebook button.</p>
                    <div class="border rounded p-2 mb-2">
                        <?php if ($qrUrl !== ''): ?>
                            <img src="<?= esc($qrUrl) ?>" alt="Footer QR Code" class="img-fluid mb-2" style="max-height: 90px; max-width: 90px; display: block;">
                        <?php else: ?>
                            <div class="bg-light border rounded mb-2" style="height: 70px; width: 70px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.8rem;">No QR code</div>
                        <?php endif; ?>
                        <input type="file" class="form-control form-control-sm" name="footer_qr_code" accept="image/png,image/jpeg,image/webp">
                        <input type="hidden" name="remove_qr_code" value="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="lp-col">
            <div class="card border-0 shadow-sm lp-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-images me-2"></i>Hero Slideshow</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Upload up to 3 wide banner images (2.5:1 aspect ratio). <strong>Drag on the preview to adjust image position.</strong></p>
                    <?php for ($slot = 1; $slot <= 3; $slot++): ?>
                        <?php $slide = $slides[$slot] ?? null; ?>
                        <?php $pos = $slide['position'] ?? '50% 50%'; ?>
                        <?php $posParts = explode(' ', $pos); $posX = (int) rtrim($posParts[0] ?? '50', '%'); $posY = (int) rtrim($posParts[1] ?? '50', '%'); ?>
                        <?php $scale = $slide['scale'] ?? 1; ?>
                        <div class="border rounded p-3 mb-3">
                            <h6 class="fw-bold">Slide <?= $slot ?></h6>
                            <div class="slide-preview-thumb mb-2" style="height: 140px;" data-slot="<?= $slot ?>" data-posx="<?= $posX ?>" data-posy="<?= $posY ?>">
                                <?php if (!empty($slide['url'])): ?>
                                    <img src="<?= esc($slide['url']) ?>" alt="Slide <?= $slot ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: <?= $posX ?>% <?= $posY ?>%;">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #666; font-size: 0.85rem; background: #1a1a2e;">No image uploaded</div>
                                <?php endif; ?>
                                <div class="slide-position-overlay" data-slot="<?= $slot ?>"></div>
                                <div class="slide-position-crosshair" style="left: <?= $posX ?>%; top: <?= $posY ?>%;"></div>
                                <div class="slide-position-coords"><?= $posX ?>% <?= $posY ?>%</div>
                            </div>
                            <input type="file" class="form-control form-control-sm mb-2" name="hero_slide_<?= $slot ?>" accept="image/jpeg,image/png,image/webp">
                            
                            <div class="slide-position-controls">
                                <input type="range" class="slide-pos-x" min="0" max="100" value="<?= $posX ?>" data-slot="<?= $slot ?>" title="Horizontal position">
                                <input type="range" class="slide-pos-y" min="0" max="100" value="<?= $posY ?>" data-slot="<?= $slot ?>" title="Vertical position">
                            </div>
                            
                            <div class="slide-position-controls mt-1">
                                <input type="range" class="slide-scale" min="100" max="250" value="<?= (int)($scale * 100) ?>" data-slot="<?= $slot ?>" title="Zoom level">
                            </div>

                            <input type="hidden" name="hero_slide_<?= $slot ?>_position" id="posInput_<?= $slot ?>" value="<?= esc($pos) ?>">
                            <input type="hidden" name="hero_slide_<?= $slot ?>_scale" id="scaleInput_<?= $slot ?>" value="<?= esc($scale) ?>">
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Preview Row -->
    <div class="lp-row" style="align-items: stretch;">
        <div class="lp-col" style="flex: 0 0 70%; max-width: 70%;">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-eye-fill me-2"></i>Live Preview</h5>
                </div>
                <div class="card-body p-3 d-flex flex-column">
                    <div class="live-preview-container" id="livePreview">
                        <?php 
                        // Collect only slides with images
                        $previewSlides = [];
                        for ($slot = 1; $slot <= 3; $slot++): 
                            $slide = $slides[$slot] ?? null;
                            if (!empty($slide['url'])):
                                $previewSlides[] = [
                                    'slot' => $slot,
                                    'url' => $slide['url'],
                                    'pos' => $slide['position'] ?? '50% 50%'
                                ];
                            endif;
                        endfor; 
                        ?>
                        <?php if (!empty($previewSlides)): ?>
                            <?php foreach ($previewSlides as $idx => $ps): ?>
                                <div class="preview-slide <?= $idx === 0 ? 'is-active' : '' ?>" data-preview-index="<?= $idx ?>">
                                    <img src="<?= esc($ps['url']) ?>" alt="Preview Slide <?= $ps['slot'] ?>" style="--preview-pos: <?= esc($ps['pos']) ?>;">
                                </div>
                            <?php endforeach; ?>
                            
                            <!-- Announcement Strip -->
                            <?php if ($announcementStrip !== ''): ?>
                            <div class="preview-announcement-strip">
                                <div class="preview-announcement-viewport">
                                    <div class="preview-announcement-track">
                                        <div class="preview-announcement-group">
                                            <span class="preview-announcement-icon"><i class="bi bi-megaphone-fill"></i></span>
                                            <span class="preview-announcement-text"><?= esc($announcementStrip) ?></span>
                                        </div>
                                        <div class="preview-announcement-group" aria-hidden="true">
                                            <span class="preview-announcement-icon"><i class="bi bi-megaphone-fill"></i></span>
                                            <span class="preview-announcement-text"><?= esc($announcementStrip) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Lifelines -->
                            <div class="preview-lifelines">
                                <div class="preview-lifeline-item <?= $lifelines['water'] !== 'FUNCTIONAL' ? 'is-down' : '' ?>">
                                    <span class="preview-lifeline-icon"><i class="bi bi-droplet-fill"></i></span>
                                    <span class="preview-lifeline-status"><?= $lifelines['water'] === 'FUNCTIONAL' ? 'ON' : 'OFF' ?></span>
                                </div>
                                <div class="preview-lifeline-item <?= $lifelines['communication'] !== 'FUNCTIONAL' ? 'is-down' : '' ?>">
                                    <span class="preview-lifeline-icon"><i class="bi bi-wifi"></i></span>
                                    <span class="preview-lifeline-status"><?= $lifelines['communication'] === 'FUNCTIONAL' ? 'ON' : 'OFF' ?></span>
                                </div>
                                <div class="preview-lifeline-item <?= $lifelines['electricity'] !== 'FUNCTIONAL' ? 'is-down' : '' ?>">
                                    <span class="preview-lifeline-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                                    <span class="preview-lifeline-status"><?= $lifelines['electricity'] === 'FUNCTIONAL' ? 'ON' : 'OFF' ?></span>
                                </div>
                            </div>
                            
                            <?php if (count($previewSlides) > 1): ?>
                            <div class="live-preview-dots">
                                <?php foreach ($previewSlides as $idx => $ps): ?>
                                    <button type="button" class="preview-dot <?= $idx === 0 ? 'is-active' : '' ?>" data-preview-index="<?= $idx ?>" aria-label="View slide <?= $ps['slot'] ?>"></button>
                                <?php endforeach; ?>
                            </div>
                            
                            <button type="button" class="live-preview-nav prev" id="previewPrev" aria-label="Previous slide">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button type="button" class="live-preview-nav next" id="previewNext" aria-label="Next slide">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.3);font-size:1rem;background:#0b1530;flex-direction:column;gap:0.5rem;">
                                <i class="bi bi-images" style="font-size:2rem;"></i>
                                <span>Upload slides to see preview</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="lp-col" style="flex: 0 0 25%; max-width: 25%;">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Info</h5>
                </div>
                <div class="card-body p-3">
                    <p style="color:#000 !important; margin-bottom: 0.75rem; font-size: 0.875rem; line-height: 1.6;">
                        <i class="bi bi-info-circle me-1"></i><strong style="color:#000;">Landing Page Controls</strong><br><br>
                        • <strong>Hero Slideshow:</strong> Upload 3 banners (2.5:1) for events. Drag or use sliders for position/zoom.<br><br>
                        • <strong>Announcement Ticker:</strong> Scrolling text above hero for notices and reminders.<br><br>
                        • <strong>Lifelines:</strong> Set Water, Communication, Electricity status. Green = ON, Red = OFF.<br><br>
                        • <strong>QR Code:</strong> Upload QR for footer. Share links or contact info.<br><br>
                        • <strong>Live Preview:</strong> Preview instantly. Changes apply after saving.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="lp-row">
        <div class="lp-col" style="flex: 0 0 100%; max-width: 100%;">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-2"></i>Save Changes
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Live Preview Navigation (index-based) ---
    let currentPreview = 0;
    const previewSlides = document.querySelectorAll('.preview-slide');
    const totalPreviews = previewSlides.length;
    
    function showPreviewSlide(index) {
        if (totalPreviews === 0) return;
        if (index < 0) index = totalPreviews - 1;
        if (index >= totalPreviews) index = 0;
        currentPreview = index;
        
        previewSlides.forEach(el => {
            el.classList.toggle('is-active', parseInt(el.dataset.previewIndex) === index);
        });
        document.querySelectorAll('.preview-dot').forEach(el => {
            el.classList.toggle('is-active', parseInt(el.dataset.previewIndex) === index);
        });
    }
    
    document.querySelectorAll('.preview-dot').forEach(dot => {
        dot.addEventListener('click', function() {
            showPreviewSlide(parseInt(this.dataset.previewIndex));
        });
    });
    
    document.getElementById('previewPrev')?.addEventListener('click', function() {
        showPreviewSlide(currentPreview - 1);
    });
    
    document.getElementById('previewNext')?.addEventListener('click', function() {
        showPreviewSlide(currentPreview + 1);
    });
    
    // Auto-rotate preview every 5 seconds (only if multiple slides)
    let previewInterval;
    if (totalPreviews > 1) {
        previewInterval = setInterval(() => showPreviewSlide(currentPreview + 1), 5000);
    }
    
    const livePreview = document.getElementById('livePreview');
    if (livePreview) {
        livePreview.addEventListener('mouseenter', function() {
            clearInterval(previewInterval);
        });
        livePreview.addEventListener('mouseleave', function() {
            if (totalPreviews > 1) {
                previewInterval = setInterval(() => showPreviewSlide(currentPreview + 1), 5000);
            }
        });
    }

    // --- Slide Position Controls ---
    function updateSlideCrosshair(slot, x, y) {
        const thumb = document.querySelector('.slide-preview-thumb[data-slot="' + slot + '"]');
        if (!thumb) return;
        const crosshair = thumb.querySelector('.slide-position-crosshair');
        if (crosshair) {
            crosshair.style.left = x + '%';
            crosshair.style.top = y + '%';
        }
        const coords = thumb.querySelector('.slide-position-coords');
        if (coords) {
            coords.textContent = x + '% ' + y + '%';
        }
    }
    
    function updateSlidePreview(slot, x, y) {
        const previewSlide = document.querySelector('.preview-slide[data-preview-index="' + (slot - 1) + '"]');
        if (previewSlide) {
            const img = previewSlide.querySelector('img');
            if (img) {
                img.style.setProperty('--preview-pos', x + '% ' + y + '%');
            }
        }
    }
    
    function updateSlidePosition(slot, x, y) {
        const posStr = x + '% ' + y + '%';
        document.getElementById('posInput_' + slot).value = posStr;
        
        // Update thumb image position
        const thumb = document.querySelector('.slide-preview-thumb[data-slot="' + slot + '"]');
        if (thumb) {
            const img = thumb.querySelector('img');
            if (img) {
                img.style.objectPosition = posStr;
            }
        }
        
        updateSlideCrosshair(slot, x, y);
        updateSlidePreview(slot, x, y);
    }
    
    // Slider controls
    document.querySelectorAll('.slide-pos-x').forEach(slider => {
        slider.addEventListener('input', function() {
            const slot = parseInt(this.dataset.slot);
            const x = parseInt(this.value);
            const y = parseInt(document.querySelector('.slide-pos-y[data-slot="' + slot + '"]').value);
            updateSlidePosition(slot, x, y);
        });
    });
    
    document.querySelectorAll('.slide-pos-y').forEach(slider => {
        slider.addEventListener('input', function() {
            const slot = parseInt(this.dataset.slot);
            const y = parseInt(this.value);
            const x = parseInt(document.querySelector('.slide-pos-x[data-slot="' + slot + '"]').value);
            updateSlidePosition(slot, x, y);
        });
    });
    
    document.querySelectorAll('.slide-scale').forEach(slider => {
        slider.addEventListener('input', function() {
            const slot = this.dataset.slot;
            const val = parseInt(this.value);
            const scaleVal = (val / 100).toFixed(2);
            document.getElementById('scaleInput_' + slot).value = scaleVal;
            
            const thumb = document.querySelector('.slide-preview-thumb[data-slot="' + slot + '"]');
            if (thumb) {
                const img = thumb.querySelector('img');
                if (img) {
                    img.style.transform = 'scale(' + scaleVal + ')';
                }
            }
        });
    });
    
    // --- Drag to position on thumbnails ---
    document.querySelectorAll('.slide-position-overlay').forEach(overlay => {
        const slot = parseInt(overlay.dataset.slot);
        let isDragging = false;
        
        function setPositionFromEvent(e) {
            const thumb = overlay.closest('.slide-preview-thumb');
            const rect = thumb.getBoundingClientRect();
            const clientX = e.clientX || (e.touches && e.touches[0].clientX);
            const clientY = e.clientY || (e.touches && e.touches[0].clientY);
            
            if (clientX === undefined) return;
            
            let x = ((clientX - rect.left) / rect.width) * 100;
            let y = ((clientY - rect.top) / rect.height) * 100;
            x = Math.max(0, Math.min(100, Math.round(x)));
            y = Math.max(0, Math.min(100, Math.round(y)));
            
            // Update sliders
            const xSlider = document.querySelector('.slide-pos-x[data-slot="' + slot + '"]');
            const ySlider = document.querySelector('.slide-pos-y[data-slot="' + slot + '"]');
            if (xSlider) xSlider.value = x;
            if (ySlider) ySlider.value = y;
            
            updateSlidePosition(slot, x, y);
        }
        
        overlay.addEventListener('mousedown', function(e) {
            isDragging = true;
            setPositionFromEvent(e);
            e.preventDefault();
        });
        
        document.addEventListener('mousemove', function(e) {
            if (isDragging) {
                setPositionFromEvent(e);
                e.preventDefault();
            }
        });
        
        document.addEventListener('mouseup', function() {
            isDragging = false;
        });
        
        // Touch support
        overlay.addEventListener('touchstart', function(e) {
            isDragging = true;
            setPositionFromEvent(e);
            e.preventDefault();
        }, { passive: false });
        
        overlay.addEventListener('touchmove', function(e) {
            if (isDragging) {
                setPositionFromEvent(e);
                e.preventDefault();
            }
        }, { passive: false });
        
        overlay.addEventListener('touchend', function() {
            isDragging = false;
        });
    });
});
</script>

<?= $this->endSection() ?>