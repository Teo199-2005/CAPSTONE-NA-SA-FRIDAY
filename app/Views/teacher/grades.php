<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Enter Grades</h1>
    <span class="badge bg-info">Term <?= (int) $currentTerm ?> - SY <?= get_current_school_year() ?></span>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Tabs Navigation -->
<?php if ($isAdvisory || !empty($subjectSections)): ?>
<ul class="nav nav-tabs mb-3" id="gradesTabs" role="tablist">
    <?php if ($isAdvisory): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="advisory-tab" data-bs-toggle="tab" data-bs-target="#advisory" type="button" role="tab">
            <i class="bi bi-star-fill"></i> Advisory Class
        </button>
    </li>
    <?php endif; ?>
    <?php foreach ($subjectSections as $index => $sectionData): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= !$isAdvisory && $index === 0 ? 'active' : '' ?>" id="section-<?= $sectionData['section']['id'] ?>-tab" data-bs-toggle="tab" data-bs-target="#section-<?= $sectionData['section']['id'] ?>" type="button" role="tab">
            <?= esc($sectionData['section']['section_name']) ?> - <?= esc(grade_level_label((int) $sectionData['section']['grade_level'])) ?>
        </button>
    </li>
    <?php endforeach; ?>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="gradesTabsContent">
    <!-- Advisory Class Tab -->
    <?php if ($isAdvisory): ?>
    <div class="tab-pane fade show active" id="advisory" role="tabpanel">
        <?= view('teacher/grades_table', [
            'students' => $students,
            'subjects' => $subjects,
            'studentGrades' => $studentGrades,
            'currentTerm' => $currentTerm,
            'gradingEnabled' => $gradingEnabled,
            'isAdvisory' => true,
            'sectionId' => $teacher['id'],
            'sectionGradingType' => $sectionGradingType ?? 'numerical',
            'gradingSymbols' => $gradingSymbols ?? []
        ]) ?>
    </div>
    <?php endif; ?>
    
    <!-- Subject Sections Tabs -->
    <?php foreach ($subjectSections as $index => $sectionData): ?>
    <div class="tab-pane fade <?= !$isAdvisory && $index === 0 ? 'show active' : '' ?>" id="section-<?= $sectionData['section']['id'] ?>" role="tabpanel">
        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle"></i> <strong>Note:</strong> You are teaching <strong><?= esc(implode(', ', array_column($sectionData['section']['subjects'], 'name'))) ?></strong> in this section.
        </div>
        <?= view('teacher/grades_table', [
            'students' => $sectionData['data']['students'],
            'subjects' => $sectionData['section']['subjects'],
            'studentGrades' => $sectionData['data']['studentGrades'],
            'currentTerm' => $currentTerm,
            'gradingEnabled' => $gradingEnabled,
            'isAdvisory' => false,
            'sectionId' => $sectionData['section']['id']
        ]) ?>
    </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-clipboard-data fs-1 text-muted mb-3"></i>
        <h5 class="text-muted">No Classes Assigned</h5>
        <p class="text-muted mb-0">You are not assigned as an adviser or subject teacher to any section.</p>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
