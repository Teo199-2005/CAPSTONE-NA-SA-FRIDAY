<?php if (ENVIRONMENT !== 'production'): ?>
<style>
/* Floating Demo Accounts Container - Top Right */
.demo-accounts-floater {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 9999;
    max-width: 310px;
    width: auto;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.demo-floater-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
    color: white;
    border: 2px solid rgba(251, 191, 36, 0.5);
    border-radius: 14px;
    padding: 10px 18px;
    font-size: 0.85rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(30, 58, 138, 0.3), 0 0 0 2px rgba(251, 191, 36, 0.1);
    transition: all 0.3s ease;
    margin-left: auto;
    letter-spacing: 0.02em;
    width: 100%;
}

.demo-floater-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(30, 58, 138, 0.45), 0 0 0 2px rgba(251, 191, 36, 0.2);
    border-color: rgba(251, 191, 36, 0.7);
}

.demo-floater-toggle i {
    font-size: 1.1rem;
    color: #fbbf24;
}

.demo-floater-badge {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
    color: #1e3a8a;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}

/* Demo Dropdown Panel */
.demo-floater-panel {
    display: none;
    margin-top: 10px;
    background: rgba(30, 58, 138, 0.97);
    backdrop-filter: blur(25px);
    border: 2px solid rgba(251, 191, 36, 0.5);
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 4px rgba(251, 191, 36, 0.08), 0 0 0 1px rgba(59, 130, 246, 0.2);
    animation: demoSlideIn 0.25s ease-out;
    position: relative;
}

.demo-floater-panel::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #fbbf24, #f59e0b, #ea580c);
    border-radius: 16px 16px 0 0;
    z-index: 2;
}

.demo-floater-panel.open {
    display: block;
}

@keyframes demoSlideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.demo-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(59, 130, 246, 0.15);
}

.demo-panel-title {
    color: #fbbf24;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    letter-spacing: -0.01em;
}

.demo-panel-title i {
    font-size: 1rem;
    color: #fbbf24;
}

.demo-panel-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    transition: all 0.2s;
    font-size: 0.9rem;
    line-height: 1;
}

.demo-panel-close:hover {
    color: white;
    background: rgba(59, 130, 246, 0.2);
}

.demo-role-group {
    margin-bottom: 16px;
}

.demo-role-group:last-child {
    margin-bottom: 0;
}

.demo-role-label {
    color: rgba(251, 191, 36, 0.7);
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
    display: block;
}

.demo-account-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 9px 12px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 10px;
    color: white;
    text-align: left;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 5px;
    text-decoration: none;
}

.demo-account-btn:last-child {
    margin-bottom: 0;
}

.demo-account-btn:hover {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(251, 191, 36, 0.4);
    transform: translateX(3px);
}

.demo-account-btn:active {
    transform: translateX(1px);
}

.demo-btn-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.demo-btn-icon.admin {
    background: linear-gradient(135deg, #f59e0b, #ea580c);
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.demo-btn-icon.teacher {
    background: linear-gradient(135deg, #3b82f6, #1e40af);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.demo-btn-icon.student {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.demo-btn-info {
    flex: 1;
    min-width: 0;
}

.demo-btn-name {
    font-size: 0.78rem;
    font-weight: 600;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: white;
}

.demo-btn-detail {
    font-size: 0.62rem;
    color: rgba(255, 255, 255, 0.5);
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 1px;
}

.demo-btn-arrow {
    color: rgba(251, 191, 36, 0.4);
    font-size: 0.7rem;
    transition: all 0.2s;
    flex-shrink: 0;
}

.demo-account-btn:hover .demo-btn-arrow {
    color: #fbbf24;
    transform: translateX(3px);
}

.demo-panel-footer {
    margin-top: 14px;
    padding-top: 10px;
    border-top: 1px solid rgba(59, 130, 246, 0.15);
}

.demo-disclaimer {
    color: rgba(255, 255, 255, 0.35);
    font-size: 0.6rem;
    text-align: center;
    margin: 0;
    line-height: 1.4;
}

.demo-disclaimer i {
    color: #fbbf24;
}

/* Responsive */
@media (max-width: 767.98px) {
    .demo-accounts-floater {
        top: auto;
        bottom: 20px;
        right: 12px;
        left: 12px;
        max-width: 100%;
    }

    .demo-floater-toggle {
        padding: 8px 14px;
        font-size: 0.78rem;
        border-radius: 12px;
    }

    .demo-floater-panel {
        padding: 14px;
        border-radius: 14px;
        margin-bottom: 6px;
    }

    .demo-account-btn {
        padding: 8px 12px;
    }

    .demo-btn-icon {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }

    .demo-btn-name {
        font-size: 0.72rem;
    }

    .demo-btn-detail {
        font-size: 0.6rem;
    }
}

@media (max-width: 480px) {
    .demo-accounts-floater {
        right: 8px;
        left: 8px;
        bottom: 12px;
    }

    .demo-floater-toggle {
        padding: 6px 10px;
        font-size: 0.72rem;
    }

    .demo-floater-panel {
        padding: 12px;
        border-radius: 12px;
    }
}
</style>

<div class="demo-accounts-floater" id="demoAccountsFloater" aria-label="Demo Accounts">
    <button class="demo-floater-toggle" id="demoFloaterToggle" aria-expanded="false" aria-controls="demoFloaterPanel">
        <i class="bi bi-incognito" aria-hidden="true"></i>
        <span>Demo Accounts</span>
        <span class="demo-floater-badge">Quick Access</span>
    </button>

    <div class="demo-floater-panel" id="demoFloaterPanel">
        <div class="demo-panel-header">
            <h4 class="demo-panel-title">
                <i class="bi bi-people-fill" aria-hidden="true"></i>
                Try Demo Accounts
            </h4>
            <button class="demo-panel-close" id="demoPanelClose" aria-label="Close demo accounts panel">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Admin -->
        <div class="demo-role-group">
            <span class="demo-role-label">Administrator</span>
            <a href="<?= base_url('login/demo/admin') ?>" class="demo-account-btn" title="Login as Admin">
                <span class="demo-btn-icon admin">
                    <i class="bi bi-shield-lock-fill" aria-hidden="true"></i>
                </span>
                <span class="demo-btn-info">
                    <span class="demo-btn-name">Admin Access</span>
                    <span class="demo-btn-detail">demo.admin@lphs.edu / DemoPass123!</span>
                </span>
                <span class="demo-btn-arrow">
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </span>
            </a>
        </div>

        <!-- Teachers -->
        <div class="demo-role-group">
            <span class="demo-role-label">Teachers</span>
            <a href="<?= base_url('login/demo/teacher/nonnumeric') ?>" class="demo-account-btn" title="Login as Teacher - Maria Santos (Adviser of 1-A)">
                <span class="demo-btn-icon teacher">
                    <i class="bi bi-person-badge-fill" aria-hidden="true"></i>
                </span>
                <span class="demo-btn-info">
                    <span class="demo-btn-name">Maria Santos (Adviser - 1-A)</span>
                    <span class="demo-btn-detail">teacher.santos@lphs.edu / Teacher123!</span>
                </span>
                <span class="demo-btn-arrow">
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </span>
            </a>
            <a href="<?= base_url('login/demo/teacher/numerical') ?>" class="demo-account-btn" title="Login as Teacher - Juan Reyes (Adviser of Baybayin)">
                <span class="demo-btn-icon teacher">
                    <i class="bi bi-person-badge-fill" aria-hidden="true"></i>
                </span>
                <span class="demo-btn-info">
                    <span class="demo-btn-name">Juan Reyes (Adviser - Baybayin)</span>
                    <span class="demo-btn-detail">teacher.reyes@lphs.edu / Teacher123!</span>
                </span>
                <span class="demo-btn-arrow">
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </span>
            </a>
        </div>

        <!-- Students -->
        <div class="demo-role-group">
            <span class="demo-role-label">Students</span>
            <a href="<?= base_url('login/demo/student/numerical') ?>" class="demo-account-btn" title="Login as Student - Demo Student1 (Section 1-A)">
                <span class="demo-btn-icon student">
                    <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                </span>
                <span class="demo-btn-info">
                    <span class="demo-btn-name">Demo Student1 (Section 1-A)</span>
                    <span class="demo-btn-detail">demo.student1@lphs.edu / DemoPass123!</span>
                </span>
                <span class="demo-btn-arrow">
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </span>
            </a>
            <a href="<?= base_url('login/demo/student/nonnumeric') ?>" class="demo-account-btn" title="Login as Student - Demo Student2 (Section 1-B)">
                <span class="demo-btn-icon student">
                    <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                </span>
                <span class="demo-btn-info">
                    <span class="demo-btn-name">Demo Student2 (Section 1-B)</span>
                    <span class="demo-btn-detail">demo.student2@lphs.edu / DemoPass123!</span>
                </span>
                <span class="demo-btn-arrow">
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </span>
            </a>
        </div>

        <div class="demo-panel-footer">
            <p class="demo-disclaimer">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
                Demo accounts for testing only. Data resets periodically.
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('demoFloaterToggle');
    var panel = document.getElementById('demoFloaterPanel');
    var close = document.getElementById('demoPanelClose');

    if (toggle && panel) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var isOpen = panel.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    if (close && panel) {
        close.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            panel.classList.remove('open');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        });
    }

    // Close panel when clicking outside
    document.addEventListener('click', function(e) {
        var floater = document.getElementById('demoAccountsFloater');
        if (floater && !floater.contains(e.target)) {
            if (panel) panel.classList.remove('open');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>
<?php endif; ?>