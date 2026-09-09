<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
.forgot-container {
  height: calc(100vh - 80px);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  position: relative;
  margin: -2rem -15px 0 -15px;
}

.forgot-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%233b82f6" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 1;
}

.forgot-card {
  background: rgba(30, 64, 175, 0.95);
  backdrop-filter: blur(25px);
  border: 1px solid rgba(59, 130, 246, 0.3);
  border-radius: 20px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.2);
  width: 100%;
  max-width: 440px;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.forgot-header {
  text-align: center;
  padding: 3rem 2.5rem 2rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
  border-bottom: 1px solid rgba(59, 130, 246, 0.1);
}

.forgot-title {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.025em;
}

.forgot-subtitle {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0;
  line-height: 1.5;
}

.forgot-body {
  padding: 2.5rem;
}

.form-control {
  width: 100%;
  padding: 1rem 1.25rem;
  border: 2px solid rgba(255, 255, 255, 0.15);
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  font-size: 1rem;
  font-weight: 500;
  transition: all 0.3s ease;
  margin-bottom: 1.5rem;
}

.form-control::placeholder {
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
}

.form-control:focus {
  outline: none;
  border-color: #fbbf24;
  background: rgba(255, 255, 255, 0.15);
  box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1);
  transform: translateY(-2px);
}

.password-field {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  font-size: 1.2rem;
  padding: 0.5rem;
  min-width: 40px;
  min-height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: all 0.2s ease;
  -webkit-tap-highlight-color: transparent;
  z-index: 5;
}

.password-toggle:hover {
  color: #fbbf24;
  background: rgba(255, 255, 255, 0.1);
}

/* Ensure password input has space for toggle */
.password-field .form-control {
  padding-right: 3.5rem;
}

.forgot-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
  border: none;
  border-radius: 14px;
  color: white;
  font-weight: 700;
  font-size: 1.05rem;
  letter-spacing: 0.025em;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin-bottom: 2rem;
  position: relative;
  overflow: hidden;
  min-height: 52px;
  -webkit-tap-highlight-color: transparent;
}

.forgot-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 35px rgba(251, 191, 36, 0.4);
  background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%);
}

.forgot-btn:active {
  transform: translateY(0);
  box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.back-section {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid rgba(59, 130, 246, 0.15);
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, rgba(147, 197, 253, 0.02) 100%);
  margin: 0 -2.5rem -2.5rem;
  padding-left: 2.5rem;
  padding-right: 2.5rem;
  padding-bottom: 2.5rem;
}

.back-text {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0;
}

.back-link {
  color: #fbbf24;
  text-decoration: none;
  font-weight: 700;
  transition: all 0.2s ease;
}

.back-link:hover {
  color: #f59e0b;
  text-decoration: underline;
}

.alert {
  padding: 1rem 1.25rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid;
  font-weight: 500;
}

.alert-success {
  background: rgba(34, 197, 94, 0.1);
  border-color: rgba(34, 197, 94, 0.3);
  color: #22c55e;
}

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  border-color: rgba(239, 68, 68, 0.3);
  color: #ef4444;
}

/* ===== MOBILE RESPONSIVE STYLES ===== */

@media (max-width: 767.98px) {
  .forgot-container {
    padding: 2rem 0.75rem;
    margin: -2rem -15px 0 -15px;
  }
  
  .forgot-card {
    max-width: 400px;
    border-radius: 16px;
  }
  
  .forgot-header {
    padding: 2rem 1.5rem 1.25rem;
  }
  
  .forgot-title {
    font-size: 1.5rem;
  }
  
  .forgot-subtitle {
    font-size: 0.85rem;
  }
  
  .forgot-body {
    padding: 1.5rem;
  }
  
  .form-control {
    padding: 0.875rem 1rem;
    font-size: 16px;
    border-radius: 12px;
    margin-bottom: 1rem;
  }
  
  .password-field .form-control {
    padding-right: 3.25rem;
  }
  
  .password-toggle {
    right: 0.75rem;
    padding: 0.375rem;
    min-width: 36px;
    min-height: 36px;
    font-size: 1.1rem;
  }
  
  .forgot-btn {
    padding: 0.875rem;
    font-size: 1rem;
    min-height: 48px;
    border-radius: 12px;
    margin-bottom: 1.5rem;
  }
  
  .back-section {
    padding-top: 1.25rem;
    margin: 0 -1.5rem -1.5rem;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
    padding-bottom: 1.5rem;
  }
  
  .back-text {
    font-size: 0.9rem;
  }
  
  .alert {
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    border-radius: 10px;
  }
}

@media (max-width: 480px) {
  .forgot-container {
    padding: 1.5rem 0.5rem;
  }
  
  .forgot-card {
    border-radius: 14px;
    max-width: 100%;
  }
  
  .forgot-header {
    padding: 1.5rem 1rem 1rem;
  }
  
  .forgot-title {
    font-size: 1.25rem;
  }
  
  .forgot-subtitle {
    font-size: 0.8rem;
  }
  
  .forgot-body {
    padding: 1.25rem;
  }
  
  .form-control {
    padding: 0.75rem 0.875rem;
    font-size: 16px;
    border-radius: 10px;
  }
  
  .password-field .form-control {
    padding-right: 3rem;
  }
  
  .password-toggle {
    right: 0.5rem;
    padding: 0.25rem;
    min-width: 32px;
    min-height: 32px;
    font-size: 1rem;
  }
  
  .forgot-btn {
    padding: 0.75rem;
    font-size: 0.9rem;
    min-height: 44px;
    border-radius: 10px;
    margin-bottom: 1rem;
  }
  
  .back-section {
    padding-top: 1rem;
    margin: 0 -1.25rem -1.25rem;
    padding-left: 1.25rem;
    padding-right: 1.25rem;
    padding-bottom: 1.25rem;
  }
  
  .back-text {
    font-size: 0.85rem;
  }
  
  .alert {
    padding: 0.625rem 0.875rem;
    font-size: 0.8rem;
    border-radius: 8px;
  }
}

@media (max-width: 359px) {
  .forgot-header {
    padding: 1rem 0.75rem 0.75rem;
  }
  
  .forgot-title {
    font-size: 1.1rem;
  }
  
  .forgot-body {
    padding: 1rem;
  }
  
  .form-control {
    padding: 0.625rem 0.75rem;
    font-size: 16px;
  }
  
  .forgot-btn {
    padding: 0.625rem;
    font-size: 0.85rem;
    min-height: 40px;
  }
  
  .back-section {
    margin: 0 -1rem -1rem;
    padding-left: 1rem;
    padding-right: 1rem;
    padding-bottom: 1rem;
  }
}

@media (max-height: 500px) and (orientation: landscape) {
  .forgot-container {
    height: auto;
    min-height: auto;
    padding: 1rem 0.5rem;
  }
  
  .forgot-header {
    padding: 1rem 1rem 0.75rem;
  }
  
  .forgot-title {
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
  }
  
  .forgot-subtitle {
    font-size: 0.75rem;
  }
  
  .forgot-body {
    padding: 0.75rem 1rem;
  }
  
  .form-control {
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.75rem;
  }
  
  .forgot-btn {
    padding: 0.5rem;
    min-height: 38px;
    font-size: 0.85rem;
    margin-bottom: 0.75rem;
  }
  
  .back-section {
    padding-top: 0.75rem;
    margin: 0 -1rem -1rem;
    padding-left: 1rem;
    padding-right: 1rem;
    padding-bottom: 1rem;
  }
}
</style>

<div class="forgot-container">
  <div class="forgot-card">
    <div class="forgot-header">
      <h1 class="forgot-title">Change Password</h1>
      <p class="forgot-subtitle">Enter your current password and choose a new password.</p>
    </div>
    
    <div class="forgot-body">
      <?php if (isset($error)): ?>
        <div class="alert alert-danger">
          <?= $error ?>
        </div>
      <?php endif; ?>

      <?php if (isset($validation) && $validation->getErrors()): ?>
        <div class="alert alert-danger">
          <ul style="margin: 0; padding-left: 1.5rem;">
            <?php foreach ($validation->getErrors() as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('change-password') ?>">
        <?= csrf_field() ?>
        
        <div class="password-field">
          <input type="password" class="form-control" id="current_password" name="current_password"
                 placeholder="Current Password" required>
          <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
            <i class="bi bi-eye" id="current_password_icon"></i>
          </button>
        </div>

        <div class="password-field">
          <input type="password" class="form-control" id="new_password" name="new_password"
                 placeholder="New Password (min. 8 characters)" required>
          <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
            <i class="bi bi-eye" id="new_password_icon"></i>
          </button>
        </div>

        <div class="password-field">
          <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                 placeholder="Confirm New Password" required>
          <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
            <i class="bi bi-eye" id="confirm_password_icon"></i>
          </button>
        </div>

        <button type="submit" class="forgot-btn">UPDATE PASSWORD</button>
      </form>

      <div class="back-section">
        <p class="back-text">
          <a href="<?= base_url('forgot-password') ?>" class="back-link">← Start Over</a>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
function togglePassword(fieldId) {
  const field = document.getElementById(fieldId);
  const icon = document.getElementById(fieldId + '_icon');
  
  if (field.type === 'password') {
    field.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    field.type = 'password';
    icon.className = 'bi bi-eye';
  }
}
</script>

<?= $this->endSection() ?>