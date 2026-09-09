<?php
/**
 * Shared teacher personnel form fields.
 *
 * @var array  $teacher   Existing teacher row (edit) or empty for create
 * @var string $mode      'create' or 'edit'
 * @var bool   $showAccount Whether to show email/password block (create page)
 */
$teacher = $teacher ?? [];
$mode = $mode ?? 'create';
$showAccount = $showAccount ?? ($mode === 'create');

$birth = teacher_date_parts_from_value(old('date_of_birth', $teacher['date_of_birth'] ?? null));
if (old('birth_month')) {
    $birth = ['month' => (int) old('birth_month'), 'day' => (int) old('birth_day'), 'year' => (int) old('birth_year')];
}
$service = teacher_date_parts_from_value(old('date_first_service', $teacher['date_first_service'] ?? null));
if (old('service_month')) {
    $service = ['month' => (int) old('service_month'), 'day' => (int) old('service_day'), 'year' => (int) old('service_year')];
}
$newStation = teacher_date_parts_from_value(old('date_first_service_new_station', $teacher['date_first_service_new_station'] ?? null));
if (old('new_station_month')) {
    $newStation = ['month' => (int) old('new_station_month'), 'day' => (int) old('new_station_day'), 'year' => (int) old('new_station_year')];
}

$val = static function (string $key, $default = '') use ($teacher) {
    $v = old($key, $teacher[$key] ?? $default);
    return $v === null ? '' : $v;
};
?>

<?php if ($showAccount): ?>
<div class="row mb-4">
  <div class="col-12">
    <h6 class="text-primary border-bottom pb-2 mb-3">Account Information</h6>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
      <input type="email" class="form-control" id="email" name="email" value="<?= esc($val('email')) ?>" required>
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="password" class="form-control" id="password" name="password" <?= $mode === 'create' ? 'required' : '' ?>>
        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
          <i class="bi bi-eye" id="toggleIcon"></i>
        </button>
      </div>
      <div class="form-text">Minimum 8 characters</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="license_number" class="form-label">PRC License Number</label>
      <input type="text" class="form-control teacher-prc-license" id="license_number" name="license_number"
             value="<?= esc($val('license_number')) ?>" placeholder="7 digits" maxlength="7" inputmode="numeric">
      <div class="form-text">Exactly 7 digits (optional if not yet available)</div>
    </div>
  </div>
</div>
<?php else: ?>
<div class="row mb-3">
  <div class="col-md-6">
    <div class="mb-3">
      <label for="license_number" class="form-label">PRC License Number</label>
      <input type="text" class="form-control teacher-prc-license" id="license_number" name="license_number"
             value="<?= esc($val('license_number')) ?>" maxlength="7" inputmode="numeric">
    </div>
  </div>
  <div class="col-md-6">
    <div class="mb-3">
      <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
      <input type="email" class="form-control" id="email" name="email" value="<?= esc($val('email')) ?>" required>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="row mb-4">
  <div class="col-12">
    <h6 class="text-primary border-bottom pb-2 mb-3">Identification</h6>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="tin" class="form-label">TIN</label>
      <input type="text" class="form-control teacher-tin" id="tin" name="tin"
             value="<?= esc($val('tin')) ?>" placeholder="000-000-000" maxlength="11" inputmode="numeric">
      <div class="form-text">Format: 999-999-999</div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="mb-3">
      <label for="personnel_category" class="form-label">Category (Cat)</label>
      <input type="text" class="form-control" id="personnel_category" name="personnel_category"
             value="<?= esc($val('personnel_category')) ?>" maxlength="10" inputmode="text" placeholder="e.g. Cat 1">
      <div class="form-text">Optional. Use the personnel category/code from the school sheet.</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="government_employee_no" class="form-label">Employee No.</label>
      <input type="text" class="form-control" id="government_employee_no" name="government_employee_no"
             value="<?= esc($val('government_employee_no')) ?>" maxlength="20" inputmode="numeric"
             placeholder="e.g. 22243">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="philsys_number" class="form-label">PhilSys Number</label>
      <input type="text" class="form-control teacher-philsys" id="philsys_number" name="philsys_number"
             value="<?= esc($val('philsys_number')) ?>" maxlength="12" inputmode="numeric"
             placeholder="12-digit PhilSys ID">
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <h6 class="text-primary border-bottom pb-2 mb-3">Personal Information</h6>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control teacher-name" id="first_name" name="first_name"
             value="<?= esc($val('first_name')) ?>" required>
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="middle_name" class="form-label">Middle Name</label>
      <input type="text" class="form-control teacher-name" id="middle_name" name="middle_name"
             value="<?= esc($val('middle_name')) ?>">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control teacher-name" id="last_name" name="last_name"
             value="<?= esc($val('last_name')) ?>" required>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="gender" class="form-label">Sex <span class="text-danger">*</span></label>
      <select class="form-select" id="gender" name="gender" required>
        <option value="">Select</option>
        <option value="Male" <?= $val('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $val('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="civil_status" class="form-label">Civil Status</label>
      <select class="form-select" id="civil_status" name="civil_status">
        <option value="">Select</option>
        <?php foreach (teacher_civil_status_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= $val('civil_status') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-2">
    <div class="mb-3">
      <label for="birth_month" class="form-label">Birth Month <span class="text-danger">*</span></label>
      <select class="form-select" id="birth_month" name="birth_month" required>
        <option value="">Month</option>
        <?php foreach (teacher_month_options() as $num => $label): ?>
          <option value="<?= $num ?>" <?= (int) ($birth['month'] ?? 0) === $num ? 'selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-2">
    <div class="mb-3">
      <label for="birth_day" class="form-label">Birth Day <span class="text-danger">*</span></label>
      <input type="number" class="form-control" id="birth_day" name="birth_day" min="1" max="31"
             value="<?= esc((string) ($birth['day'] ?? '')) ?>" required>
    </div>
  </div>
  <div class="col-md-2">
    <div class="mb-3">
      <label for="birth_year" class="form-label">Birth Year <span class="text-danger">*</span></label>
      <input type="number" class="form-control" id="birth_year" name="birth_year" min="1900" max="<?= date('Y') ?>"
             value="<?= esc((string) ($birth['year'] ?? '')) ?>" required>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="religion" class="form-label">Religion</label>
      <input type="text" class="form-control" id="religion" name="religion" value="<?= esc($val('religion')) ?>" maxlength="50">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="ethnic_group" class="form-label">Ethnic Group</label>
      <input type="text" class="form-control" id="ethnic_group" name="ethnic_group" value="<?= esc($val('ethnic_group')) ?>" maxlength="50">
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <h6 class="text-primary border-bottom pb-2 mb-3">Employment &amp; Appointment</h6>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="fund_source" class="form-label">Fund Source</label>
      <select class="form-select" id="fund_source" name="fund_source">
        <option value="">Select</option>
        <?php foreach (teacher_fund_source_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= $val('fund_source') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="position" class="form-label">Position</label>
      <select class="form-select" id="position" name="position">
        <option value="">Select Position</option>
        <?php foreach (teacher_position_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= $val('position') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="designation" class="form-label">Designation</label>
      <select class="form-select" id="designation" name="designation">
        <option value="">Select</option>
        <?php foreach (teacher_designation_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= $val('designation') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="nature_of_appointment" class="form-label">Nature of Appointment</label>
      <select class="form-select" id="nature_of_appointment" name="nature_of_appointment">
        <option value="">Select</option>
        <?php foreach (teacher_nature_of_appointment_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= $val('nature_of_appointment') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="hiring_arrangement" class="form-label">Hiring Arrangement</label>
      <select class="form-select" id="hiring_arrangement" name="hiring_arrangement">
        <option value="">Select</option>
        <?php foreach (teacher_hiring_arrangement_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= $val('hiring_arrangement') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="item_status" class="form-label">Status (Item)</label>
      <select class="form-select" id="item_status" name="item_status">
        <option value="">Select</option>
        <?php foreach (teacher_item_status_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= $val('item_status') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="employment_status" class="form-label">Employment Status <span class="text-danger">*</span></label>
      <select class="form-select" id="employment_status" name="employment_status" required>
        <option value="">Select</option>
        <option value="active" <?= $val('employment_status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $val('employment_status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        <option value="on_leave" <?= $val('employment_status') === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
        <option value="resigned" <?= $val('employment_status') === 'resigned' ? 'selected' : '' ?>>Resigned</option>
        <option value="terminated" <?= $val('employment_status') === 'terminated' ? 'selected' : '' ?>>Terminated</option>
      </select>
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="eligibility" class="form-label">Eligibility</label>
      <select class="form-select" id="eligibility" name="eligibility">
        <option value="">Select</option>
        <?php foreach (teacher_eligibility_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= $val('eligibility') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <h6 class="text-primary border-bottom pb-2 mb-3">Education</h6>
  </div>
  <div class="col-md-6">
    <div class="mb-3">
      <label for="baccalaureate_degree" class="form-label">Degree Finished / Baccalaureate</label>
      <input type="text" class="form-control" id="baccalaureate_degree" name="baccalaureate_degree"
             value="<?= esc($val('baccalaureate_degree')) ?>" maxlength="255"
             placeholder="e.g. Bachelor of Elementary Education">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="prc_specialization" class="form-label">PRC Specialization</label>
      <input type="text" class="form-control" id="prc_specialization" name="prc_specialization"
             value="<?= esc($val('prc_specialization')) ?>" maxlength="100" placeholder="e.g. Science">
    </div>
  </div>
  <div class="col-md-3">
    <div class="mb-3">
      <label for="prc_major_units_percent" class="form-label">PRC Major Units (%)</label>
      <input type="number" class="form-control" id="prc_major_units_percent" name="prc_major_units_percent"
             value="<?= esc((string) $val('prc_major_units_percent')) ?>" min="0" max="100" step="0.01"
             placeholder="e.g. 40">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="minor" class="form-label">Minor</label>
      <input type="text" class="form-control" id="minor" name="minor" value="<?= esc($val('minor')) ?>" maxlength="100">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="masters_degree" class="form-label">Post-Graduate / Master's Degree</label>
      <input type="text" class="form-control" id="masters_degree" name="masters_degree"
             value="<?= esc($val('masters_degree')) ?>" maxlength="255">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="subjects" class="form-label">Teaching Area / Subject</label>
      <select class="form-select" id="subjects" name="subjects">
        <option value="">Select Subject</option>
        <?php foreach (teacher_subject_options() as $opt): ?>
          <option value="<?= esc($opt) ?>" <?= old('subjects', $teacher['department'] ?? '') === $opt ? 'selected' : '' ?>><?= esc($opt) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <h6 class="text-primary border-bottom pb-2 mb-3">Service Dates</h6>
  </div>
  <div class="col-md-12"><p class="text-muted small mb-2">Date of First Day of Service</p></div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="service_month" class="form-label">Month</label>
      <select class="form-select" id="service_month" name="service_month">
        <option value="">Month</option>
        <?php foreach (teacher_month_options() as $num => $label): ?>
          <option value="<?= $num ?>" <?= (int) ($service['month'] ?? 0) === $num ? 'selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="service_day" class="form-label">Day</label>
      <input type="number" class="form-control" id="service_day" name="service_day" min="1" max="31"
             value="<?= esc((string) ($service['day'] ?? '')) ?>">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="service_year" class="form-label">Year</label>
      <input type="number" class="form-control" id="service_year" name="service_year" min="1900" max="<?= date('Y') ?>"
             value="<?= esc((string) ($service['year'] ?? '')) ?>">
    </div>
  </div>
  <div class="col-md-12"><p class="text-muted small mb-2">First Day of Service (New Station)</p></div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="new_station_month" class="form-label">Month</label>
      <select class="form-select" id="new_station_month" name="new_station_month">
        <option value="">Month</option>
        <?php foreach (teacher_month_options() as $num => $label): ?>
          <option value="<?= $num ?>" <?= (int) ($newStation['month'] ?? 0) === $num ? 'selected' : '' ?>><?= esc($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="new_station_day" class="form-label">Day</label>
      <input type="number" class="form-control" id="new_station_day" name="new_station_day" min="1" max="31"
             value="<?= esc((string) ($newStation['day'] ?? '')) ?>">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="new_station_year" class="form-label">Year</label>
      <input type="number" class="form-control" id="new_station_year" name="new_station_year" min="1900" max="<?= date('Y') ?>"
             value="<?= esc((string) ($newStation['year'] ?? '')) ?>">
    </div>
  </div>
  <div class="col-md-4">
    <div class="mb-3">
      <label for="date_hired" class="form-label">Date Hired (system)</label>
      <input type="date" class="form-control" id="date_hired" name="date_hired"
             value="<?= esc($val('date_hired')) ?>">
      <div class="form-text">Optional; defaults to first day of service if blank</div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <h6 class="text-primary border-bottom pb-2 mb-3">Contact Information</h6>
  </div>
  <div class="col-md-6">
    <div class="mb-3">
      <label for="contact_number" class="form-label">Contact Number</label>
      <input type="text" class="form-control teacher-phone" id="contact_number" name="contact_number"
             value="<?= esc($val('contact_number')) ?>" maxlength="20">
    </div>
  </div>
  <div class="col-md-12">
    <div class="mb-3">
      <label for="address" class="form-label">Address</label>
      <textarea class="form-control" id="address" name="address" rows="3"><?= esc($val('address')) ?></textarea>
    </div>
  </div>
</div>

<?= view('admin/partials/teacher_personnel_scripts') ?>
