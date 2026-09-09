<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5.0" />
  <title><?= esc($title ?? 'CSCS Tap n Track - Cauayan South Central School Management System') ?></title>
  
  <!-- Canonical URL -->
  <link rel="canonical" href="<?= current_url() ?>" />
  
  <!-- Meta Robots -->
  <meta name="robots" content="index, follow, max-image-preview:large" />
  
  <?= view('partials/site_head_meta', [
    'headMetaTitle' => $title ?? 'CSCS Tap n Track - Cauayan South Central School',
    'headMetaDescription' => $headMetaDescription ?? 'Cauayan South Central School Management System - Modern Education Management Platform for Students, Teachers, and Administrators.',
  ]) ?>
  
  <!-- Open Graph locale (regional) -->
  <meta property="og:locale" content="en_PH" />
  
  <!-- DNS Prefetch / Preconnect for third-party resources -->
  <link rel="dns-prefetch" href="https://cdn.jsdelivr.net" />
  <link rel="dns-prefetch" href="https://fonts.googleapis.com" />
  <link rel="dns-prefetch" href="https://fonts.gstatic.com" />
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  
  <!-- Stylesheets -->
  <link rel="preload" href="<?= asset_url('css/app.css') ?>" as="style" />
  <link href="<?= asset_url('css/app.css') ?>" rel="stylesheet" />
  <link href="<?= asset_url('css/responsive.css') ?>" rel="stylesheet" />
  <link href="<?= asset_url('css/auth-mobile.css') ?>" rel="stylesheet" />
  <link href="<?= asset_url('css/ui-helpers.css') ?>" rel="stylesheet" />
  
  <!-- Third-party CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" as="style" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  
  <!-- Font loading with swap for performance -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <!-- Sitemap -->
  <link rel="sitemap" type="application/xml" title="Sitemap" href="<?= base_url('sitemap.xml') ?>" />
</head>
<body>
  <a href="#main-content" class="skip-nav-link">Skip to main content</a>
  <header class="site-header py-2" role="banner">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4 site-header-inner">
      <?php
        $brandHref = base_url();
        if (!empty($loggedIn) && $loggedIn) {
          $brandHref = base_url('dashboard');
        }
      ?>
      <a class="site-brand flex-shrink-0" href="<?= $brandHref ?>" aria-label="CSCS Tap n Track Home">
          <img src="<?= asset_url('LPHS2.png') ?>" alt="CSCS Logo" width="64" height="64" loading="lazy" />
        <span class="site-brand-title-text">Cauayan South Central School</span>
      </a>
      <button class="nav-toggle d-md-none" type="button" aria-label="Toggle navigation" id="navToggle" aria-controls="siteNav" aria-expanded="false">
        <i class="bi bi-list" aria-hidden="true"></i>
      </button>
      <nav class="site-nav ms-auto" id="siteNav" role="navigation" aria-label="Main navigation">
        <?php
          $loggedIn = false;
          $user = null;
          try {
            $authService = auth();
            $loggedIn = $authService->loggedIn();
            if ($loggedIn) {
              $user = $authService->user();
            }
          } catch (\Throwable $e) {
            $loggedIn = false;
          }

          $currentPath = rtrim(parse_url(current_url(), PHP_URL_PATH) ?: '', '/');
          $currentSegment = trim(str_replace(rtrim(parse_url(base_url(), PHP_URL_PATH) ?: '', '/'), '', $currentPath), '/');
          if (empty($currentSegment)) {
            $currentSegment = 'home';
          }
        ?>
        <?php if ($loggedIn): ?>
          <span class="text-white nav-user-greeting">Welcome, <?= esc($user->email) ?></span>

          <?php if ($user && function_exists('user_is_any_admin') && user_is_any_admin($user)): ?>
            <a href="<?= base_url('admin/dashboard') ?>" class="text-white text-decoration-none me-3" title="Admin Dashboard"><i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard</a>
          <?php elseif ($user && $user->inGroup('teacher')): ?>
            <a href="<?= base_url('teacher/dashboard') ?>" class="text-white text-decoration-none me-3" title="Teacher Dashboard"><i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard</a>
          <?php elseif ($user && $user->inGroup('student')): ?>
            <a href="<?= base_url('student/dashboard') ?>" class="text-white text-decoration-none me-3" title="Student Dashboard"><i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard</a>
          <?php elseif ($user && $user->inGroup('parent')): ?>
            <a href="<?= base_url('parent/dashboard') ?>" class="text-white text-decoration-none me-3" title="Parent Dashboard"><i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>Dashboard</a>
          <?php endif; ?>

          <a href="<?= base_url('announcements') ?>" class="text-white text-decoration-none me-3" title="View Announcements"><i class="bi bi-megaphone me-1" aria-hidden="true"></i>Announcements</a>
          <a href="#" class="text-white text-decoration-none me-3" data-bs-toggle="modal" data-bs-target="#schoolMaterialsModal" role="button" title="School Materials"><i class="bi bi-folder2-open me-1" aria-hidden="true"></i>Materials</a>
          <div class="nav-item-dropdown">
            <a href="#" class="text-white text-decoration-none me-3" id="transparencyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Transparency <i class="bi bi-chevron-down" style="font-size: 0.7rem;" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="transparencyDropdown">
              <li>
                <a class="dropdown-item text-white" href="<?= base_url('about') ?>">
                  <i class="bi bi-building me-2" aria-hidden="true"></i>About
                </a>
              </li>
              <li>
                <a class="dropdown-item text-white" href="#" data-bs-toggle="modal" data-bs-target="#citizensCharterModal">
                  <i class="bi bi-file-earmark-text me-2" aria-hidden="true"></i>Citizens Charter
                </a>
              </li>
              <li>
                <a class="dropdown-item text-white" href="<?= base_url('programs') ?>">
                  <i class="bi bi-gear me-2" aria-hidden="true"></i>Programs and Projects
                </a>
              </li>
            </ul>
          </div>
          <a href="<?= base_url('logout') ?>" class="text-white text-decoration-none" title="Sign Out"><i class="bi bi-box-arrow-left me-1" aria-hidden="true"></i>Logout</a>
        <?php else: ?>
          <a href="<?= base_url() ?>" class="text-white text-decoration-none me-3<?= $currentSegment === 'home' ? ' active' : '' ?>" title="Home Page"><i class="bi bi-house me-1" aria-hidden="true"></i>Home</a>
          <a href="#" class="text-white text-decoration-none me-3" id="transparencyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-building me-1" aria-hidden="true"></i>Transparency <i class="bi bi-chevron-down" style="font-size: 0.7rem;" aria-hidden="true"></i>
          </a>
          <ul class="dropdown-menu" aria-labelledby="transparencyDropdown">
            <li>
              <a class="dropdown-item text-white" href="<?= base_url('about') ?>">
                <i class="bi bi-building me-2" aria-hidden="true"></i>About
              </a>
            </li>
            <li>
              <a class="dropdown-item text-white" href="#" data-bs-toggle="modal" data-bs-target="#citizensCharterModal">
                <i class="bi bi-file-earmark-text me-2" aria-hidden="true"></i>Citizens Charter
              </a>
            </li>
            <li>
              <a class="dropdown-item text-white" href="<?= base_url('programs') ?>">
                <i class="bi bi-gear me-2" aria-hidden="true"></i>Programs and Projects
              </a>
            </li>
          </ul>
          <a href="#" class="text-white text-decoration-none me-3" data-bs-toggle="modal" data-bs-target="#schoolMaterialsModal" role="button" title="School Materials"><i class="bi bi-folder2-open me-1" aria-hidden="true"></i>Materials</a>
          <a href="<?= base_url('childpro') ?>" class="text-white text-decoration-none me-3<?= str_starts_with($currentSegment, 'childpro') ? ' active' : '' ?>" title="CHILDPRO Programs">
            <i class="bi bi-people me-1" aria-hidden="true"></i>CHILDPRO
          </a>
          <a href="<?= base_url('gad') ?>" class="text-white text-decoration-none me-3<?= str_starts_with($currentSegment, 'gad') ? ' active' : '' ?>" title="GAD Programs">
            <i class="bi bi-gender-ambiguous me-1" aria-hidden="true"></i>GAD
          </a>
          <a href="<?= base_url('login') ?>" class="text-white text-decoration-none me-3<?= $currentSegment === 'login' ? ' active' : '' ?>" title="Sign In"><i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>Login</a>
          <?php 
          try {
            $systemSettingModel = new \App\Models\SystemSettingModel();
            $registrationSetting = $systemSettingModel->getSetting('registration_enabled', null);
            if ($registrationSetting === null) {
              $registrationSetting = $systemSettingModel->getSetting('enrollment_enabled', 1);
            }
            $registrationEnabled = (bool) $registrationSetting;
          } catch (\Throwable $e) {
            $registrationEnabled = true;
          }
          ?>
          <?php if ($registrationEnabled): ?>
          <a href="<?= base_url('register') ?>" class="text-white text-decoration-none btn btn-accent<?= $currentSegment === 'register' ? ' active' : '' ?>" title="Create Account">Register</a>
          <?php else: ?>
          <a href="#" class="text-white text-decoration-none btn btn-secondary register-disabled-btn" id="registerDisabledBtn" title="Registration Closed">Register</a>
          <?php endif; ?>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main id="main-content" role="main">
    <?= $this->renderSection('content') ?>
  </main>

  <!-- Footer Divider -->
  <div class="footer-divider" aria-hidden="true"></div>
  
  <footer class="site-footer" role="contentinfo" style="min-height: 100px; margin-top: -90px; position: relative; z-index: 30;">
    <div class="footer-container">
      <div class="footer-grid">
        
        <!-- Left: Brand -->
        <div class="footer-brand">
          <img src="<?= asset_url('LPHS2.png') ?>" alt="CSCS Logo" width="60" height="60" loading="lazy" />
          <div>
            <h4>CSCS Tap n Track</h4>
            <p>Modern Education Management</p>
          </div>
        </div>
        
        <!-- Center: Contact Grid -->
        <div class="footer-contact">
          <div>
            <i class="bi bi-geo-alt footer-contact-icon" aria-hidden="true"></i>
            <a href="https://www.google.com/maps/search/?api=1&query=WQJC%2BMM7%2C+Cauayan+City" target="_blank" rel="noopener">Mabini Street, District I, Cauayan City, Isabela, Philippines</a>
          </div>
          <div>
            <i class="bi bi-envelope footer-contact-icon" aria-hidden="true"></i>
            <span>Principal: Ronnie G. Rumbaoa</span>
          </div>
          <div>
            <i class="bi bi-clock footer-contact-icon footer-contact-icon--green" aria-hidden="true"></i>
            <span>24/7 System Access</span>
          </div>
        </div>
        
        <!-- Right: Social & QR Code -->
        <div class="footer-right">
          <div class="footer-social-row">
            <a href="https://www.facebook.com/cauayan.south.central" target="_blank" rel="noopener" class="footer-facebook" title="Follow us on Facebook" aria-label="CSCS Facebook Page">
              <i class="bi bi-facebook" aria-hidden="true"></i>
            </a>
            <?php helper('landing'); ?>
            <?php $qrUrl = landing_qr_code_url(); ?>
            <div class="footer-qr" title="Scan QR Code">
              <?php if ($qrUrl !== ''): ?>
                <img src="<?= esc($qrUrl) ?>" alt="QR Code for CSCS Portal" width="42" height="42" loading="lazy" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'bi bi-qr-code\' aria-hidden=\'true\'></i>';" />
              <?php else: ?>
                <i class="bi bi-qr-code" aria-hidden="true"></i>
              <?php endif; ?>
            </div>
          </div>
          <div class="footer-meta">
            <div>&copy; 2026 CSCS Tap n Track</div>
            <div>Version 2.1.0</div>
          </div>
        </div>
        
      </div>
    </div>
  </footer>

  <?= view('partials/citizens_charter_modal') ?>
  <?= view('partials/public_materials_modal') ?>

  <!-- Scripts -->
  <script src="<?= asset_url('js/ui-helpers.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Local Bootstrap fallback if CDN fails -->
  <script>
    if (typeof bootstrap === 'undefined') {
      document.write('<script src="<?= asset_url('js/bootstrap.bundle.min.js') ?>"><\/script>');
    }
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Nav toggle with ARIA state management
      var navToggle = document.getElementById('navToggle');
      var siteNav = document.getElementById('siteNav');
      if (navToggle && siteNav) {
        navToggle.addEventListener('click', function() {
          var isOpen = siteNav.classList.toggle('open');
          navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
      }
      
      // Registration disabled button
      var regBtn = document.getElementById('registerDisabledBtn');
      if (regBtn) {
        regBtn.addEventListener('click', function(e) {
          e.preventDefault();
          if (window.showConfirmModal) {
            window.showConfirmModal({
              title: 'Registration Closed',
              message: 'Registration is currently closed. Please contact the school office for assistance.',
              confirmText: 'OK'
            });
          } else {
            alert('Registration is currently closed.');
          }
        });
      }
    });
  </script>
  <script>
    window.gradeLevelLabels = <?= json_encode(grade_level_js_labels()) ?>;
    window.formatGradeLevel = function(level) {
      if (level === null || level === undefined || level === '') return 'N/A';
      const key = String(level);
      return window.gradeLevelLabels[key] ?? ('Grade ' + key);
    };
  </script>

  <!-- JSON-LD Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "Cauayan South Central School",
    "alternateName": "CSCS",
    "url": "<?= base_url() ?>",
    "logo": "<?= asset_url('LPHS2.png') ?>",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Mabini Street, District I",
      "addressLocality": "Cauayan City",
      "addressRegion": "Isabela",
      "addressCountry": "PH"
    },
    "description": "Cauayan South Central School Management System - Modern Education Management Platform",
    "foundingDate": "2024",
    "sameAs": [
      "https://www.facebook.com/cauayan.south.central"
    ]
  }
  </script>

  <!-- Critical layout styles (extracted from inline for maintainability) -->
  <style>
    html {
      scroll-behavior: smooth;
      /* iOS 100vh fix */
      height: -webkit-fill-available;
    }
    body {
      min-height: 100vh;
      min-height: -webkit-fill-available;
    }
    .site-header-inner {
      height: 64px;
    }
    .site-brand {
      margin-right: 2rem;
    }
    .nav-item-dropdown {
      position: relative;
      display: inline-block;
    }
    .dropdown-menu {
      background: rgba(30, 58, 138, 0.95);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      margin-top: 8px;
    }
    .dropdown-item {
      font-size: 0.9rem;
    }
    .site-brand-title-text {
      position: relative;
    }
    .site-brand-title-text::after,
    .site-brand-title-text::before {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: white;
    }
    .site-brand-title-text::before {
      bottom: -2px;
    }
    .footer-divider {
      height: 4px;
      background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
      margin-top: 0;
    }
    .site-footer {
      background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%) !important;
      color: white !important;
      padding: 16px 0 !important;
      box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }
    .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    .footer-grid {
      display: grid;
      grid-template-columns: 1fr 2fr 1fr;
      gap: 30px;
      align-items: center;
    }
    .footer-brand {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .footer-brand img {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .footer-brand h4 {
      margin: 0;
      font-weight: 700;
      color: #fbbf24;
      font-size: 1.1rem;
    }
    .footer-brand p {
      margin: 0;
      font-size: 12px;
      color: white;
    }
    .footer-contact {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      font-size: 13px;
    }
    .footer-contact div {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .footer-contact-icon {
      color: #fbbf24;
    }
    .footer-contact-icon--green {
      color: #10b981;
    }
    .footer-contact a,
    .footer-contact span {
      color: white;
      text-decoration: none;
      opacity: 0.9;
    }
    .footer-right {
      text-align: right;
    }
    .footer-social-row {
      margin-bottom: 15px;
      display: inline-flex;
      align-items: center;
      gap: 12px;
    }
    .footer-facebook {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 50px;
      height: 50px;
      background: #1877f2;
      border-radius: 12px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(24,119,242,0.3);
    }
    .footer-facebook i {
      font-size: 24px;
      color: white;
    }
    .footer-facebook:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(24,119,242,0.5);
    }
    .footer-qr {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 50px;
      height: 50px;
      background: white;
      border-radius: 12px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      overflow: hidden;
      text-decoration: none;
    }
    .footer-qr img {
      width: 42px;
      height: 42px;
      object-fit: contain;
    }
    .footer-qr i {
      font-size: 28px;
      color: #1e3a8a;
    }
    .footer-meta {
      font-size: 11px;
      opacity: 0.8;
      line-height: 1.4;
    }
    .register-disabled-btn {
      opacity: 0.6;
      cursor: not-allowed;
    }
    .nav-user-greeting {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 150px;
      display: inline-block;
      vertical-align: middle;
    }

    @media (max-width: 768px) {
      .site-footer {
        padding: 18px 0 12px !important;
      }
      .footer-container {
        padding: 0 14px;
      }
      .footer-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
        align-items: center;
      }
      .footer-brand {
        flex: 1 1 160px;
        min-width: 120px;
      }
      .footer-contact {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
        flex: 1 1 200px;
        min-width: 140px;
      }
      .footer-contact div {
        flex: 1 1 120px;
        min-width: 120px;
      }
      .footer-right {
        flex: 0 0 auto;
        text-align: right;
        min-width: 80px;
      }
      .footer-facebook {
        width: 36px;
        height: 36px;
      }
      .footer-facebook i {
        font-size: 20px;
      }
      .footer-qr {
        width: 36px;
        height: 36px;
      }
      .footer-qr img {
        width: 30px;
        height: 30px;
      }
      .footer-qr i {
        font-size: 22px;
      }
      .footer-meta {
        font-size: 0.78rem;
      }
      .nav-user-greeting {
        max-width: 100px;
      }
    }
  </style>

</body>
</html>