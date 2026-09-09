<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
.reset-container {
  height: calc(100vh - 80px);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  position: relative;
  margin: -2rem -15px 0 -15px;
}

.reset-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%233b82f6" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 1;
}

.reset-card {
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

.reset-header {
  text-align: center;
  padding: 3rem 2.5rem 2rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
  border-bottom: 1px solid rgba(59, 130, 246, 0.1);
}

.reset-title {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  margin: 0 0 0.5rem 0;
  letter-spacing: -0.025em;
}

.reset-subtitle {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0;
  line-height: 1.5;
}

.reset-body {
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

.reset-btn {
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

.reset-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 35px rgba(251, 191, 36, 0.4);
  background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%);
}

.reset-btn:active {
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

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  border-color: rgba(239, 68, 68, 0.3);
  color: #ef4444;
}

.email-display {
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid rgba(255, 255, 255, 0.15);
  border-radius: 14px;
  padding: 1rem 1.25rem;
  color: rgba(255, 255, 255, 0.9);
  font-weight: 500;
  margin-bottom: 1.5rem;
  text-align: center;
}

/* ===== MOBILE RESPONSIVE STYLES ===== */

@media (max-width: 767.98px) {
  .reset-container {
    padding: 2rem 0.75rem;
    margin: -2rem -15px 0 -15px;
  }
  
  .reset-card {
    max-width: 400px;
    border-radius: 16px;
  }
  
  .reset-header {
    padding: 2rem 1.5rem 1.25rem;
  }
  
  .reset-title {
    font-size: 1.5rem;
  }
  
  .reset-subtitle {
    font-size: 0.85rem;
  }
  
  .reset-body {
    padding: 1.5rem;
  }
  
  .form-control {
    padding: 0.875rem 1rem;
    font-size: 16px;
    border-radius: 12px;
    margin-bottom: 1rem;
  }
  
  .reset-btn {
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
  
  .email-display {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    border-radius: 12px;
    margin-bottom: 1rem;
  }
}

@media (max-width: 480px) {
  .reset-container {
    padding: 1.5rem 0.5rem;
  }
  
  .reset-card {
    border-radius: 14px;
    max-width: 100%;
  }
  
  .reset-header {
    padding: 1.5rem 1rem 1rem;
  }
  
  .reset-title {
    font-size: 1.25rem;
  }
  
  .reset-subtitle {
    font-size: 0.8rem;
  }
  
  .reset-body {
    padding: 1.25rem;
  }
  
  .form-control {
    padding: 0.75rem 0.875rem;
    font-size: 16px;
    border-radius: 10px;
  }
  
  .reset-btn {
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
  
  .email-display {
    padding: 0.625rem 0.875rem;
    font-size: 0.85rem;
  }
}

@media (max-width: 359px) {
  .reset-header {
    padding: 1rem 0.75rem 0.75rem;
  }
  
  .reset-title {
    font-size: 1.1rem;
  }
  
  .reset-body {
    padding: 1rem;
  }
  
  .form-control {
    padding: 0.625rem 0.75rem;
    font-size: 16px;
  }
  
  .reset-btn {
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
  .reset-container {
    height: auto;
    min-height: auto;
    padding: 1rem 0.5rem;
  }
  
  .reset-header {
    padding: 1rem 1rem 0.75rem;
  }
  
  .reset-title {
    font-size: 1.1rem;
    margin-bottom: 0.25rem;
  }
  
  .reset-subtitle {
    font-size: 0.75rem;
  }
  
  .reset-body {
    padding: 0.75rem 1rem;
  }
  
  .form-control {
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.75rem;
  }
  
  .reset-btn {
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

<div class="reset-container">
  <div class="reset-card">
    <div class="reset-header">
      <h1 class="reset-title">Set New Password</h1>
      <p class="reset-subtitle">Enter your new password below. Make sure it's strong and secure.</p>
    </div>
    
    <div class="reset-body">
      <?php if (isset($validation) && $validation->getErrors()): ?>
        <div class="alert alert-danger">
          <ul style="margin: 0; padding-left: 1.5rem;">
            <?php foreach ($validation->getErrors() as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="email-display">
        <i class="bi bi-envelope"></i> <?= esc($email ?? '') ?>
      </div>

      <form method="post" action="<?= base_url('reset-password') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">
        
        <div class="mb-3">
          <input type="password" class="form-control" id="password" name="password"
                 placeholder="New Password (minimum 8 characters)" required>
        </div>

        <div class="mb-3">
          <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                 placeholder="Confirm New Password" required>
        </div>

        <button type="submit" class="reset-btn">UPDATE PASSWORD</button>
      </form>

      <div class="back-section">
        <p class="back-text">
          Remember your password? 
          <a href="<?= base_url('login') ?>" class="back-link">Back to Login</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>