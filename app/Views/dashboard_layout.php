<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php
    $docTitle = (string) ($title ?? 'Dashboard - CSCS SMS');
    $docParts = explode(' - ', $docTitle, 2);
    $docSub = trim($docParts[1] ?? '');
    if ($docSub === '' || ctype_digit($docSub)) {
      $docSub = 'CSCS SMS';
    }
    $docTitle = $docParts[0] . ' - ' . $docSub;
  ?>
  <title><?= esc($docTitle) ?></title>
  <?= view('partials/site_head_meta', [
    'headMetaTitle' => $docTitle,
    'headMetaDescription' => $headMetaDescription ?? 'Cauayan South Central School — CSCS Tap n Track portal.',
  ]) ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <?php
    $cssV = static function (string $file): string {
      $path = asset_file_path('css/' . $file);

      return $path !== null ? (string) filemtime($path) : '1';
    };
    $jsV = static function (string $file): string {
      $path = asset_file_path('js/' . $file);

      return $path !== null ? (string) filemtime($path) : '1';
    };
  ?>
  <link href="<?= asset_url('css/app.css') ?>?v=<?= $cssV('app.css') ?>" rel="stylesheet" />
  <link href="<?= asset_url('css/dashboard.css') ?>?v=<?= $cssV('dashboard.css') ?>" rel="stylesheet" />
  <link href="<?= asset_url('css/featured-poster.css') ?>?v=<?= $cssV('featured-poster.css') ?>" rel="stylesheet" />
  <link href="<?= asset_url('css/responsive.css') ?>?v=<?= $cssV('responsive.css') ?>" rel="stylesheet" />
  <link href="<?= asset_url('css/admin-table-enhancements.css') ?>" rel="stylesheet" />
  <link href="<?= asset_url('css/modal-system.css') ?>" rel="stylesheet" />
</head>
<body class="dashboard-app">
  <!-- Sidebar -->
  <div class="app-sidebar-wrapper">
    <aside class="app-sidebar">
      <?php
        helper('portal_nav');
        $authUser = null;
        try {
          if (auth()->loggedIn()) {
            $authUser = auth()->user();
          }
        } catch (\Throwable $e) {
          $authUser = null;
        }

        $portalNav = portal_nav_for_user($authUser);
        $role               = $portalNav['role'];
        $portalLabel        = $portalNav['portal_label'];
        $dashboardUrl       = $portalNav['dashboard_url'];
        $accountPanelHref   = $portalNav['account_panel_href'];
        $notificationsHref  = $portalNav['notifications_href'];
        $navSections        = $portalNav['sections'];
      ?>
      <!-- Sidebar Header -->
      <div class="app-sidebar-header">
        <a href="<?= $dashboardUrl ?>" class="d-flex align-items-center text-decoration-none">
          <div class="app-brand-text">
            <div class="app-brand-title">CSCS Tap n Track</div>
            <div class="app-brand-subtitle"><?= esc($portalLabel) ?></div>
          </div>
        </a>
        <button class="app-sidebar-toggle d-lg-none" type="button" aria-label="Toggle sidebar">
          <i class="bi bi-list"></i>
        </button>
      </div>
      
      <!-- User Greeting -->
      <?php if (auth()->loggedIn()): ?>
        <div class="user-greeting">
          <div class="greeting-text">
            <?php 
              $greeting = 'Welcome';
              
              $user = auth()->user();
              $userName = 'User';
              
              if (function_exists('user_is_any_admin') && user_is_any_admin($user)) {
                $userName = $user->inGroup('admin_staff') ? 'Admin staff' : 'Admin';
              } elseif ($user->inGroup('teacher')) {
                // Try to get teacher name from teachers table
                $db = \Config\Database::connect();
                $teacher = $db->table('teachers')
                  ->select('first_name, last_name')
                  ->where('user_id', $user->id)
                  ->get()
                  ->getRow();
                if ($teacher) {
                  $userName = $teacher->first_name . ' ' . $teacher->last_name;
                } else {
                  $userName = 'Teacher';
                }
              } elseif ($user->inGroup('student')) {
                // Try to get student name from students table
                $db = \Config\Database::connect();
                $student = $db->table('students')
                  ->select('first_name, last_name')
                  ->where('user_id', $user->id)
                  ->get()
                  ->getRow();
                if ($student) {
                  $userName = $student->first_name . ' ' . $student->last_name;
                } else {
                  $userName = 'Student';
                }
              }
            ?>
            <div class="greeting-main"><?= $greeting ?>!</div>
            <div class="greeting-name"><?= esc($userName) ?></div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Sidebar Navigation -->
      <nav class="app-sidebar-nav" aria-label="Portal navigation">
        <?php
          $currentPath = rtrim(parse_url(current_url(), PHP_URL_PATH) ?: '', '/');
          $activeItemPath = '';
          $activeMatchLen = -1;

          foreach ($navSections as $sectionScan) {
            foreach (($sectionScan['items'] ?? []) as $itemScan) {
              $scanPath = rtrim(parse_url($itemScan['href'] ?? '', PHP_URL_PATH) ?: '', '/');
              if ($scanPath === '') {
                continue;
              }
              $scanMatches = $currentPath === $scanPath || str_starts_with($currentPath . '/', $scanPath . '/');
              if (! $scanMatches) {
                continue;
              }

              $scanLen = strlen($scanPath);
              if ($scanLen > $activeMatchLen) {
                $activeMatchLen = $scanLen;
                $activeItemPath = $scanPath;
              }
            }
          }

          foreach ($navSections as $section):
        ?>
          <div class="app-sidebar-section-title"><?= esc($section['title']) ?></div>
          <ul class="app-sidebar-menu">
            <?php foreach ($section['items'] as $item):
              $itemPath = rtrim(parse_url($item['href'], PHP_URL_PATH) ?: '', '/');
              $isActive = $itemPath !== '' && $itemPath === $activeItemPath;
            ?>
              <li>
                <a href="<?= $item['href'] ?>" class="app-sidebar-link<?= $isActive ? ' active' : '' ?>" data-label="<?= esc($item['label']) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                  <i class="bi <?= esc($item['icon']) ?>" aria-hidden="true"></i>
                  <span class="app-sidebar-link-text"><?= esc($item['label']) ?></span>
                  <?php if (! empty($item['badge'])): ?>
                    <span class="badge bg-danger app-sidebar-badge ms-auto flex-shrink-0" id="<?= esc($item['badge']) ?>" style="display: none;"></span>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endforeach; ?>

        <div class="app-sidebar-divider"></div>
        <div class="app-sidebar-section-title">Session</div>
        <ul class="app-sidebar-menu app-sidebar-menu-account">
          <li>
            <a href="<?= base_url('logout') ?>" class="app-sidebar-link app-sidebar-link-logout" data-label="Logout">
              <i class="bi bi-box-arrow-left" aria-hidden="true"></i><span class="app-sidebar-link-text">Logout</span>
            </a>
          </li>
        </ul>
      </nav>
    </aside>
  </div>

  <!-- Sidebar Backdrop (Mobile Only) -->
  <div class="app-sidebar-backdrop d-lg-none"></div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Breadcrumb Navigation -->
    <div class="container-fluid px-4">
      <nav aria-label="breadcrumb" class="breadcrumb-nav">
        <ol class="breadcrumb mb-0 py-2">
          <li class="breadcrumb-item"><a href="<?= $dashboardUrl ?? base_url('dashboard') ?>"><i class="bi bi-house me-1"></i>Home</a></li>
          <?php
            $breadcrumbParts = explode(' - ', $pageTitle ?? 'Dashboard', 2);
            $currentPage = trim($breadcrumbParts[0] ?? 'Dashboard');
          ?>
          <li class="breadcrumb-item active" aria-current="page"><?= esc($currentPage) ?></li>
        </ol>
      </nav>
    </div>

    <!-- Top Bar -->
    <div class="top-bar">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <button class="app-sidebar-toggle d-lg-none me-3" type="button" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
          </button>
          <button class="app-desktop-collapse d-none d-lg-inline-flex btn btn-outline-secondary btn-sm me-3" type="button" aria-label="Collapse sidebar">
            <i class="bi bi-layout-sidebar-inset"></i>
          </button>
          <?php
            $pageTitle = (string) ($title ?? 'Dashboard');
            $parts = explode(' - ', $pageTitle, 2);
            $subtitle = trim($parts[1] ?? '');
            if ($subtitle === '' || ctype_digit($subtitle)) {
              $subtitle = 'CSCS SMS';
            }
          ?>
          <div class="top-bar-title-wrap">
            <h1 class="mb-0 top-bar-title">
              <span class="title-main"><?= esc($parts[0]) ?></span><span class="title-sub"> <?= esc($subtitle) ?></span>
            </h1>
          </div>
        </div>
        <div class="top-bar-actions d-flex align-items-center gap-2">
          <a
            href="<?= esc($notificationsHref ?? base_url('notifications')) ?>"
            class="top-bar-icon-btn top-bar-notification-bell"
            aria-label="Notifications"
            title="Notifications"
          >
            <i class="bi bi-bell"></i>
          </a>
          <a
            href="<?= esc($accountPanelHref ?? base_url('/')) ?>"
            class="top-bar-icon-btn top-bar-profile-btn"
            aria-label="Profile"
            title="Profile"
          >
            <i class="bi bi-person-circle"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Page Content -->
    <main class="page-content">
      <div class="dashboard-page-container">
        <?= $this->renderSection('content') ?>
      </div>
    </main>

    <footer class="dashboard-footer" role="contentinfo">
      <div class="dashboard-footer-bar" aria-hidden="true"></div>
      <div class="dashboard-footer-mobile">
        <div class="dashboard-footer-mobile-brand">
          <img src="<?= asset_url('LPHS2.png') ?>" alt="" width="28" height="28" />
          <span>CSCS Tap n Track</span>
        </div>
        <p class="dashboard-footer-mobile-meta">&copy; <?= date('Y') ?> · v2.1.0</p>
      </div>
      <div class="dashboard-footer-inner dashboard-footer-inner--desktop">
        <div class="dashboard-footer-grid">
          <div class="dashboard-footer-brand">
            <img src="<?= asset_url('LPHS2.png') ?>" alt="CSCS" />
            <div>
              <p class="dashboard-footer-brand-title">CSCS Tap n Track</p>
              <p class="dashboard-footer-brand-sub">Modern Education Management</p>
            </div>
          </div>
          <div class="dashboard-footer-contact">
            <a href="https://maps.google.com/?q=WQ8Q%2BJ5V%20Cauayan%20City" target="_blank" rel="noopener">
              <i class="bi bi-geo-alt" aria-hidden="true"></i>
              <span>Mabini St., District I, Cauayan City, Isabela</span>
            </a>
            <span><i class="bi bi-pin-map" aria-hidden="true"></i> WQ8Q+J5V, Cauayan City</span>
            <span><i class="bi bi-clock" aria-hidden="true"></i> 24/7 System Access</span>
          </div>
          <div class="dashboard-footer-aside">
            <a href="https://www.facebook.com/cauayan.south.central" target="_blank" rel="noopener" class="dashboard-footer-social" aria-label="Facebook">
              <i class="bi bi-facebook" aria-hidden="true"></i>
            </a>
            <p class="dashboard-footer-copy">&copy; <?= date('Y') ?> CSCS Tap n Track<br>Version 2.1.0</p>
          </div>
        </div>
      </div>
    </footer>
  </div>

  <?php
  // Full-viewport overlays (modals, etc.) must render here — outside .main-content — so they are
  // not capped by .main-content > * { z-index: 1 } and sit above the fixed sidebar / sticky top bar.
  ?>
  <?= $this->renderSection('portal_overlays') ?>

  <div id="dashboard-modal-portal" aria-hidden="true" role="region" aria-label="Modal dialogs"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.gradeLevelLabels = <?= json_encode(grade_level_js_labels()) ?>;
    window.formatGradeLevel = function(level) {
      if (level === null || level === undefined || level === '') return 'N/A';
      const key = String(level);
      return window.gradeLevelLabels[key] ?? ('Grade ' + key);
    };
  </script>
  <script src="<?= asset_url('js/mobile-tables.js') ?>?v=<?= $jsV('mobile-tables.js') ?>"></script>
  <script src="<?= asset_url('js/admin-table-enhancements.js') ?>?v=<?= $jsV('admin-table-enhancements.js') ?>"></script>
  <script src="<?= asset_url('js/dashboard-modals.js') ?>?v=<?= $jsV('dashboard-modals.js') ?>"></script>
  <script src="<?= asset_url('js/modal-system.js') ?>?v=<?= $jsV('modal-system.js') ?>"></script>
  <script>
    // Load notification counts
    <?php if (auth()->user()): ?>
    function updateBadge(id, count) {
      const badge = document.getElementById(id);
      if (badge) {
        if (count > 0) {
          badge.textContent = count;
          badge.style.display = 'inline-block';
        } else {
          badge.style.display = 'none';
        }
      }
    }

    function loadNotificationCounts() {
      fetch('<?= base_url('api/notification-counts') ?>')
        .then(response => response.json())
        .then(data => {
          if (data.pending_applications !== undefined) updateBadge('pending-applications-count', data.pending_applications);
          if (data.password_resets !== undefined) updateBadge('password-resets-count', data.password_resets);
          if (data.announcements !== undefined) updateBadge('announcements-count', data.announcements);
          if (data.teacher_announcements !== undefined) updateBadge('teacher-announcements-count', data.teacher_announcements);
          if (data.student_announcements !== undefined) updateBadge('student-announcements-count', data.student_announcements);
          if (data.new_grades !== undefined) updateBadge('new-grades-count', data.new_grades);
          if (data.schedule_updates !== undefined) updateBadge('schedule-updates-count', data.schedule_updates);

          // Topbar bell: do not show count badge (avoids "1" appearing next to page title)
          // Sidebar items (Pending Applications, etc.) still show their counts.
        })
        .catch(error => console.error('Error loading notification counts:', error));
    }

    document.addEventListener('DOMContentLoaded', loadNotificationCounts);
    setInterval(loadNotificationCounts, 30000);
    <?php endif; ?>
  </script>
  <script>
    // Sidebar functionality (scoped to dashboard)
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector('.app-sidebar-wrapper');
      const mobileToggleButtons = document.querySelectorAll('.app-sidebar-toggle');
      const desktopCollapseBtn = document.querySelector('.app-desktop-collapse');
      const sidebarBackdrop = document.querySelector('.app-sidebar-backdrop');

      // Mobile sidebar toggle
      mobileToggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          sidebarWrapper.classList.toggle('show');
          sidebarBackdrop.classList.toggle('show');
        });
      });

      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(event) {
        if (window.innerWidth < 992) {
          if (!sidebarWrapper.contains(event.target) && !event.target.closest('.app-sidebar-toggle')) {
            sidebarWrapper.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
          }
        }
      });

      // Handle window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
          sidebarWrapper.classList.remove('show');
          sidebarBackdrop.classList.remove('show');
        }
      });

      // Desktop collapse toggle
      if (desktopCollapseBtn) {
        desktopCollapseBtn.addEventListener('click', function() {
          sidebarWrapper.classList.toggle('collapsed');
        });
      }

      // Set active sidebar link based on current page
      const currentPath = window.location.pathname;
      document.querySelectorAll('.app-sidebar-link').forEach(link => {
        try {
          const linkPath = new URL(link.href, window.location.origin).pathname;
          if (linkPath === currentPath) {
            link.classList.add('active');
          }
        } catch (e) {
          // ignore URL parse errors
        }
      });
    });
  </script>

  <?php if (isset($role) && in_array($role, ['student', 'teacher'], true)): ?>
    <?= view('partials/platform_rating_logout_modal') ?>
  <?php endif; ?>

</body>
</html>


