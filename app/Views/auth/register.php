<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
.register-container {
  min-height: calc(100vh - 200px);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
  position: relative;
  margin: 0 -15px;
}

.register-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%233b82f6" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 0.9;
}

.register-container::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url('<?= asset_url('LPHS2.png') ?>');
  background-repeat: repeat;
  background-size: 110px 110px;
  background-position: center center;
  opacity: 0.095;
  filter: grayscale(0.7) saturate(0.8);
  pointer-events: none;
}

.register-card {
  background: rgba(30, 64, 175, 0.95);
  backdrop-filter: blur(25px);
  border: 1px solid rgba(59, 130, 246, 0.3);
  border-radius: 20px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.2);
  width: 100%;
  max-width: 800px;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.register-header {
  text-align: center;
  padding: 3rem 2.5rem 2rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
  border-bottom: 1px solid rgba(59, 130, 246, 0.1);
}

.register-logo {
  width: 78px;
  height: 78px;
  object-fit: contain;
  border-radius: 50%;
  margin: 0 auto 1rem;
  display: block;
  background: rgba(255, 255, 255, 0.95);
  border: 2px solid rgba(255, 255, 255, 0.7);
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.35);
}

.register-title {
  font-size: 2rem;
  font-weight: 800;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 0.5rem;
  letter-spacing: -0.025em;
}

.register-subtitle {
  color: rgba(255, 255, 255, 0.9);
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0;
}

.register-form {
  padding: 1.5rem 2rem 2rem;
}

.form-control, .form-select {
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 0.5rem 0.75rem;
  font-size: 0.85rem;
  background: #f8fafc;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-weight: 500;
  color: #000000;
}

.form-control:focus, .form-select:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
  background: white;
}

.form-label {
  color: white;
  font-weight: 600;
  font-size: 0.8rem;
  margin-bottom: 0.25rem;
}

.section-title {
  font-size: 1rem;
  font-weight: 700;
  color: white;
  margin-bottom: 0.75rem;
  margin-top: 0;
  padding-bottom: 0.25rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.section-title::before {
  display: none;
}

.section-title:first-of-type {
  margin-top: 0;
}

.register-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
  border: none;
  border-radius: 10px;
  color: white;
  font-weight: 700;
  font-size: 0.9rem;
  letter-spacing: 0.025em;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.register-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 25px rgba(251, 191, 36, 0.4);
  background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%);
}

.btn-outline-secondary {
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 1rem 2rem;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.alert {
  border: none;
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

.alert-danger {
  background: #fef2f2;
  color: #dc2626;
  border-left: 4px solid #dc2626;
}

.form-text, .text-muted {
  color: rgba(255, 255, 255, 0.6) !important;
  font-size: 0.75rem;
}

.form-step {
  display: none;
}

.form-step.active {
  display: block;
}

.step-navigation {
  display: flex;
  justify-content: space-between;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.step-navigation .right-buttons {
  display: flex;
  gap: 10px;
}

.btn-step {
  padding: 0.6rem 1.2rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 8px;
  background: transparent;
  color: white;
  font-weight: 600;
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.btn-step:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.step-indicator {
  text-align: center;
  margin-bottom: 1.5rem;
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.85rem;
}

.custom-alert {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
  z-index: 1000;
  max-width: 400px;
  width: 90%;
}

.alert-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
}

.alert-title {
  color: #dc2626;
  font-weight: 700;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.alert-message {
  color: #374151;
  margin-bottom: 1.5rem;
  line-height: 1.5;
}

.alert-close {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  float: right;
}

@keyframes slideIn {
  from {
    transform: translateX(400px);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* ===== MOBILE RESPONSIVE STYLES ===== */

/* Large tablets */
@media (max-width: 991.98px) {
  .register-header {
    padding: 2.5rem 2rem 1.5rem;
  }
  
  .register-title {
    font-size: 1.75rem;
  }
  
  .register-logo {
    width: 70px;
    height: 70px;
  }
  
  .register-form {
    padding: 1.25rem 1.5rem 1.5rem;
  }
}

/* Medium devices (tablets, 768px and below) */
@media (max-width: 767.98px) {
  .register-container {
    padding: 1.5rem 0.75rem;
  }
  
  .register-card {
    border-radius: 16px;
    max-width: 100%;
  }
  
  .register-header {
    padding: 2rem 1.5rem 1.25rem;
  }
  
  .register-logo {
    width: 60px;
    height: 60px;
    margin-bottom: 0.75rem;
  }
  
  .register-title {
    font-size: 1.5rem;
  }
  
  .register-subtitle {
    font-size: 0.85rem;
  }
  
  .register-form {
    padding: 1rem 1.25rem 1.25rem;
  }
  
  .form-control, .form-select {
    font-size: 16px; /* Prevents zoom on iOS */
    padding: 0.5rem 0.625rem;
  }
  
  .form-label {
    font-size: 0.78rem;
  }
  
  .section-title {
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
  }
  
  .step-indicator {
    font-size: 0.8rem;
    margin-bottom: 1rem;
  }
  
  /* 2-column grid for tablets */
  .register-form .row > [class*="col-"] {
    margin-bottom: 0.5rem;
  }
  
  .register-form .row.g-2 {
    --bs-gutter-y: 0.5rem;
  }
  
  .register-form .row.g-3 {
    --bs-gutter-y: 0.5rem;
  }

  /* Password toggle buttons on register */
  .register-form .position-relative button {
    right: 4px !important;
    padding: 6px !important;
    min-width: 36px !important;
    min-height: 36px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
}

/* Small phones */
@media (max-width: 575.98px) {
  .register-container {
    padding: 1rem 0.5rem;
  }
  
  .register-header {
    padding: 1.5rem 1rem 1rem;
  }
  
  .register-logo {
    width: 52px;
    height: 52px;
    margin-bottom: 0.5rem;
  }
  
  .register-title {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
  }
  
  .register-subtitle {
    font-size: 0.75rem;
  }
  
  .register-form {
    padding: 0.75rem 0.75rem 1rem;
  }
  
  /* 2x2x1 grid: 2 columns per row, last row full width */
  .register-form .row > .col-md-3,
  .register-form .row > .col-md-4,
  .register-form .row > .col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding-left: 4px;
    padding-right: 4px;
  }
  
  /* Full width items on mobile */
  .register-form .row > .col-md-12 {
    flex: 0 0 100%;
    max-width: 100%;
    padding-left: 4px;
    padding-right: 4px;
  }
  
  /* Ensure Address textarea and other full-width elements span full width */
  .register-form .row > .col-md-12:not([class*="col-sm-"]) {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .form-control, .form-select {
    font-size: 16px; /* Prevents zoom on iOS */
    padding: 0.45rem 0.5rem;
    border-radius: 6px;
  }
  
  .form-label {
    font-size: 0.72rem;
    margin-bottom: 0.15rem;
  }
  
  .section-title {
    font-size: 0.85rem;
    margin-bottom: 0.35rem;
    padding-bottom: 0.15rem;
  }
  
  .step-indicator {
    font-size: 0.75rem;
    margin-bottom: 0.75rem;
  }
  
  .register-form .row {
    margin-left: -4px;
    margin-right: -4px;
  }
  
  /* Fix row gap */
  .register-form .row.g-2 {
    --bs-gutter-x: 8px;
    --bs-gutter-y: 6px;
  }
  
  .register-form .row.g-3 {
    --bs-gutter-x: 8px;
    --bs-gutter-y: 6px;
  }
  
  .step-navigation {
    margin-top: 1rem;
    padding-top: 0.75rem;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .step-navigation .right-buttons {
    width: 100%;
    justify-content: center;
  }
  
  .btn-step {
    padding: 0.5rem 0.875rem;
    font-size: 0.8rem;
  }
  
  .register-btn {
    padding: 0.625rem 1rem;
    font-size: 0.85rem;
    min-height: 42px;
    -webkit-tap-highlight-color: transparent;
  }
  
  .alert {
    padding: 0.625rem;
    margin-bottom: 0.75rem;
    font-size: 0.8rem;
    border-radius: 8px;
  }
  
  .alert ul {
    padding-left: 1.25rem;
    margin-bottom: 0;
  }
  
  .custom-alert {
    padding: 1.25rem;
    width: 88%;
  }
  
  .form-text, .text-muted {
    font-size: 0.7rem;
  }
  
  .step-navigation #prevBtn {
    width: 100%;
  }
}

/* Very small phones (< 360px) */
@media (max-width: 359px) {
  .register-header {
    padding: 1rem 0.75rem 0.75rem;
  }
  
  .register-logo {
    width: 44px;
    height: 44px;
  }
  
  .register-title {
    font-size: 1.1rem;
  }
  
  .register-form {
    padding: 0.5rem 0.5rem 0.75rem;
  }
  
  .register-form .row > .col-md-3,
  .register-form .row > .col-md-4,
  .register-form .row > .col-md-6 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .form-control, .form-select {
    padding: 0.375rem 0.5rem;
    font-size: 16px;
  }
  
  .form-label {
    font-size: 0.7rem;
  }
  
  .section-title {
    font-size: 0.8rem;
  }
}

/* Landscape mode on small phones */
@media (max-height: 500px) and (orientation: landscape) {
  .register-container {
    min-height: auto;
    padding: 0.75rem 0.5rem;
  }
  
  .register-header {
    padding: 1rem 1rem 0.75rem;
  }
  
  .register-logo {
    width: 40px;
    height: 40px;
    margin-bottom: 0.25rem;
  }
  
  .register-title {
    font-size: 1.1rem;
    margin-bottom: 0.15rem;
  }
  
  .register-subtitle {
    font-size: 0.7rem;
  }
  
  .register-form {
    padding: 0.5rem 0.75rem 0.75rem;
  }
  
  .form-control, .form-select {
    padding: 0.35rem 0.5rem;
  }
  
  .form-label {
    font-size: 0.7rem;
  }
}

/* Ensure password toggle buttons inside register are clickable */
.register-form .position-relative {
  position: relative !important;
}

.register-form .position-relative button {
  position: absolute !important;
  cursor: pointer !important;
  z-index: 5 !important;
}

/* Ensure password fields have padding for toggle icon */
.register-form input[type="password"],
.register-form input[id^="password"] {
  padding-right: 40px !important;
}
</style>

<div class="register-container">


  <div class="register-card">
    <div class="register-header">
      <img src="<?= asset_url('LPHS2.png') ?>" alt="Cauayan South Central School Logo" class="register-logo">
      <h1 class="register-title">Student Registration</h1>
      <p class="register-subtitle">Register for enrollment at Cauayan South Central School</p>
    </div>

    <div class="register-form">
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        
        <?php 
        $errorStep = session()->getFlashdata('error_step') ?? 1;
        ?>

        <form method="post" action="<?= base_url('register') ?>" id="registrationForm" novalidate>
          <?= csrf_field() ?>

          <div class="step-indicator">
            <span id="stepText">Step 1 of 4: Personal Information</span>
          </div>

          <!-- Step 1: Personal Information -->
          <div class="form-step active" id="step1">
            <h5 class="section-title">Personal Information</h5>
          <div class="row g-2">
            <div class="col-md-3 col-6">
              <label class="form-label">First Name *</label>
              <input type="text" class="form-control" name="first_name" value="<?= old('first_name') ?>" oninput="validateNameField(this)" required />
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Middle Name *</label>
              <input type="text" class="form-control" name="middle_name" value="<?= old('middle_name') ?>" oninput="validateNameField(this)" required />
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Last Name *</label>
              <input type="text" class="form-control" name="last_name" value="<?= old('last_name') ?>" oninput="validateNameField(this)" required />
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Suffix</label>
              <select class="form-select" name="suffix">
                <option value="">None</option>
                <option value="Jr." <?= old('suffix') === 'Jr.' ? 'selected' : '' ?>>Jr.</option>
                <option value="Sr." <?= old('suffix') === 'Sr.' ? 'selected' : '' ?>>Sr.</option>
                <option value="II" <?= old('suffix') === 'II' ? 'selected' : '' ?>>II</option>
                <option value="III" <?= old('suffix') === 'III' ? 'selected' : '' ?>>III</option>
                <option value="IV" <?= old('suffix') === 'IV' ? 'selected' : '' ?>>IV</option>
                <option value="V" <?= old('suffix') === 'V' ? 'selected' : '' ?>>V</option>
              </select>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Gender *</label>
              <select class="form-select" name="gender" required>
                <option value="">Select</option>
                <option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
              </select>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Date of Birth *</label>
              <input type="date" class="form-control" name="date_of_birth" value="<?= old('date_of_birth') ?>" required />
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Grade Level *</label>
              <select class="form-select" name="grade_level" id="grade_level" required>
                <option value="">Select</option>
                <?php foreach (grade_level_options() as $g): ?>
                  <option value="<?= $g ?>" <?= old('grade_level') === (string) $g ? 'selected' : '' ?>><?= esc(grade_level_label($g)) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">LRN *</label>
              <input type="text" class="form-control" name="lrn" value="<?= old('lrn') ?>" placeholder="e.g. 123456789012" maxlength="12" pattern="[0-9]{12}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12)" required />
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Student Type *</label>
              <select class="form-select" name="student_type" required>
                <option value="">Select</option>
                <option value="New Student" <?= old('student_type') === 'New Student' ? 'selected' : '' ?>>New Student</option>
                <option value="Transferee" <?= old('student_type') === 'Transferee' ? 'selected' : '' ?>>Transferee</option>
                <option value="Old Student" <?= old('student_type') === 'Old Student' ? 'selected' : '' ?>>Old Student</option>
              </select>
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label">Place of Birth *</label>
              <input type="text" class="form-control" name="place_of_birth" value="<?= old('place_of_birth') ?>" required />
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Nationality *</label>
              <select class="form-select" name="nationality" required>
                <option value="">Select nationality</option>
                <?php foreach (nationality_options() as $nationality): ?>
                  <option value="<?= esc($nationality) ?>" <?= old('nationality', 'Filipino') === $nationality ? 'selected' : '' ?>><?= esc($nationality) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Religion *</label>
              <input type="text" class="form-control" name="religion" value="<?= old('religion') ?>" required />
            </div>
          </div>

          </div>

          <!-- Step 2: Contact Information -->
          <div class="form-step" id="step2">
            <h5 class="section-title">Contact Information</h5>
          <div class="row g-3">
            <div class="col-md-6 col-12">
              <label class="form-label">Email Address *</label>
              <input type="email" class="form-control" name="email" value="<?= old('email') ?>" required />
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label">Contact Number *</label>
              <input type="text" class="form-control" name="contact_number" value="<?= old('contact_number') ?>" oninput="validatePhoneField(this)" required />
            </div>
            <div class="col-md-12 col-12">
              <label class="form-label">Address *</label>
              <textarea class="form-control" name="address" rows="2" required><?= old('address') ?></textarea>
            </div>
          </div>

          </div>

          <!-- Step 3: Emergency Contact -->
          <div class="form-step" id="step3">
            <h5 class="section-title">Emergency Contact</h5>
          <div class="row g-3">
            <div class="col-md-4 col-6">
              <label class="form-label">Emergency Contact Name *</label>
              <input type="text" class="form-control" name="emergency_contact_name" value="<?= old('emergency_contact_name') ?>" oninput="validateNameField(this)" required />
            </div>
            <div class="col-md-4 col-6">
              <label class="form-label">Emergency Contact Number *</label>
              <input type="text" class="form-control" name="emergency_contact_number" value="<?= old('emergency_contact_number') ?>" oninput="validatePhoneField(this)" required />
            </div>
            <div class="col-md-4 col-12">
              <label class="form-label">Relationship *</label>
              <select class="form-select" name="emergency_contact_relationship" required>
                <option value="">Select relationship</option>
                <?php foreach (emergency_contact_relationship_options() as $relationship): ?>
                  <option value="<?= esc($relationship) ?>" <?= old('emergency_contact_relationship') === $relationship ? 'selected' : '' ?>><?= esc($relationship) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          </div>

          <!-- Step 4: Account Information -->
          <div class="form-step" id="step4">
            <h5 class="section-title">Account Information</h5>
          <div class="row g-3">
            <div class="col-md-6 col-12">
              <label class="form-label">Password *</label>
              <div class="position-relative">
                <input type="password" class="form-control" name="password" id="password" required />
                <button type="button" class="btn btn-sm position-absolute" style="right: 8px; top: 50%; transform: translateY(-50%); border: none; background: none; color: #6c757d; padding: 8px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center; z-index: 10;" onclick="togglePassword('password')">
                  <i class="bi bi-eye" id="password-icon"></i>
                </button>
              </div>
              <div class="form-text" id="password-hint">Minimum 8 characters</div>
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label">Confirm Password *</label>
              <div class="position-relative">
                <input type="password" class="form-control" name="password_confirm" id="password_confirm" required />
                <button type="button" class="btn btn-sm position-absolute" style="right: 8px; top: 50%; transform: translateY(-50%); border: none; background: none; color: #6c757d; padding: 8px; min-width: 36px; min-height: 36px; display: flex; align-items: center; justify-content: center; z-index: 10;" onclick="togglePassword('password_confirm')">
                  <i class="bi bi-eye" id="password_confirm-icon"></i>
                </button>
              </div>
              <div class="form-text" id="password-match-hint"></div>
            </div>
          </div>

          </div>

          <div class="step-navigation">
            <button type="button" class="btn-step" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
            <i class="bi bi-arrow-left me-2"></i>Previous
          </button>
            <div class="right-buttons">
              <button type="button" class="btn-step" id="nextBtn" onclick="validateAndNext()">
                Next<i class="bi bi-arrow-right ms-2"></i>
              </button>
              <button class="register-btn" type="submit" id="submitBtn" style="display: none;">
                <i class="bi bi-check-circle me-2"></i>SUBMIT REGISTRATION
              </button>
            </div>
          </div>



          <div class="mt-3">
            <small class="text-muted">
              * Required fields. Your registration will be reviewed by school administrators before approval.
            </small>
          </div>
        </form>

        <script>
        let currentStep = <?= $errorStep ?>;
        const totalSteps = 4;
        const stepTitles = [
          'Personal Information',
          'Contact Information', 
          'Emergency Contact',
          'Account Information'
        ];

        function showStep(step) {
          document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
          document.getElementById('step' + step).classList.add('active');
          document.getElementById('stepText').textContent = `Step ${step} of ${totalSteps}: ${stepTitles[step-1]}`;
          
          document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
          document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-block';
          document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';
        }

        function changeStep(direction) {
          const newStep = currentStep + direction;
          if (newStep >= 1 && newStep <= totalSteps) {
            currentStep = newStep;
            showStep(currentStep);
          }
        }

        function showCustomAlert(message) {
          const overlay = document.createElement('div');
          overlay.className = 'alert-overlay';
          
          const alertBox = document.createElement('div');
          alertBox.className = 'custom-alert';
          alertBox.innerHTML = `
            <div class="alert-title"><i class="bi bi-exclamation-triangle"></i> Required Fields Missing</div>
            <div class="alert-message">${message}</div>
            <button class="alert-close" onclick="closeCustomAlert()">OK</button>
          `;
          
          document.body.appendChild(overlay);
          document.body.appendChild(alertBox);
        }
        
        function closeCustomAlert() {
          document.querySelector('.alert-overlay')?.remove();
          document.querySelector('.custom-alert')?.remove();
        }
        
        function showSubmittingNotification() {
          const notification = document.createElement('div');
          notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            z-index: 9999;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
          `;
          notification.innerHTML = `
            <i class="bi bi-hourglass-split" style="font-size: 1.2rem;"></i>
            <span>Submitting your registration...</span>
          `;
          document.body.appendChild(notification);
        }

        function getFieldLabel(field) {
          const wrapper = field.closest('[class*="col-"]');
          const label = wrapper ? wrapper.querySelector('label') : null;
          if (label) {
            return label.textContent.replace(/\s*\*$/, '').trim();
          }
          return field.name || 'This field';
        }

        function isFieldEmpty(field) {
          if (field.type === 'file') {
            return !field.files.length;
          }
          if (field.tagName === 'SELECT') {
            return !String(field.value).trim();
          }
          return !String(field.value).trim();
        }

        function validateStep(stepNumber) {
          const stepElement = document.getElementById('step' + stepNumber);
          const requiredFields = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
          const emptyFields = [];

          requiredFields.forEach(field => {
            if (isFieldEmpty(field)) {
              emptyFields.push(getFieldLabel(field));
            }
          });

          if (stepNumber === 2) {
            const emailField = stepElement.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
              const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              if (!emailRegex.test(emailField.value)) {
                return { valid: false, message: 'Please enter a valid email address.<br><br>Example: <strong>student@example.com</strong>' };
              }
            }
          }

          if (stepNumber === 1) {
            const lrnField = stepElement.querySelector('input[name="lrn"]');
            if (lrnField && lrnField.value.length !== 12) {
              return { valid: false, message: 'LRN must be exactly 12 digits.' };
            }
          }

          if (emptyFields.length > 0) {
            return {
              valid: false,
              message: 'Please fill in the following required fields:<br><br><strong>' + emptyFields.join('<br>') + '</strong>'
            };
          }

          return { valid: true };
        }

        function validateAllSteps() {
          for (let step = 1; step <= totalSteps; step++) {
            const result = validateStep(step);
            if (!result.valid) {
              currentStep = step;
              showStep(currentStep);
              showCustomAlert(result.message);
              return false;
            }
          }
          return true;
        }

        function validateAndNext() {
          const result = validateStep(currentStep);
          if (!result.valid) {
            showCustomAlert(result.message);
            return;
          }
          
          changeStep(1);
        }

        function togglePassword(fieldId) {
          const field = document.getElementById(fieldId);
          const icon = document.getElementById(fieldId + '-icon');
          
          if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'bi bi-eye-slash';
          } else {
            field.type = 'password';
            icon.className = 'bi bi-eye';
          }
        }

        function validateNameField(input) {
          // Remove any numbers and special characters, keep only letters, spaces, hyphens, and apostrophes
          input.value = input.value.replace(/[^a-zA-Z\s\-\']/g, '');
        }

        function validatePhoneField(input) {
          // Remove any non-numeric characters except spaces, hyphens, parentheses, and plus sign
          input.value = input.value.replace(/[^0-9\s\-\(\)\+]/g, '');
        }

        document.addEventListener('DOMContentLoaded', function() {
          showStep(currentStep);

          // Real-time password validation
          const passwordField = document.getElementById('password');
          const passwordConfirmField = document.getElementById('password_confirm');
          const passwordHint = document.getElementById('password-hint');
          const passwordMatchHint = document.getElementById('password-match-hint');
          
          function validatePassword() {
            const password = passwordField.value;
            if (password.length > 0 && password.length < 8) {
              passwordHint.style.color = '#dc2626';
              passwordHint.textContent = '❌ Password must be at least 8 characters';
              passwordField.style.borderColor = '#dc2626';
            } else if (password.length >= 8) {
              passwordHint.style.color = '#16a34a';
              passwordHint.textContent = '✓ Password length is valid';
              passwordField.style.borderColor = '#16a34a';
            } else {
              passwordHint.style.color = 'rgba(255, 255, 255, 0.6)';
              passwordHint.textContent = 'Minimum 8 characters';
              passwordField.style.borderColor = '#e2e8f0';
            }
            validatePasswordMatch();
          }
          
          function validatePasswordMatch() {
            const password = passwordField.value;
            const passwordConfirm = passwordConfirmField.value;
            
            if (passwordConfirm.length > 0) {
              if (password !== passwordConfirm) {
                passwordMatchHint.style.color = '#dc2626';
                passwordMatchHint.textContent = '❌ Passwords do not match';
                passwordConfirmField.style.borderColor = '#dc2626';
              } else {
                passwordMatchHint.style.color = '#16a34a';
                passwordMatchHint.textContent = '✓ Passwords match';
                passwordConfirmField.style.borderColor = '#16a34a';
              }
            } else {
              passwordMatchHint.textContent = '';
              passwordConfirmField.style.borderColor = '#e2e8f0';
            }
          }
          
          passwordField.addEventListener('input', validatePassword);
          passwordField.addEventListener('blur', validatePassword);
          passwordConfirmField.addEventListener('input', validatePasswordMatch);
          passwordConfirmField.addEventListener('blur', validatePasswordMatch);
          
          // Initialize form validation
          const form = document.getElementById('registrationForm');
          form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateAllSteps()) {
              return;
            }

            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;

            if (password !== passwordConfirm) {
              currentStep = 4;
              showStep(currentStep);
              showCustomAlert('Passwords do not match. Please make sure both password fields are identical.');
              return;
            }

            if (password.length < 8) {
              currentStep = 4;
              showStep(currentStep);
              showCustomAlert('Password must be at least 8 characters long.');
              return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> SUBMITTING...';
            showSubmittingNotification();

            HTMLFormElement.prototype.submit.call(form);
          });
        })


        </script>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>