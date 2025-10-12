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
  margin: -2rem -15px -2rem -15px;
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
</style>

<div class="forgot-container">
  <div class="forgot-card">
    <div class="forgot-header">
      <h1 class="forgot-title">Reset Password</h1>
      <p class="forgot-subtitle">Enter your PRC License Number (teachers) or LRN (students) to verify your identity.</p>
    </div>
    
    <div class="forgot-body">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
          <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
          <?= session()->getFlashdata('error') ?>
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

      <form method="post" action="<?= base_url('verify-identity') ?>">
        <?= csrf_field() ?>
        
        <div class="mb-3">
          <input type="text" class="form-control" id="identifier" name="identifier"
                 placeholder="Enter PRC License Number or LRN" value="<?= old('identifier') ?>" required>
        </div>

        <button type="submit" class="forgot-btn">VERIFY IDENTITY</button>
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
