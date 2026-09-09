<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
.login-container {
  min-height: calc(100vh - 80px);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem 4rem;
  position: relative;
  width: 100%;
  box-sizing: border-box;
}

.login-container::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%233b82f6" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 0.9;
}

.login-container::after {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background-image: url('<?= asset_url('LPHS2.png') ?>');
  background-repeat: repeat;
  background-size: 110px 110px;
  background-position: center center;
  opacity: 0.095;
  filter: grayscale(0.7) saturate(0.8);
  pointer-events: none;
}

.login-card {
  background: rgba(30, 64, 175, 0.95);
  backdrop-filter: blur(25px);
  border: 2px solid rgba(251, 191, 36, 0.6);
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15), 0 0 0 4px rgba(251, 191, 36, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.3);
  width: 100%;
  max-width: 440px;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.login-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  background: linear-gradient(90deg, #fbbf24, #f59e0b, #ea580c);
  z-index: 2;
}

.login-header {
  text-align: center;
  padding: 2.5rem 2.5rem 1.75rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
  border-bottom: 1px solid rgba(59, 130, 246, 0.15);
}

.login-logo {
  width: 64px; height: 64px; object-fit: contain; border-radius: 50%;
  margin: 0 auto 0.875rem; display: block;
  background: rgba(255, 255, 255, 0.95);
  border: 2px solid rgba(255, 255, 255, 0.7);
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.3);
}

.login-title {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-size: 1.85rem; font-weight: 800; margin-bottom: 0.5rem;
  letter-spacing: -0.02em; line-height: 1.2;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ffffff 100%);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  background-clip: text; color: transparent;
}

.login-subtitle {
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.9rem; font-weight: 500; margin: 0;
}

.login-form { padding: 1.75rem 2.25rem 2.25rem; }

.form-control, .custom-field, input[type="text"], input[type="password"], input[type="email"],
input[type="tel"], input[type="date"], input[type="number"], select.form-select, select {
  width: 100%;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  padding: 0.5rem 0.875rem;
  font-size: 0.92rem;
  background: #ffffff;
  color: #1e293b;
  font-weight: 400;
  height: 42px;
  line-height: 1.4;
  transition: all 0.2s ease;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
  box-sizing: border-box;
}

.form-control:focus, .custom-field:focus, input[type="text"]:focus, input[type="password"]:focus,
input[type="email"]:focus, input[type="tel"]:focus, input[type="date"]:focus,
input[type="number"]:focus, select.form-select:focus, select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
  background: white;
}

.form-control::placeholder, .custom-field::placeholder {
  color: #94a3b8; font-weight: 400;
}

.custom-input-group { position: relative; margin-bottom: 0.875rem; }
.custom-input-group .input-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  color: #94a3b8; font-size: 0.95rem; z-index: 2; pointer-events: none;
}
.custom-input-group .custom-field { padding-left: 2.4rem !important; height: 42px; }
.custom-input-group .custom-field:focus ~ .input-icon { color: #3b82f6; }

.password-toggle-btn {
  position: absolute !important; right: 4px !important; top: 50% !important;
  transform: translateY(-50%) !important; border: none !important;
  background: none !important; color: #6b7280 !important; z-index: 5 !important;
  padding: 0 !important; width: 36px !important; height: 36px !important;
  display: flex !important; align-items: center !important;
  justify-content: center !important; cursor: pointer !important;
  -webkit-tap-highlight-color: transparent !important; border-radius: 6px !important;
}
.password-toggle-btn:hover, .password-toggle-btn:active {
  background: rgba(107, 114, 128, 0.1) !important; color: #3b82f6 !important;
}
.password-input-wrapper .custom-field { padding-right: 44px !important; }

.remember-section {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.25rem; margin-top: 0.25rem;
}
.form-check { 
  display: flex; 
  align-items: center; 
  gap: 0.5rem; 
  margin: 0;
  padding: 0;
}
.form-check-input {
  width: 1rem; height: 1rem; border-radius: 4px; border: 1.5px solid #cbd5e1;
  transition: all 0.2s ease; flex-shrink: 0; cursor: pointer;
  margin: 0;
}
.form-check-input:checked { background-color: #3b82f6; border-color: #3b82f6; }
.form-check-label {
  color: rgba(255, 255, 255, 0.95); font-size: 0.85rem; font-weight: 500;
  margin: 0; cursor: pointer; user-select: none;
}
.forgot-link {
  color: #fbbf24; text-decoration: none; font-size: 0.85rem; font-weight: 500;
  transition: color 0.2s ease;
}
.forgot-link:hover { color: #f59e0b; text-decoration: underline; }

.login-btn {
  width: 100%; padding: 0.625rem 1rem;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
  border: none; border-radius: 8px; color: white; font-weight: 700;
  font-size: 0.9rem; letter-spacing: 0.025em;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin-bottom: 1rem; position: relative; overflow: hidden; height: 42px;
  -webkit-tap-highlight-color: transparent;
  box-shadow: 0 4px 14px rgba(251, 191, 36, 0.35);
}
.login-btn:hover {
  transform: translateY(-1px); box-shadow: 0 6px 20px rgba(251, 191, 36, 0.5);
  background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%);
}
.login-btn:active { transform: translateY(0); }

.register-section {
  text-align: center; padding-top: 1rem;
  border-top: 1px solid rgba(59, 130, 246, 0.15);
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, rgba(147, 197, 253, 0.02) 100%);
  margin: 0 -2.25rem -2.25rem;
  padding-left: 2.25rem; padding-right: 2.25rem; padding-bottom: 1.5rem;
}
.register-text { color: rgba(255, 255, 255, 0.85); font-size: 0.85rem; font-weight: 500; margin: 0; }
.register-link {
  color: #fbbf24; text-decoration: none; font-weight: 700;
  transition: all 0.2s ease; position: relative;
}
.register-link::after {
  content: ''; position: absolute; bottom: -2px; left: 0;
  width: 0; height: 1.5px;
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  transition: width 0.3s ease;
}
.register-link:hover { color: #f59e0b; transform: translateY(-1px); }
.register-link:hover::after { width: 100%; }

.alert {
  border: 1px solid; border-radius: 8px;
  padding: 0.5rem 0.875rem; margin-bottom: 0.875rem; font-size: 0.82rem;
}
.alert-danger { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
.alert-success { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }

@media (max-width: 991.98px) {
  .login-header { padding: 2.25rem 2rem 1.5rem; }
  .login-title { font-size: 1.6rem; }
  .login-logo { width: 60px; height: 60px; }
  .login-form { padding: 1.5rem 2rem 1.75rem; }
  .register-section { margin: 0 -2rem -1.75rem; padding-left: 2rem; padding-right: 2rem; padding-bottom: 1.5rem; }
}

@media (max-width: 767.98px) {
  .login-container { padding: 1.5rem 0.75rem 3rem; }
  .login-card { max-width: 400px; border-radius: 14px; }
  .login-header { padding: 2rem 1.5rem 1.25rem; }
  .login-logo { width: 56px; height: 56px; margin-bottom: 0.75rem; }
  .login-title { font-size: 1.4rem; }
  .login-subtitle { font-size: 0.8rem; }
  .login-form { padding: 1.25rem 1.5rem 1.5rem; }
  .form-control, .custom-field,
  input[type="text"], input[type="password"], input[type="email"],
  input[type="tel"], input[type="date"], input[type="number"],
  select.form-select { font-size: 16px; height: 44px; padding: 0.5rem 0.75rem; }
  .remember-section { flex-direction: row; gap: 0.9rem; align-items: center; justify-content: flex-end; margin-bottom: 1rem; }
  .login-btn { padding: 0.55rem 1rem; font-size: 0.85rem; min-height: 44px; margin-bottom: 0.875rem; }
  .forgot-link, .register-link {
    padding: 6px 4px;
    display: inline-block;
    min-height: 44px;
    line-height: 1.2;
    font-size: 0.8rem;
  }
  .forgot-link {
    margin: 0;
  }
  .form-check {
    padding: 0;
    margin: 0;
  }
  .form-check-input {
    width: 24px;
    height: 24px;
    margin: 0;
  }
  .register-section { padding-top: 0.875rem; margin: 0 -1.5rem -1.5rem; padding-left: 1.5rem; padding-right: 1.5rem; padding-bottom: 1.25rem; }
  .register-text { font-size: 0.78rem; }
  .alert { padding: 0.45rem 0.7rem; font-size: 0.78rem; }
}

@media (max-width: 480px) {
  .login-container { padding: 1rem 0.5rem 2rem; }
  .login-card { border-radius: 12px; max-width: 100%; }
  .login-header { padding: 1.5rem 1rem 1rem; }
  .login-logo { width: 48px; height: 48px; margin-bottom: 0.5rem; }
  .login-title { font-size: 1.15rem; }
  .login-subtitle { font-size: 0.72rem; }
  .login-form { padding: 1rem 1rem 1.25rem; }
  .form-control, .custom-field,
  input[type="text"], input[type="password"], input[type="email"],
  select.form-select { height: 44px; padding: 0.5rem 0.75rem; }
  .custom-input-group .custom-field { padding-left: 2rem !important; }
  .custom-input-group .input-icon { left: 10px; font-size: 0.85rem; }
  .password-input-wrapper .custom-field { padding-right: 56px !important; }
  .password-toggle-btn { width: 44px !important; height: 44px !important; right: 4px !important; }
  .login-btn { height: 44px; font-size: 0.85rem; padding: 0.55rem 0.85rem; margin-bottom: 0.875rem; }
  .forgot-link, .register-link {
    padding: 10px 6px;
    display: inline-block;
    min-height: 44px;
    line-height: 1.4;
  }
  .forgot-link {
    margin-top: -10px;
    margin-bottom: -10px;
  }
  .form-check {
    padding: 5px 0;
    margin: 0;
    align-items: center;
  }
  .form-check-input {
    width: 20px;
    height: 20px;
    margin: 0;
    vertical-align: middle;
  }
  .remember-section { margin-bottom: 0.875rem; }
  .register-section { margin: 0 -1rem -1.25rem; padding-left: 1rem; padding-right: 1rem; padding-bottom: 1.25rem; }
}

@media (max-width: 359px) {
  .login-header { padding: 1rem 0.75rem 0.75rem; }
  .login-logo { width: 42px; height: 42px; }
  .login-title { font-size: 1.05rem; }
  .login-form { padding: 0.75rem 0.75rem 1rem; }
  .form-control, .custom-field,
  input[type="text"], input[type="password"], input[type="email"],
  select.form-select { height: 44px; padding: 0.4rem 0.6rem; }
  .custom-input-group .custom-field { padding-left: 1.75rem !important; padding-right: 56px !important; }
  .custom-input-group .input-icon { left: 8px; font-size: 0.75rem; }
  .password-input-wrapper .custom-field { padding-right: 56px !important; }
  .password-toggle-btn { width: 44px !important; height: 44px !important; right: 2px !important; }
  .login-btn { height: 44px; font-size: 0.8rem; padding: 0.45rem 0.75rem; margin-bottom: 0.5rem; }
}

@media (max-height: 500px) and (orientation: landscape) {
  .login-container { min-height: auto; padding: 1rem 0.5rem; }
  .login-header { padding: 1rem 1rem 0.75rem; }
  .login-logo { width: 40px; height: 40px; margin-bottom: 0.25rem; }
  .login-title { font-size: 1.05rem; margin-bottom: 0.25rem; }
  .login-subtitle { font-size: 0.72rem; }
  .login-form { padding: 0.75rem 1rem 1rem; }
  .form-control, .custom-field { height: 44px; }
  .remember-section { margin-bottom: 0.5rem; }
  .login-btn { height: 44px; font-size: 0.8rem; margin-bottom: 0.5rem; }
  .password-toggle-btn { width: 44px !important; height: 44px !important; }
  .password-input-wrapper .custom-field { padding-right: 56px !important; }
}

</style>

<div class="login-container">
  <div class="login-card">
    <div class="login-header">
      <img src="<?= asset_url('LPHS2.png') ?>" alt="Cauayan South Central School Logo" class="login-logo">
      <h1 class="login-title">Cauayan South Central School</h1>
      <p class="login-subtitle">School Management System</p>
      <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.78rem; margin: 0.4rem 0 0 0;">Version 2.1.0 | Secure Portal</p>
    </div>

    <div class="login-form">
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" id="errorAlert" role="alert" aria-live="assertive">
          <?= session()->getFlashdata('error') ?>
          <?php if (session()->getFlashdata('locked_until')): ?>
            <div id="countdown" style="font-weight: bold; margin-top: 0.5rem;"></div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" role="alert">
          <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger" role="alert" aria-live="assertive">
          <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('login') ?>">
        <?= csrf_field() ?>
        <div class="custom-input-group">
          <i class="bi bi-person input-icon"></i>
          <input type="text" class="custom-field" id="identifier" name="identifier"
                 placeholder="Email, LRN, or PRC License" value="<?= old('identifier') ?: ($_COOKIE['remembered_identifier'] ?? '') ?>"
                 autocomplete="username" inputmode="text" required>
          <label for="identifier" class="visually-hidden">Email, LRN, or PRC License</label>
        </div>
        <div class="custom-input-group password-input-wrapper">
          <i class="bi bi-lock input-icon"></i>
          <input type="password" class="custom-field" id="password" name="password"
                 placeholder="Password" autocomplete="current-password" required>
          <label for="password" class="visually-hidden">Password</label>
          <button type="button" class="btn position-absolute password-toggle-btn" id="togglePassword">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
        <div class="remember-section">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1" <?= isset($_COOKIE['remembered_identifier']) && $_COOKIE['remembered_identifier'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          <div class="forgot-password-section">
            <a href="<?= base_url('forgot-password') ?>" class="forgot-link">Forgot Password?</a>
          </div>
        </div>
        <button type="submit" class="login-btn" id="loginSubmitBtn">ACCESS SYSTEM</button>
      </form>

      <?php if ($registrationEnabled ?? true): ?>
      <div class="register-section">
        <p class="register-text">New student? <a href="<?= base_url('register') ?>" class="register-link">Create Account</a></p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= view('partials/demo_accounts_floater') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tp = document.getElementById('togglePassword');
    var pi = document.getElementById('password');
    var ti = document.getElementById('toggleIcon');
    if (tp) tp.addEventListener('click', function() {
        var t = pi.getAttribute('type') === 'password' ? 'text' : 'password';
        pi.setAttribute('type', t);
        if (t === 'text') { ti.classList.remove('bi-eye'); ti.classList.add('bi-eye-slash'); }
        else { ti.classList.remove('bi-eye-slash'); ti.classList.add('bi-eye'); }
    });
      var lockEl = document.getElementById('countdown');
    if (lockEl) {
        var lockUntil = new Date('<?= session()->getFlashdata('locked_until') ?? '0' ?>').getTime();
        var ea = document.getElementById('errorAlert');
        setInterval(function() {
            var d = lockUntil - new Date().getTime();
            if (d < 0) { lockEl.innerHTML = ''; if (ea) ea.style.display = 'none'; return; }
            var m = Math.floor(d / 60000), s = Math.floor((d % 60000) / 1000);
            lockEl.innerHTML = '<i class="bi bi-clock"></i> Time remaining: ' + m + 'm ' + s + 's';
        }, 1000);
    }

    // Login button loading state - prevent double submission
    var loginForm = document.querySelector('.login-form form');
    var loginBtn = document.getElementById('loginSubmitBtn');
    if (loginForm && loginBtn) {
      loginForm.addEventListener('submit', function() {
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Signing in...';
        loginBtn.style.opacity = '0.85';
      });
    }
  });
</script>

<?= $this->endSection() ?>