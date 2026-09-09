<?php /** @var array $teacher */ ?>

<div class="teacher-info-section">
  <div class="teacher-info-title"><i class="bi bi-card-heading"></i> Identification</div>
  <div class="teacher-info-grid">
    <div class="teacher-info-item">
      <div class="teacher-info-label">TIN</div>
      <?= teacher_detail_value($teacher['tin'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Category</div>
      <?= teacher_detail_value($teacher['personnel_category'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Employee No.</div>
      <?= teacher_detail_value($teacher['government_employee_no'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">System ID</div>
      <?= teacher_detail_value($teacher['employee_id'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">PhilSys Number</div>
      <?php
      $philsys = $teacher['philsys_number'] ?? null;
      $philsysDisplay = $philsys ? trim(chunk_split($philsys, 4, ' ')) : null;
      echo teacher_detail_value($philsysDisplay);
      ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">PRC License</div>
      <?= teacher_detail_value($teacher['license_number'] ?? null, 'N/A') ?>
    </div>
  </div>
</div>

<div class="teacher-info-section">
  <div class="teacher-info-title"><i class="bi bi-person"></i> Personal Information</div>
  <div class="teacher-info-grid">
    <div class="teacher-info-item">
      <div class="teacher-info-label">Full Name</div>
      <span class="teacher-info-value"><?= esc(trim(($teacher['first_name'] ?? '') . ' ' . ($teacher['middle_name'] ?? '') . ' ' . ($teacher['last_name'] ?? ''))) ?></span>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Sex</div>
      <?= teacher_detail_value($teacher['gender'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Civil Status</div>
      <?= teacher_detail_value($teacher['civil_status'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Date of Birth</div>
      <span class="teacher-info-value"><?= teacher_format_date_display($teacher['date_of_birth'] ?? null) ?></span>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Religion</div>
      <?= teacher_detail_value($teacher['religion'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Ethnic Group</div>
      <?= teacher_detail_value($teacher['ethnic_group'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Email</div>
      <?= teacher_detail_value($teacher['email'] ?? null, 'N/A') ?>
    </div>
  </div>
</div>

<div class="teacher-info-section">
  <div class="teacher-info-title"><i class="bi bi-briefcase"></i> Employment &amp; Appointment</div>
  <div class="teacher-info-grid">
    <div class="teacher-info-item">
      <div class="teacher-info-label">Fund Source</div>
      <?= teacher_detail_value($teacher['fund_source'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Position</div>
      <?= teacher_detail_value($teacher['position'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Designation</div>
      <?= teacher_detail_value($teacher['designation'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Nature of Appointment</div>
      <?= teacher_detail_value($teacher['nature_of_appointment'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Hiring Arrangement</div>
      <?= teacher_detail_value($teacher['hiring_arrangement'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Status (Item)</div>
      <?= teacher_detail_value($teacher['item_status'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Eligibility</div>
      <?= teacher_detail_value($teacher['eligibility'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Employment Status</div>
      <span class="status-badge status-<?= esc($teacher['employment_status'] ?? 'active') ?>">
        <?= ucfirst(str_replace('_', ' ', $teacher['employment_status'] ?? 'active')) ?>
      </span>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Teaching Area</div>
      <?= teacher_detail_value($teacher['department'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Date Hired</div>
      <span class="teacher-info-value"><?= teacher_format_date_display($teacher['date_hired'] ?? null) ?></span>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">First Day of Service</div>
      <span class="teacher-info-value"><?= teacher_format_date_display($teacher['date_first_service'] ?? null) ?></span>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">First Day (New Station)</div>
      <span class="teacher-info-value"><?= teacher_format_date_display($teacher['date_first_service_new_station'] ?? null) ?></span>
    </div>
  </div>
</div>

<div class="teacher-info-section">
  <div class="teacher-info-title"><i class="bi bi-mortarboard"></i> Education</div>
  <div class="teacher-info-grid">
    <div class="teacher-info-item">
      <div class="teacher-info-label">Baccalaureate</div>
      <?= teacher_detail_value($teacher['baccalaureate_degree'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">PRC Specialization</div>
      <?php
      $spec = $teacher['prc_specialization'] ?? '';
      if ($spec !== '' && ! empty($teacher['prc_major_units_percent'])) {
          $spec .= ' (' . rtrim(rtrim(number_format((float) $teacher['prc_major_units_percent'], 2), '0'), '.') . '%)';
      }
      echo teacher_detail_value($spec !== '' ? $spec : null);
      ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Minor</div>
      <?= teacher_detail_value($teacher['minor'] ?? null) ?>
    </div>
    <div class="teacher-info-item">
      <div class="teacher-info-label">Master's Degree</div>
      <?= teacher_detail_value($teacher['masters_degree'] ?? null) ?>
    </div>
  </div>
</div>

<div class="teacher-info-section">
  <div class="teacher-info-title"><i class="bi bi-telephone"></i> Contact Information</div>
  <div class="teacher-info-grid">
    <div class="teacher-info-item">
      <div class="teacher-info-label">Contact Number</div>
      <?= teacher_detail_value($teacher['contact_number'] ?? null, 'Not provided') ?>
    </div>
    <div class="teacher-info-item" style="grid-column: 1 / -1;">
      <div class="teacher-info-label">Address</div>
      <?= teacher_detail_value($teacher['address'] ?? null, 'Not provided') ?>
    </div>
  </div>
</div>
